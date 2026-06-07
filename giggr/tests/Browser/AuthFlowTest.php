<?php

use App\Models\User;

it('affiche le formulaire de connexion', function () {
    $page = visit(path('login'));

    $page->assertSee(__('auth.login_heading'))
        ->assertPresent('input[name="email"]')
        ->assertPresent('input[name="password"]')
        ->assertSee(__('auth.login_submit'));
});

it('affiche une erreur quand les identifiants sont invalides', function () {
    $page = visit(path('login'));

    $page->fill('email', 'inconnu@exemple.com')
        ->fill('password', 'mauvais-mot-de-passe')
        ->click('button[type="submit"]')
        ->assertSee(__('auth.failed'));
});

it('connecte un utilisateur avec des identifiants valides', function () {
    User::factory()->create([
        'email' => 'marie@exemple.com',
    ]);

    $page = visit(path('login'));

    $page->fill('email', 'marie@exemple.com')
        ->fill('password', 'password')
        ->click('button[type="submit"]')
        ->assertPathContains('explorer');
});

it("affiche le formulaire d'inscription avec tous ses champs", function () {
    $page = visit(path('register'));

    $page->assertSee(__('auth.register_heading'))
        ->assertPresent('input[name="first_name"]')
        ->assertPresent('input[name="last_name"]')
        ->assertPresent('input[name="birth_date"]')
        ->assertPresent('input[name="email"]')
        ->assertPresent('input[name="password"]')
        ->assertSee(__('auth.register_submit'));
});
