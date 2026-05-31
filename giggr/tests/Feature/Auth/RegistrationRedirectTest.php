<?php

use App\Models\User;

it('redirects an unverified new user to the verify-email page', function () {
    $response = $this->post('/register', [
        'first_name' => 'Hugo',
        'last_name' => 'Test',
        'email' => 'newuser@example.com',
        'password' => 'password123',
        'birth_date' => '1998-06-15',
    ]);

    $response->assertRedirect(route('verification.notice'));
    expect(User::where('email', 'newuser@example.com')->exists())->toBeTrue();
});
