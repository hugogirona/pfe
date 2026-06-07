<?php

it('déploie une réponse de la FAQ au clic (accordéon Alpine)', function () {
    $page = visit(path('contact'));

    $page->assertSee(__('contact.faq_q2'))
        ->click(__('contact.faq_q2'))
        ->assertSee(__('contact.faq_a2'));
});

it('affiche le formulaire de contact avec ses champs', function () {
    $page = visit(path('contact'));

    $page->assertSee(__('contact.form_title'))
        ->assertPresent('input[name="first_name"]')
        ->assertPresent('input[name="email"]')
        ->assertPresent('select[name="subject"]')
        ->assertPresent('textarea[name="message"]')
        ->assertSee(__('contact.form_submit'));
});
