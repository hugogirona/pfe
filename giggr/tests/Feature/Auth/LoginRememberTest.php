<?php

use App\Models\User;
use Livewire\Livewire;

it('renders a remember-me checkbox on the login form', function () {
    Livewire::test('pages::auth.login')
        ->assertSeeHtml('name="remember"');
});

it('sets the remember-me cookie when the box is checked', function () {
    $user = User::factory()->withProfile()->create();

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
        'remember' => 'on',
    ]);

    $this->assertAuthenticatedAs($user);
    $response->assertCookie(auth()->guard()->getRecallerName());
});

it('does not set the remember-me cookie when the box is unchecked', function () {
    $user = User::factory()->withProfile()->create();

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticatedAs($user);
    $response->assertCookieMissing(auth()->guard()->getRecallerName());
});
