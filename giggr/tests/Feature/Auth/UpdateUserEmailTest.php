<?php

use App\Actions\UpdateUserEmail;
use App\Models\User;
use Illuminate\Validation\ValidationException;

it('updates the email, unverifies the account and regenerates a verification code', function () {
    $user = User::factory()->withProfile()->create([
        'email' => 'old@example.com',
        'email_verified_at' => now(),
        'email_verification_code' => null,
    ]);
    $this->actingAs($user);

    app(UpdateUserEmail::class)->update($user, [
        'email' => 'new@example.com',
        'current_password' => 'password',
    ]);

    $fresh = $user->fresh();
    expect($fresh->email)->toBe('new@example.com')
        ->and($fresh->hasVerifiedEmail())->toBeFalse()
        ->and($fresh->email_verification_code)->toMatch('/^\d{6}$/')
        ->and($fresh->email_verification_code_expires_at)->not->toBeNull();
});

it('rejects the change when the current password is wrong', function () {
    $user = User::factory()->withProfile()->create(['email' => 'old@example.com']);
    $this->actingAs($user);

    expect(fn () => app(UpdateUserEmail::class)->update($user, [
        'email' => 'new@example.com',
        'current_password' => 'wrong-password',
    ]))->toThrow(ValidationException::class)
        ->and($user->fresh()->email)->toBe('old@example.com');
});

it('rejects an email already taken by another user', function () {
    User::factory()->create(['email' => 'taken@example.com']);
    $user = User::factory()->withProfile()->create(['email' => 'old@example.com']);
    $this->actingAs($user);

    expect(fn () => app(UpdateUserEmail::class)->update($user, [
        'email' => 'taken@example.com',
        'current_password' => 'password',
    ]))->toThrow(ValidationException::class);
});

it('rejects when the new email equals the current one', function () {
    $user = User::factory()->withProfile()->create(['email' => 'same@example.com']);
    $this->actingAs($user);

    expect(fn () => app(UpdateUserEmail::class)->update($user, [
        'email' => 'same@example.com',
        'current_password' => 'password',
    ]))->toThrow(ValidationException::class);
});
