<?php

it("permet à un visiteur d'atteindre la page de connexion depuis l'en-tête", function () {
    $page = visit('/');

    $page->assertSee(__('nav.sign_in'))
        ->click(__('nav.sign_in'))
        ->assertPathIs(path('login'))
        ->assertSee(__('auth.login_heading'));
});

it('redirige un visiteur vers la connexion sur une route protégée', function () {
    $page = visit(path('settings.account'));

    $page->assertPathIs(path('login'));
});

it('ouvre le menu mobile et révèle les liens de navigation', function () {
    $page = visit('/')->on()->mobile();

    $page->click('[aria-label="'.__('nav.aria_menu').'"]')
        ->assertSee(__('nav.explore'))
        ->assertSee(__('nav.contact'));
});
