<?php

namespace App\Providers;

use App\Models\Club;
use App\Models\Prasarana;
use App\Models\Setting;
use App\Notifications\AccountVerifiedNotification;
use Illuminate\Auth\Events\Verified;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Relation::enforceMorphMap([
            'prasarana' => Prasarana::class,
            'club' => Club::class,
            'event' => \App\Models\Event::class,
        ]);

        $this->applyRuntimeMailConfig();

        // Kirim email "selamat datang" setelah email diverifikasi.
        // Dibungkus try/catch supaya kegagalan kirim email tidak menggagalkan verifikasi.
        Event::listen(Verified::class, function (Verified $event) {
            if (Setting::get('welcome_email_enabled', '1') === '1') {
                try {
                    $event->user->notify(new AccountVerifiedNotification);
                } catch (\Throwable $e) {
                    report($e);
                }
            }
        });
    }

    /**
     * Timpa konfigurasi mailer dari tabel settings (menu Pengaturan) bila diisi.
     * Dibungkus try/catch penuh supaya boot() tidak pernah menggagalkan request
     * (mis. tabel settings / cache belum siap saat deploy).
     */
    private function applyRuntimeMailConfig(): void
    {
        try {
            $this->doApplyRuntimeMailConfig();
        } catch (\Throwable $e) {
            report($e);
        }
    }

    private function doApplyRuntimeMailConfig(): void
    {
        $mailer = Setting::get('mail_mailer');

        if (! $mailer) {
            return;
        }

        config(['mail.default' => $mailer]);

        if ($mailer === 'smtp') {
            $encryption = Setting::get('mail_encryption', 'ssl');

            config([
                'mail.mailers.smtp.host' => Setting::get('mail_host') ?: config('mail.mailers.smtp.host'),
                'mail.mailers.smtp.port' => (int) (Setting::get('mail_port') ?: 465),
                'mail.mailers.smtp.username' => Setting::get('mail_username') ?: null,
                'mail.mailers.smtp.password' => Setting::get('mail_password') ?: null,
                'mail.mailers.smtp.scheme' => $encryption === 'ssl' ? 'smtps' : null,
            ]);
        }

        if ($from = Setting::get('mail_from_address')) {
            config([
                'mail.from.address' => $from,
                'mail.from.name' => Setting::get('mail_from_name') ?: config('app.name'),
            ]);
        }
    }
}
