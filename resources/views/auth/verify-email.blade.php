<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verifikasi Email — Dataraga</title>
    <link rel="icon" href="/storage/logo.png" type="image/png">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="antialiased font-sans min-h-screen flex items-center justify-center bg-gradient-to-br from-teal-600 via-teal-500 to-emerald-600 p-6">

    <div class="w-full max-w-md bg-white rounded-3xl shadow-2xl p-8 sm:p-10 text-center">
        <div class="mx-auto w-16 h-16 rounded-2xl bg-teal-50 flex items-center justify-center mb-5">
            <i class="fas fa-envelope-open-text text-3xl text-teal-600"></i>
        </div>

        <h1 class="text-2xl font-black text-gray-900 mb-2">Cek Email Kamu</h1>
        <p class="text-sm text-gray-500 leading-relaxed">
            Kami sudah mengirim tautan verifikasi ke
            <span class="font-semibold text-gray-700">{{ auth()->user()->email }}</span>.
            Klik tautan tersebut untuk mengaktifkan akun dan mulai berkontribusi.
        </p>

        @if (session('status') == 'verification-link-sent')
            <div class="mt-5 flex items-center justify-center gap-2 p-3 bg-green-50 text-green-700 rounded-xl text-sm border border-green-100">
                <i class="fas fa-check-circle"></i>
                Tautan verifikasi baru sudah dikirim.
            </div>
        @endif
        @if (session('error'))
            <div class="mt-5 flex items-center justify-center gap-2 p-3 bg-red-50 text-red-700 rounded-xl text-sm border border-red-100">
                <i class="fas fa-exclamation-circle"></i>
                {{ session('error') }}
            </div>
        @endif

        <p class="text-xs text-gray-400 mt-5">Tidak menerima email? Cek folder spam, atau kirim ulang di bawah ini.</p>

        <form method="POST" action="{{ route('verification.send') }}" class="mt-4">
            @csrf
            <button type="submit"
                class="w-full py-3.5 bg-teal-600 text-white font-black rounded-2xl hover:bg-teal-700 shadow-lg transition-all active:scale-[0.98]">
                Kirim Ulang Email Verifikasi
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}" class="mt-4">
            @csrf
            <button type="submit" class="text-sm text-gray-500 hover:text-gray-800 font-semibold">
                <i class="fas fa-arrow-left mr-1"></i> Keluar
            </button>
        </form>
    </div>

</body>
</html>
