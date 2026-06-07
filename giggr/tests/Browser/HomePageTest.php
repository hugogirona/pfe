<?php

it('affiche le hero et ne déclenche aucune erreur JavaScript', function () {
    $page = visit('/');

    $page->assertSee(__('home.welcome'))
        ->assertSee(__('home.hero_title_share'))
        ->assertSee(__('home.hero_subtitle'))
        ->assertNoJavaScriptErrors();
});

it("ne présente aucun problème d'accessibilité sérieux sur l'accueil", function () {
    $page = visit('/');

    $page->assertNoAccessibilityIssues();
});
