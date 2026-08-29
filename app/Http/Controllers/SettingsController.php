<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class SettingsController extends Controller
{
    /** Key yang dikelola oleh form Pengaturan. */
    private const KEYS = [
        'mail_mailer', 'mail_host', 'mail_port', 'mail_username', 'mail_password',
        'mail_encryption', 'mail_from_address', 'mail_from_name',
        'verify_email_subject', 'verify_email_greeting', 'verify_email_body',
        'verify_email_button', 'verify_email_outro',
        'welcome_email_enabled', 'welcome_email_subject', 'welcome_email_greeting',
        'welcome_email_body', 'welcome_email_button',
    ];

    public function edit(): View
    {
        $settings = [];
        foreach (self::KEYS as $key) {
            $settings[$key] = Setting::get($key, Setting::DEFAULTS[$key] ?? '');
        }
        // jangan kirim password ke form
        $settings['mail_password'] = '';
        $hasMailPassword = filled(Setting::get('mail_password'));

        return view('settings.index', compact('settings', 'hasMailPassword'));
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'mail_mailer' => ['required', 'in:smtp,log'],
            'mail_host' => ['nullable', 'string', 'max:255'],
            'mail_port' => ['nullable', 'integer', 'min:1', 'max:65535'],
            'mail_username' => ['nullable', 'string', 'max:255'],
            'mail_password' => ['nullable', 'string', 'max:255'],
            'mail_encryption' => ['required', 'in:ssl,tls,none'],
            'mail_from_address' => ['nullable', 'email', 'max:255'],
            'mail_from_name' => ['nullable', 'string', 'max:255'],

            'verify_email_subject' => ['required', 'string', 'max:255'],
            'verify_email_greeting' => ['required', 'string', 'max:255'],
            'verify_email_body' => ['required', 'string', 'max:2000'],
            'verify_email_button' => ['required', 'string', 'max:60'],
            'verify_email_outro' => ['nullable', 'string', 'max:2000'],

            'welcome_email_enabled' => ['nullable', 'boolean'],
            'welcome_email_subject' => ['required', 'string', 'max:255'],
            'welcome_email_greeting' => ['required', 'string', 'max:255'],
            'welcome_email_body' => ['required', 'string', 'max:2000'],
            'welcome_email_button' => ['required', 'string', 'max:60'],
        ]);

        $data['welcome_email_enabled'] = $request->boolean('welcome_email_enabled') ? '1' : '0';

        // password kosong / tidak dikirim = pertahankan yang lama
        if (blank($data['mail_password'] ?? null)) {
            unset($data['mail_password']);
        }

        // nilai nullable yang tidak dikirim -> simpan string kosong
        foreach (['mail_host', 'mail_port', 'mail_username', 'mail_from_address', 'mail_from_name', 'verify_email_outro'] as $key) {
            $data[$key] ??= '';
        }

        Setting::putMany($data);

        return back()->with('success', 'Pengaturan berhasil disimpan.');
    }

    public function testMail(Request $request): RedirectResponse
    {
        $request->validate(['test_email' => ['required', 'email']]);

        try {
            Mail::raw(
                'Ini email uji dari '.config('app.name').". Jika Anda menerima pesan ini, konfigurasi server email sudah benar.\n\nDikirim: ".now()->timezone('Asia/Jakarta')->format('d/m/Y H:i').' WIB',
                fn ($m) => $m->to($request->test_email)->subject('Email Uji — '.config('app.name'))
            );
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal mengirim: '.$e->getMessage());
        }

        return back()->with('success', 'Email uji dikirim ke '.$request->test_email.'. Cek kotak masuk (atau folder spam).');
    }
}
