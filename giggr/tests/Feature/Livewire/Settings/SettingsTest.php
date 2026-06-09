<?php

use App\Enums\ContactPreference;
use App\Events\ContactPreferenceUpdated;
use App\Models\City;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;

it('changes the email, unverifies the account and redirects to verification', function () {
    $user = User::factory()->withProfile()->create([
        'email' => 'old@example.com',
        'email_verified_at' => now(),
    ]);

    Livewire::actingAs($user)
        ->test('parts.settings.update-email')
        ->set('email', 'new@example.com')
        ->set('current_password', 'password')
        ->call('save')
        ->assertRedirect(route('verification.notice'));

    $fresh = $user->fresh();
    expect($fresh->email)->toBe('new@example.com')
        ->and($fresh->hasVerifiedEmail())->toBeFalse();
});

it('rejects the email change with a wrong current password', function () {
    $user = User::factory()->withProfile()->create(['email' => 'old@example.com']);

    Livewire::actingAs($user)
        ->test('parts.settings.update-email')
        ->set('email', 'new@example.com')
        ->set('current_password', 'wrong')
        ->call('save')
        ->assertHasErrors('current_password');

    expect($user->fresh()->email)->toBe('old@example.com');
});

it('changes the password with the correct current password', function () {
    $user = User::factory()->withProfile()->create();

    Livewire::actingAs($user)
        ->test('parts.settings.update-password')
        ->set('current_password', 'password')
        ->set('password', 'new-password-123')
        ->set('password_confirmation', 'new-password-123')
        ->call('save')
        ->assertHasNoErrors()
        ->assertSet('saved', true);

    expect(Hash::check('new-password-123', $user->fresh()->password))->toBeTrue();
});

it('rejects the password change with a wrong current password', function () {
    $user = User::factory()->withProfile()->create();

    Livewire::actingAs($user)
        ->test('parts.settings.update-password')
        ->set('current_password', 'wrong')
        ->set('password', 'new-password-123')
        ->set('password_confirmation', 'new-password-123')
        ->call('save')
        ->assertHasErrors('current_password');
});

it('saves the birth date', function () {
    $user = User::factory()->withProfile()->create();

    Livewire::actingAs($user)
        ->test('parts.settings.personal-info')
        ->set('birth_date', '1990-05-05')
        ->call('save')
        ->assertHasNoErrors()
        ->assertSet('saved', true);

    expect($user->fresh()->profile->birth_date->format('Y-m-d'))->toBe('1990-05-05');
});

it('saves the city', function () {
    $user = User::factory()->withProfile()->create();
    $city = City::factory()->create();

    Livewire::actingAs($user)
        ->test('parts.settings.personal-info')
        ->set('cityId', $city->id)
        ->call('save')
        ->assertHasNoErrors()
        ->assertSet('saved', true);

    expect($user->fresh()->profile->city_id)->toBe($city->id);
});

it('allows clearing the city', function () {
    $city = City::factory()->create();
    $user = User::factory()->withProfile()->create();
    $user->profile->update(['city_id' => $city->id]);

    Livewire::actingAs($user)
        ->test('parts.settings.personal-info')
        ->set('cityId', null)
        ->call('save')
        ->assertHasNoErrors();

    expect($user->fresh()->profile->city_id)->toBeNull();
});

it('rejects a city that does not exist when saving the profile', function () {
    $user = User::factory()->withProfile()->create();

    Livewire::actingAs($user)
        ->test('parts.settings.personal-info')
        ->set('cityId', 999999)
        ->call('save')
        ->assertHasErrors('cityId');
});

it('rejects a future birth date', function () {
    $user = User::factory()->withProfile()->create();

    Livewire::actingAs($user)
        ->test('parts.settings.personal-info')
        ->set('birth_date', now()->addDay()->format('Y-m-d'))
        ->call('save')
        ->assertHasErrors('birth_date');
});

it('deletes the account with the correct password and redirects home', function () {
    $user = User::factory()->withProfile()->create();

    Livewire::actingAs($user)
        ->test('parts.settings.delete-account')
        ->set('current_password', 'password')
        ->call('delete')
        ->assertHasNoErrors()
        ->assertRedirect(route('home'));

    expect(User::find($user->id))->toBeNull();
});

it('rejects account deletion with a wrong password', function () {
    $user = User::factory()->withProfile()->create();

    Livewire::actingAs($user)
        ->test('parts.settings.delete-account')
        ->set('current_password', 'wrong')
        ->call('delete')
        ->assertHasErrors('current_password');

    expect(User::find($user->id))->not->toBeNull();
});

it('cascades the account deletion to the profile', function () {
    $user = User::factory()->withProfile()->create();
    $profileId = $user->profile->id;

    Livewire::actingAs($user)
        ->test('parts.settings.delete-account')
        ->set('current_password', 'password')
        ->call('delete');

    expect(Profile::withTrashed()->find($profileId))->toBeNull();
});

it('updates the contact preference live', function () {
    $user = User::factory()->withProfile()->create();

    Livewire::actingAs($user)
        ->test('parts.settings.privacy')
        ->set('contactPreference', ContactPreference::Nobody->value)
        ->assertSet('saved', true);

    expect($user->fresh()->profile->contact_preference)->toBe(ContactPreference::Nobody);
});

it('broadcasts ContactPreferenceUpdated so open profile/listing pages refresh live', function () {
    Event::fake([ContactPreferenceUpdated::class]);
    $user = User::factory()->withProfile()->create();

    Livewire::actingAs($user)
        ->test('parts.settings.privacy')
        ->set('contactPreference', ContactPreference::Nobody->value);

    Event::assertDispatched(
        ContactPreferenceUpdated::class,
        fn (ContactPreferenceUpdated $e) => $e->profileId === $user->profile->id,
    );
});

it('renders every settings section', function () {
    $user = User::factory()->withProfile()->create(['email_verified_at' => now()]);

    $this->actingAs($user)
        ->get(route('settings.account'))
        ->assertOk()
        ->assertSee(__('settings.email_title'))
        ->assertSee(__('settings.password_title'))
        ->assertSee(__('settings.personal_title'))
        ->assertSee(__('settings.privacy_title'))
        ->assertSee(__('settings.blocked_users_title'));
});
