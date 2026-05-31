<?php

it('shows the privacy policy page to guests', function () {
    $this->get(route('privacy'))
        ->assertOk()
        ->assertSee(__('privacy.title'))
        ->assertSee(__('privacy.data_heading'))
        ->assertSee(__('privacy.rights_heading'));
});

it('links the privacy policy from the footer', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertSee(route('privacy'));
});
