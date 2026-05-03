<?php

it('redirects to profile setup after registration', function () {
    $this->post('/register', [
        'first_name' => 'Hugo',
        'last_name' => 'Test',
        'email' => 'newuser@example.com',
        'password' => 'password123',
    ])->assertRedirectToRoute('profile.setup');
});
