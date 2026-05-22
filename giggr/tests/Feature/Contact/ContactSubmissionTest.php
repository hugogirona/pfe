<?php

use App\Mail\ContactMessageReceived;
use Illuminate\Support\Facades\Mail;

function validContactPayload(array $overrides = []): array
{
    return array_merge([
        'first_name' => 'Alice',
        'last_name' => 'Martin',
        'email' => 'alice@example.com',
        'subject' => 'general',
        'message' => 'Bonjour, je voudrais en savoir plus sur la plateforme.',
        'rgpd' => '1',
    ], $overrides);
}

it('sends a ContactMessageReceived mail to the configured recipient on a valid submission', function () {
    Mail::fake();
    config(['mail.contact_recipient' => 'gironahugo@gmail.com']);

    $this->post('/contact', validContactPayload())
        ->assertSessionHasNoErrors()
        ->assertRedirect()
        ->assertSessionHas('contact_success', true);

    Mail::assertQueued(ContactMessageReceived::class, function (ContactMessageReceived $mail) {
        return $mail->hasTo('gironahugo@gmail.com')
            && $mail->hasReplyTo('alice@example.com')
            && $mail->firstName === 'Alice'
            && $mail->lastName === 'Martin'
            && $mail->subjectKey === 'general'
            && str_contains($mail->body, 'voudrais en savoir plus');
    });
});

it('reads the recipient address from config rather than hardcoding it', function () {
    Mail::fake();
    config(['mail.contact_recipient' => 'somebody-else@example.com']);

    $this->post('/contact', validContactPayload());

    Mail::assertQueued(
        ContactMessageReceived::class,
        fn (ContactMessageReceived $mail) => $mail->hasTo('somebody-else@example.com'),
    );
});

it('requires first_name, last_name, email, subject, message and rgpd', function () {
    Mail::fake();

    $this->post('/contact', [])
        ->assertSessionHasErrors(['first_name', 'last_name', 'email', 'subject', 'message', 'rgpd']);

    Mail::assertNothingSent();
});

it('rejects an invalid email format', function () {
    Mail::fake();

    $this->post('/contact', validContactPayload(['email' => 'not-an-email']))
        ->assertSessionHasErrors(['email']);

    Mail::assertNothingSent();
});

it('rejects a subject outside the allowed list', function () {
    Mail::fake();

    $this->post('/contact', validContactPayload(['subject' => 'spam-attempt']))
        ->assertSessionHasErrors(['subject']);

    Mail::assertNothingSent();
});

it('accepts every whitelisted subject', function (string $subject) {
    Mail::fake();

    $this->post('/contact', validContactPayload(['subject' => $subject]))
        ->assertSessionHasNoErrors();

    Mail::assertQueued(
        ContactMessageReceived::class,
        fn (ContactMessageReceived $mail) => $mail->subjectKey === $subject,
    );
})->with(['general', 'partnership', 'feature', 'bug', 'other']);

it('rejects a message that exceeds the cap', function () {
    Mail::fake();

    $this->post('/contact', validContactPayload(['message' => str_repeat('a', 2001)]))
        ->assertSessionHasErrors(['message']);

    Mail::assertNothingSent();
});

it('rejects a message that is too short', function () {
    Mail::fake();

    $this->post('/contact', validContactPayload(['message' => 'hi']))
        ->assertSessionHasErrors(['message']);

    Mail::assertNothingSent();
});

it('requires the rgpd checkbox to be accepted', function () {
    Mail::fake();

    $this->post('/contact', validContactPayload(['rgpd' => null]))
        ->assertSessionHasErrors(['rgpd']);

    Mail::assertNothingSent();
});

it('drops a submission with a filled honeypot field', function () {
    Mail::fake();

    $response = $this->post('/contact', validContactPayload([
        config('honeypot.name_field_name') => 'caught-you',
    ]));

    $response->assertStatus(200);
    Mail::assertNothingSent();
});

it('queues the ContactMessageReceived mail rather than sending it inline', function () {
    Mail::fake();

    $this->post('/contact', validContactPayload());

    Mail::assertQueued(ContactMessageReceived::class);
});
