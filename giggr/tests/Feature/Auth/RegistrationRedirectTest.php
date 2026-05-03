<?php

use App\Models\User;

it('redirects to own profile after registration', function () {
    $response = $this->post('/register', [
        'first_name' => 'Hugo',
        'last_name' => 'Test',
        'email' => 'newuser@example.com',
        'password' => 'password123',
    ]);

    $user = User::where('email', 'newuser@example.com')->first();

    $response->assertRedirect(route('profile', ['id' => $user->profile->id]));
});
