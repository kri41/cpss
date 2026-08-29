<?php

namespace App\Notifications;

use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Email "selamat datang / akun aktif" setelah verifikasi berhasil.
 * Isi bisa diubah lewat menu Pengaturan. Placeholder: {nama}, {app}
 */
class AccountVerifiedNotification extends Notification
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $loginUrl = route('login');

        $repl = fn (string $key) => strtr(
            Setting::get($key, Setting::DEFAULTS[$key] ?? ''),
            [
                '{nama}' => $notifiable->name,
                '{app}' => config('app.name'),
            ]
        );

        return (new MailMessage)
            ->subject($repl('welcome_email_subject'))
            ->greeting($repl('welcome_email_greeting'))
            ->line($repl('welcome_email_body'))
            ->action(Setting::get('welcome_email_button', Setting::DEFAULTS['welcome_email_button']), $loginUrl);
    }
}
