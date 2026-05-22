<x-mail::message>
# {{ __('contact.mail_heading') }}

{{ __('contact.mail_intro', ['subject' => $subjectLabel]) }}

**{{ __('contact.mail_from') }}**
{{ $firstName }} {{ $lastName }} — {{ $email }}

**{{ __('contact.mail_body_label') }}**

> {!! nl2br(e($body)) !!}

<x-mail::button :url="'mailto:' . $email" color="primary">
{{ __('contact.mail_reply_cta') }}
</x-mail::button>

{!! __('auth.verify_email_salutation_html') !!}
</x-mail::message>
