<?php

use App\Models\Profile;
use Laravel\Fortify\Contracts\CreatesNewUsers;

it('creates a profile automatically when a new user registers', function () {
    $creator = app(CreatesNewUsers::class);

    $user = $creator->create([
        'first_name'            => 'Hugo',
        'last_name'             => 'Test',
        'email'                 => 'hugo@example.com',
        'password'              => 'password',
        'password_confirmation' => 'password',
    ]);

    expect(Profile::where('user_id', $user->id)->exists())->toBeTrue();
});
