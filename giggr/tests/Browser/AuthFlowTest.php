<?php

use App\Models\User;

it('shows the login form', function () {
    $page = visit(path('login'));

    $page->assertSee(__('auth.login_heading'))
        ->assertPresent('input[name="email"]')
        ->assertPresent('input[name="password"]')
        ->assertSee(__('auth.login_submit'));
});

it('shows an error when the credentials are invalid', function () {
    $page = visit(path('login'));

    $page->fill('email', 'inconnu@exemple.com')
        ->fill('password', 'mauvais-mot-de-passe')
        ->click('button[type="submit"]')
        ->assertSee(__('auth.failed'));
});

it('logs a user in with valid credentials', function () {
    User::factory()->create([
        'email' => 'marie@exemple.com',
    ]);

    $page = visit(path('login'));

    $page->fill('email', 'marie@exemple.com')
        ->fill('password', 'password')
        ->click('button[type="submit"]')
        ->assertPathContains('explorer');
});

it('shows the registration form with all its fields', function () {
    $page = visit(path('register'));

    $page->assertSee(__('auth.register_heading'))
        ->assertPresent('input[name="first_name"]')
        ->assertPresent('input[name="last_name"]')
        ->assertPresent('input[name="birth_date"]')
        ->assertPresent('input[name="email"]')
        ->assertPresent('input[name="password"]')
        ->assertSee(__('auth.register_submit'));
});
