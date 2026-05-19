<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordResetLink extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public string $token) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = route('password.reset', $this->token).'?email='.urlencode($notifiable->getEmailForPasswordReset());

        $expireMinutes = (int) config(
            'auth.passwords.'.config('auth.defaults.passwords').'.expire',
        );

        return (new MailMessage)
            ->subject(__('auth.password_reset_subject'))
            ->markdown('emails.password-reset', [
                'user' => $notifiable,
                'url' => $url,
                'expireMinutes' => $expireMinutes,
            ]);
    }
}
