<x-mail::message>
# {{ __('auth.verify_email_greeting', ['name' => $user->first_name]) }}

{{ __('auth.verify_email_intro') }}

<table border="0" cellpadding="0" cellspacing="0" align="center" style="margin: 28px auto;">
    <tr>
        <td align="center" style="background-color: #f9e5de; border-radius: 12px; padding: 20px 32px;">
            <div style="font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; font-size: 40px; font-weight: 700; letter-spacing: 10px; color: #f67649; line-height: 1;">
                {{ $code }}
            </div>
        </td>
    </tr>
</table>

{{ __('auth.verify_email_expiry') }}

{{ __('auth.verify_email_ignore') }}

{!! __('auth.verify_email_salutation_html') !!}
</x-mail::message>
