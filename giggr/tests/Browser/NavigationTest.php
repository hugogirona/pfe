<?php

it('lets a visitor reach the login page from the header', function () {
    $page = visit('/');

    $page->assertSee(__('nav.sign_in'))
        ->click(__('nav.sign_in'))
        ->assertPathIs(path('login'))
        ->assertSee(__('auth.login_heading'));
});

it('redirects a visitor to login on a protected route', function () {
    $page = visit(path('settings.account'));

    $page->assertPathIs(path('login'));
});

it('opens the mobile menu and reveals the navigation links', function () {
    $page = visit('/')->on()->mobile();

    $page->click('[aria-label="'.__('nav.aria_menu').'"]')
        ->assertSee(__('nav.explore'))
        ->assertSee(__('nav.contact'));
});
