<?php

use App\Models\User;

it('register page renders the honeypot fields', function () {
    $name = config('honeypot.name_field_name');
    $validFrom = config('honeypot.valid_from_field_name');

    $this->get(route('register'))
        ->assertOk()
        ->assertSee('name="'.$name.'"', false)
        ->assertSee('name="'.$validFrom.'"', false);
});

it('contact page renders the honeypot fields', function () {
    $name = config('honeypot.name_field_name');
    $validFrom = config('honeypot.valid_from_field_name');

    $this->get(route('contact'))
        ->assertOk()
        ->assertSee('name="'.$name.'"', false)
        ->assertSee('name="'.$validFrom.'"', false);
});

it('rejects a registration where the honeypot field is filled', function () {
    $response = $this->post('/register', [
        'first_name' => 'Bot',
        'last_name' => 'Spammer',
        'email' => 'bot@example.com',
        'password' => 'password123',
        config('honeypot.name_field_name') => 'caught-you',
    ]);

    expect(User::where('email', 'bot@example.com')->exists())->toBeFalse();
    $response->assertStatus(200); // BlankPageResponder, a page with empty body (juste a white screen)
});
