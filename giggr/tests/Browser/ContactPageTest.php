<?php

it('expands a FAQ answer on click (Alpine accordion)', function () {
    $page = visit(path('contact'));

    $page->assertSee(__('contact.faq_q2'))
        ->click(__('contact.faq_q2'))
        ->assertSee(__('contact.faq_a2'));
});

it('shows the contact form with its fields', function () {
    $page = visit(path('contact'));

    $page->assertSee(__('contact.form_title'))
        ->assertPresent('input[name="first_name"]')
        ->assertPresent('input[name="email"]')
        ->assertPresent('select[name="subject"]')
        ->assertPresent('textarea[name="message"]')
        ->assertSee(__('contact.form_submit'));
});
