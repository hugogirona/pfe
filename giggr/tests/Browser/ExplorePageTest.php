<?php

it("bascule entre les onglets Profils et Annonces via l'URL", function () {
    $page = visit(path('explore'));

    $page->assertSee(__('explore.tab_profiles'))
        ->assertSee(__('explore.tab_announcements'))
        ->click('[role="tab"][aria-selected="false"]')
        ->assertPathContains(__('explore.tab_announcements_slug'));
});

it('ouvre le tiroir de filtres au clic', function () {
    $page = visit(path('explore'))->on()->mobile();

    $page->click('button[aria-haspopup="dialog"]')
        ->assertSee(__('explore.filter_apply'))
        ->assertSee(__('explore.filter_clear'));
});
