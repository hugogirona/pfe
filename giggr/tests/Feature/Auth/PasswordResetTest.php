<?php

use App\Models\User;
use App\Notifications\PasswordResetLink;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;

it('sends the PasswordResetLink notification when a forgot-password request is made for an existing user', function () {
    Notification::fake();
    $user = User::factory()->create(['email' => 'hugo@example.com']);

    $this->post('/forgot-password', ['email' => 'hugo@example.com'])
        ->assertSessionHasNoErrors();

    Notification::assertSentTo($user, PasswordResetLink::class, function (PasswordResetLink $n) {
        return is_string($n->token) && $n->token !== '';
    });
});

it('does not leak whether an email exists', function () {
    Notification::fake();

    $existing = $this->post('/forgot-password', ['email' => 'unknown@example.com']);
    $existing->assertSessionHasErrors();

    // No notification is sent for non-existing users
    Notification::assertNothingSent();
});

it('resets the user password when a valid token is submitted', function () {
    $user = User::factory()->create(['email' => 'hugo@example.com']);
    $token = Password::createToken($user);

    $this->post('/reset-password', [
        'token' => $token,
        'email' => 'hugo@example.com',
        'password' => 'newpassword123',
        'password_confirmation' => 'newpassword123',
    ])->assertSessionHasNoErrors();

    expect(Hash::check('newpassword123', $user->fresh()->password))->toBeTrue();
});

it('rejects a reset with an invalid token', function () {
    User::factory()->create(['email' => 'hugo@example.com']);

    $this->post('/reset-password', [
        'token' => 'definitely-not-a-real-token',
        'email' => 'hugo@example.com',
        'password' => 'newpassword123',
        'password_confirmation' => 'newpassword123',
    ])->assertSessionHasErrors();
});

it('forgot-password form renders the honeypot fields', function () {
    $name = config('honeypot.name_field_name');
    $validFrom = config('honeypot.valid_from_field_name');

    $this->get(route('password.request'))
        ->assertOk()
        ->assertSee('name="'.$name.'"', false)
        ->assertSee('name="'.$validFrom.'"', false);
});

it('reset-password form renders the honeypot fields', function () {
    $name = config('honeypot.name_field_name');
    $validFrom = config('honeypot.valid_from_field_name');

    $this->get(route('password.reset', ['token' => 'some-token']))
        ->assertOk()
        ->assertSee('name="'.$name.'"', false)
        ->assertSee('name="'.$validFrom.'"', false);
});

it('rejects a forgot-password submission with a filled honeypot', function () {
    Notification::fake();
    User::factory()->create(['email' => 'hugo@example.com']);

    $this->post('/forgot-password', [
        'email' => 'hugo@example.com',
        config('honeypot.name_field_name') => 'gotcha-bot',
    ]);

    Notification::assertNothingSent();
});
