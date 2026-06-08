<?php

it('renders the hero without triggering any JavaScript errors', function () {
    $page = visit('/');

    $page->assertSee(__('home.welcome'))
        ->assertSee(__('home.hero_title_share'))
        ->assertSee(__('home.hero_subtitle'))
        ->assertNoJavaScriptErrors();
});

it('has no serious accessibility issues on the home page', function () {
    $page = visit('/');

    $page->assertNoAccessibilityIssues();
});
