<x-mail::message>
# {{ __('auth.password_reset_greeting', ['name' => $user->first_name]) }}

{{ __('auth.password_reset_intro') }}

<x-mail::button :url="$url" color="primary">
{{ __('auth.password_reset_button') }}
</x-mail::button>

{{ __('auth.password_reset_expiry', ['minutes' => $expireMinutes]) }}

{{ __('auth.password_reset_ignore') }}

{!! __('auth.verify_email_salutation_html') !!}
</x-mail::message>
