<?php

it('switches the interface from French to English via the language selector', function () {
    $page = visit('/');

    $page->click('header a[hreflang="en"]')
        ->assertPathBeginsWith('/en');
});
