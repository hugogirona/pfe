<?php

it('switches the interface locale via the language selector', function () {
    visit('/')
        ->click('header a[hreflang="nl"]')
        ->assertPathBeginsWith('/nl');
});
