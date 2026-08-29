<?php

namespace App\Notifications;

use App\Models\Setting;
use Illuminate\Auth\Notifications\VerifyEmail as BaseVerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

/**
 * Email verifikasi dengan isi yang bisa diubah lewat menu Pengaturan.
 * Placeholder: {nama}, {app}, {url}
 */
class VerifyEmailNotification extends BaseVerifyEmail
{
    public function toMail($notifiable): MailMessage
    {
        $url = $this->verificationUrl($notifiable);

        $repl = fn (string $key) => strtr(
            Setting::get($key, Setting::DEFAULTS[$key] ?? ''),
            [
                '{nama}' => $notifiable->name,
                '{app}' => config('app.name'),
                '{url}' => $url,
            ]
        );

        return (new MailMessage)
            ->subject($repl('verify_email_subject'))
            ->greeting($repl('verify_email_greeting'))
            ->line($repl('verify_email_body'))
            ->action(Setting::get('verify_email_button', Setting::DEFAULTS['verify_email_button']), $url)
            ->line($repl('verify_email_outro'));
    }
}
