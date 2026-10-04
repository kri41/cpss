<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as BaseResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

/**
 * Email atur ulang kata sandi — isinya Bahasa Indonesia & memakai nama
 * aplikasi. Tidak diedit lewat menu Pengaturan (tidak seperti email
 * verifikasi) karena isinya singkat & jarang perlu diubah.
 */
class ResetPasswordNotification extends BaseResetPassword
{
    protected function buildMailMessage($url): MailMessage
    {
        $menit = (int) config('auth.passwords.'.config('auth.defaults.passwords').'.expire', 60);

        return (new MailMessage)
            ->subject('Atur Ulang Kata Sandi — '.config('app.name'))
            ->greeting('Halo,')
            ->line('Kami menerima permintaan untuk mengatur ulang kata sandi akun '.config('app.name').' kamu.')
            ->action('Atur Ulang Kata Sandi', $url)
            ->line("Tautan ini berlaku selama {$menit} menit.")
            ->line('Jika kamu tidak meminta ini, abaikan saja email ini — kata sandi kamu tidak akan berubah.');
    }
}
