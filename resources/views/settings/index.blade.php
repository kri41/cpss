@extends('layouts.app')

@section('title', 'Pengaturan - Dataraga')

@section('content')
<div class="py-6" x-data="{ tab: 'server' }">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        <div>
            <h1 class="text-2xl font-bold text-gray-900">Pengaturan</h1>
            <p class="text-sm text-gray-500 mt-1">Konfigurasi server email pengirim dan isi email verifikasi pendaftar.</p>
        </div>

        @if(session('success'))
            <div class="p-3 bg-green-50 border border-green-200 rounded-xl text-sm text-green-700">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="p-3 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700">{{ session('error') }}</div>
        @endif
        @if($errors->any())
            <div class="p-3 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700">
                <ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        {{-- Tabs --}}
        <div class="flex gap-1 bg-gray-100 p-1 rounded-xl w-fit">
            @foreach(['server' => 'Server Email', 'verifikasi' => 'Email Verifikasi', 'selamat' => 'Email Selamat Datang'] as $key => $label)
                <button type="button" @click="tab = '{{ $key }}'"
                    :class="tab === '{{ $key }}' ? 'bg-white text-blue-700 shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                    class="px-4 py-2 text-sm font-medium rounded-lg transition">{{ $label }}</button>
            @endforeach
        </div>

        <form method="POST" action="{{ route('settings.update') }}" class="space-y-6">
            @csrf @method('PUT')

            {{-- ===== TAB: SERVER EMAIL ===== --}}
            <div x-show="tab === 'server'" x-cloak class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-5">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Metode Pengiriman</label>
                    <select name="mail_mailer" class="w-full sm:w-64 rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="smtp" @selected($settings['mail_mailer'] === 'smtp')>SMTP (kirim email sungguhan)</option>
                        <option value="log"  @selected($settings['mail_mailer'] === 'log')>Log (email hanya dicatat, tidak dikirim)</option>
                    </select>
                    <p class="text-xs text-gray-400 mt-1">Pilih SMTP setelah mengisi data server di bawah.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Host / Server</label>
                        <input type="text" name="mail_host" value="{{ old('mail_host', $settings['mail_host']) }}" placeholder="mail.dataraga.my.id"
                               class="w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Port</label>
                        <input type="number" name="mail_port" value="{{ old('mail_port', $settings['mail_port']) }}" placeholder="465"
                               class="w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Username</label>
                        <input type="text" name="mail_username" value="{{ old('mail_username', $settings['mail_username']) }}" placeholder="_mainaccount@dataraga.my.id" autocomplete="off"
                               class="w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                            Password
                            @if($hasMailPassword)<span class="text-xs font-normal text-green-600">(tersimpan &mdash; kosongkan bila tidak diubah)</span>@endif
                        </label>
                        <input type="password" name="mail_password" value="" placeholder="{{ $hasMailPassword ? '••••••••' : 'password email' }}" autocomplete="new-password"
                               class="w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Enkripsi</label>
                        <select name="mail_encryption" class="w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="ssl"  @selected($settings['mail_encryption'] === 'ssl')>SSL (biasanya port 465)</option>
                            <option value="tls"  @selected($settings['mail_encryption'] === 'tls')>TLS / STARTTLS (biasanya port 587)</option>
                            <option value="none" @selected($settings['mail_encryption'] === 'none')>Tanpa enkripsi</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-gray-100">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Email Pengirim (From)</label>
                        <input type="email" name="mail_from_address" value="{{ old('mail_from_address', $settings['mail_from_address']) }}" placeholder="noreply@dataraga.my.id"
                               class="w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nama Pengirim</label>
                        <input type="text" name="mail_from_name" value="{{ old('mail_from_name', $settings['mail_from_name']) }}" placeholder="Dataraga"
                               class="w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-2 pt-3 border-t border-gray-100">
                    <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl transition">Simpan Pengaturan</button>
                </div>
            </div>

            {{-- ===== TAB: EMAIL VERIFIKASI ===== --}}
            <div x-show="tab === 'verifikasi'" x-cloak class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-4">
                <p class="text-xs text-gray-500 bg-blue-50 border border-blue-100 rounded-lg p-3">
                    Placeholder yang bisa dipakai: <code class="font-mono text-blue-700">{nama}</code> (nama pendaftar),
                    <code class="font-mono text-blue-700">{app}</code> (nama aplikasi).
                    Tombol verifikasi &amp; tautannya otomatis ditambahkan sistem.
                </p>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Subjek</label>
                    <input type="text" name="verify_email_subject" value="{{ old('verify_email_subject', $settings['verify_email_subject']) }}"
                           class="w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Salam Pembuka</label>
                    <input type="text" name="verify_email_greeting" value="{{ old('verify_email_greeting', $settings['verify_email_greeting']) }}"
                           class="w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Isi Email</label>
                    <textarea name="verify_email_body" rows="4" class="w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">{{ old('verify_email_body', $settings['verify_email_body']) }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Label Tombol</label>
                    <input type="text" name="verify_email_button" value="{{ old('verify_email_button', $settings['verify_email_button']) }}"
                           class="w-full sm:w-64 rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Kalimat Penutup</label>
                    <textarea name="verify_email_outro" rows="2" class="w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">{{ old('verify_email_outro', $settings['verify_email_outro']) }}</textarea>
                </div>
                <div class="pt-3 border-t border-gray-100">
                    <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl transition">Simpan Pengaturan</button>
                </div>
            </div>

            {{-- ===== TAB: EMAIL SELAMAT DATANG ===== --}}
            <div x-show="tab === 'selamat'" x-cloak class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-4">
                <label class="flex items-center gap-2.5">
                    <input type="checkbox" name="welcome_email_enabled" value="1" @checked(old('welcome_email_enabled', $settings['welcome_email_enabled']) == '1')
                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <span class="text-sm font-medium text-gray-700">Kirim email ini setelah pendaftar berhasil verifikasi</span>
                </label>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Subjek</label>
                    <input type="text" name="welcome_email_subject" value="{{ old('welcome_email_subject', $settings['welcome_email_subject']) }}"
                           class="w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Salam Pembuka</label>
                    <input type="text" name="welcome_email_greeting" value="{{ old('welcome_email_greeting', $settings['welcome_email_greeting']) }}"
                           class="w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Isi Email</label>
                    <textarea name="welcome_email_body" rows="4" class="w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">{{ old('welcome_email_body', $settings['welcome_email_body']) }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Label Tombol</label>
                    <input type="text" name="welcome_email_button" value="{{ old('welcome_email_button', $settings['welcome_email_button']) }}"
                           class="w-full sm:w-64 rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div class="pt-3 border-t border-gray-100">
                    <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl transition">Simpan Pengaturan</button>
                </div>
            </div>
        </form>

        {{-- Test kirim email (form terpisah) --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-base font-bold text-gray-900">Uji Coba Kirim Email</h2>
            <p class="text-xs text-gray-500 mt-0.5 mb-3">Simpan pengaturan server dulu, lalu kirim email uji untuk memastikan konfigurasi benar.</p>
            <form method="POST" action="{{ route('settings.test-email') }}" class="flex flex-col sm:flex-row gap-2">
                @csrf
                <input type="email" name="test_email" required placeholder="email-tujuan@contoh.com"
                       class="flex-1 rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                <button type="submit" class="px-5 py-2.5 bg-gray-800 hover:bg-gray-900 text-white text-sm font-semibold rounded-xl transition">Kirim Email Uji</button>
            </form>
        </div>

    </div>
</div>
@endsection
