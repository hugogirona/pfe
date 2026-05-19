<?php

use App\Actions\Fortify\CreateNewUser;
use App\Models\Profile;
use App\Models\User;
use App\Notifications\WelcomeWithVerificationCode;
use Database\Seeders\CitySeeder;
use Database\Seeders\GenreSeeder;
use Database\Seeders\InstrumentSeeder;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;

it('generates a 6-digit code and stores its expiry on registration', function () {
    $user = app(CreateNewUser::class)->create([
        'first_name' => 'Hugo',
        'last_name' => 'Test',
        'email' => 'hugo@example.com',
        'password' => 'password123',
    ]);

    expect($user->email_verification_code)->toMatch('/^\d{6}$/')
        ->and($user->email_verification_code_expires_at)->not->toBeNull()
        ->and($user->email_verification_code_expires_at->isAfter(now()))->toBeTrue()
        ->and($user->email_verification_code_expires_at->isBefore(now()->addMinutes(11)))->toBeTrue();
});

it('sends the WelcomeWithVerificationCode notification on full registration HTTP flow', function () {
    Notification::fake();

    $this->post('/register', [
        'first_name' => 'Hugo',
        'last_name' => 'Test',
        'email' => 'hugo@example.com',
        'password' => 'password123',
    ]);

    $user = User::where('email', 'hugo@example.com')->firstOrFail();
    Notification::assertSentTo($user, WelcomeWithVerificationCode::class, function (WelcomeWithVerificationCode $n) use ($user) {
        return $n->code === $user->email_verification_code;
    });
});

it('leaves the user unverified on creation', function () {
    $user = app(CreateNewUser::class)->create([
        'first_name' => 'Hugo',
        'last_name' => 'Test',
        'email' => 'hugo@example.com',
        'password' => 'password123',
    ]);

    expect($user->email_verified_at)->toBeNull()
        ->and($user->hasVerifiedEmail())->toBeFalse();
});

it('verifies the user when a matching code is submitted', function () {
    $user = User::factory()->create([
        'email_verified_at' => null,
        'email_verification_code' => '123456',
        'email_verification_code_expires_at' => now()->addMinutes(5),
    ]);

    Livewire::actingAs($user)
        ->test('pages::auth.verify-email')
        ->set('code', '123456')
        ->call('verify');

    $fresh = $user->fresh();
    expect($fresh->hasVerifiedEmail())->toBeTrue()
        ->and($fresh->email_verification_code)->toBeNull()
        ->and($fresh->email_verification_code_expires_at)->toBeNull();
});

it('rejects an expired code', function () {
    $user = User::factory()->create([
        'email_verified_at' => null,
        'email_verification_code' => '123456',
        'email_verification_code_expires_at' => now()->subMinute(),
    ]);

    Livewire::actingAs($user)
        ->test('pages::auth.verify-email')
        ->set('code', '123456')
        ->call('verify')
        ->assertSet('error', __('auth.verify_email_invalid'));

    expect($user->fresh()->hasVerifiedEmail())->toBeFalse();
});

it('rejects a wrong code', function () {
    $user = User::factory()->create([
        'email_verified_at' => null,
        'email_verification_code' => '123456',
        'email_verification_code_expires_at' => now()->addMinutes(5),
    ]);

    Livewire::actingAs($user)
        ->test('pages::auth.verify-email')
        ->set('code', '000000')
        ->call('verify')
        ->assertSet('error', __('auth.verify_email_invalid'));

    expect($user->fresh()->hasVerifiedEmail())->toBeFalse();
});

it('redirects already-verified users away from the verify page', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);

    Livewire::actingAs($user)
        ->test('pages::auth.verify-email')
        ->assertRedirect(config('fortify.home'));
});

it('resend generates a new code and notifies the user', function () {
    Notification::fake();
    $user = User::factory()->create([
        'email_verified_at' => null,
        'email_verification_code' => '111111',
        'email_verification_code_expires_at' => now()->addMinutes(5),
    ]);

    Livewire::actingAs($user)
        ->test('pages::auth.verify-email')
        ->call('resend');

    $fresh = $user->fresh();
    expect($fresh->email_verification_code)
        ->not->toBe('111111')
        ->toMatch('/^\d{6}$/');
    Notification::assertSentTo($fresh, WelcomeWithVerificationCode::class);
});

it('resend is rate-limited to once per minute', function () {
    $user = User::factory()->create([
        'email_verified_at' => null,
        'email_verification_code' => '111111',
        'email_verification_code_expires_at' => now()->addMinutes(5),
    ]);

    Livewire::actingAs($user)
        ->test('pages::auth.verify-email')
        ->call('resend')
        ->call('resend')
        ->assertSet('error', fn ($v) => str_contains($v ?? '', 'avant') || str_contains($v ?? '', 'before'));
});

it('dispatches the Verified event after a successful verification', function () {
    Event::fake([Verified::class]);
    $user = User::factory()->create([
        'email_verified_at' => null,
        'email_verification_code' => '123456',
        'email_verification_code_expires_at' => now()->addMinutes(5),
    ]);

    Livewire::actingAs($user)
        ->test('pages::auth.verify-email')
        ->set('code', '123456')
        ->call('verify');

    Event::assertDispatched(Verified::class);
});

it('rate-limits verify attempts after 5 failed tries', function () {
    $user = User::factory()->create([
        'email_verified_at' => null,
        'email_verification_code' => '123456',
        'email_verification_code_expires_at' => now()->addMinutes(5),
    ]);

    $component = Livewire::actingAs($user)
        ->test('pages::auth.verify-email');

    foreach (range(1, 5) as $_) {
        $component->set('code', '000000')->call('verify');
    }

    $component->set('code', '000000')
        ->call('verify')
        ->assertSet('error', fn ($v) => str_contains($v ?? '', 'tentatives') || str_contains($v ?? '', 'attempts'));
});

it('redirects an unverified user away from a verified-only route', function () {
    $owner = User::factory()->create(['email_verified_at' => null]);
    $profile = Profile::factory()->create(['user_id' => $owner->id]);

    $this->actingAs($owner)
        ->get(route('profile', ['id' => $profile->id]))
        ->assertRedirect(route('verification.notice'));
});

it('lets a verified user access a verified-only route', function () {
    $this->seed([CitySeeder::class, InstrumentSeeder::class, GenreSeeder::class]);
    $owner = User::factory()->create(['email_verified_at' => now()]);
    $profile = Profile::factory()->create(['user_id' => $owner->id]);

    $this->actingAs($owner)
        ->get(route('profile', ['id' => $profile->id]))
        ->assertOk();
});
