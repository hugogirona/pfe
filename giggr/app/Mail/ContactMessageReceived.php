<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactMessageReceived extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string $firstName,
        public readonly string $lastName,
        public readonly string $email,
        public readonly string $subjectKey,
        public readonly string $body,
    ) {}

    public function envelope(): Envelope
    {
        $subjectLabel = __('contact.subject_'.$this->subjectKey);

        return new Envelope(
            subject: __('contact.mail_subject', ['subject' => $subjectLabel]),
            replyTo: [new Address($this->email, $this->firstName.' '.$this->lastName)],
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.contact-message',
            with: [
                'firstName' => $this->firstName,
                'lastName' => $this->lastName,
                'email' => $this->email,
                'subjectLabel' => __('contact.subject_'.$this->subjectKey),
                'body' => $this->body,
            ],
        );
    }
}
