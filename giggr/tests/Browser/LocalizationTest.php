<?php

it("bascule l'interface du français vers l'anglais via le sélecteur de langue", function () {
    $page = visit('/');

    $page->click('a[hreflang="en"]')
        ->assertPathBeginsWith('/en');
});
