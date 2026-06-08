<?php

it('renders the privacy policy page with its sections', function () {
    $page = visit(path('privacy'));

    $page->assertSee(__('privacy.title'))
        ->assertSee(__('privacy.subtitle'))
        ->assertSee(__('privacy.intro_heading'))
        ->assertSee(__('privacy.data_heading'))
        ->assertSee(__('privacy.rights_heading'))
        ->assertSee(__('privacy.contact_heading'))
        ->assertSeeLink(__('privacy.contact_cta'))
        ->assertNoJavaScriptErrors();
});

