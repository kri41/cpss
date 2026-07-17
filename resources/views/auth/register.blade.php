<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar - Dataraga</title>
    <link rel="icon" href="/storage/logo.png" type="image/png">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @keyframes gradient-move {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        .animate-bg {
            background-size: 200% 200%;
            animation: gradient-move 12s ease infinite;
        }
    </style>
</head>

<body
    class="antialiased font-sans animate-bg bg-gradient-to-br from-teal-700 via-emerald-600 to-teal-900 min-h-screen flex justify-center items-center p-4 relative overflow-hidden">

    <div
        class="absolute top-1/4 left-1/4 w-96 h-96 bg-teal-400 rounded-full mix-blend-multiply filter blur-[100px] opacity-60 animate-pulse">
    </div>
    <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-emerald-500 rounded-full mix-blend-multiply filter blur-[100px] opacity-60 animate-pulse"
        style="animation-delay: 2s;"></div>

    <div
        class="bg-white rounded-[2rem] shadow-2xl w-full max-w-md overflow-hidden relative z-10 p-8 max-h-[95vh] overflow-y-auto">
        <div class="text-center mb-6">
            <img src="/storage/logo.png" alt="Dataraga" class="w-24 h-24 object-contain mx-auto mb-4">
            <h3 class="text-2xl font-black text-teal-700 mb-1">Daftar Relawan Dataraga</h3>
            <p class="text-gray-500 text-sm">Bergabunglah menjadi agen perubahan.</p>
        </div>

        @if ($errors->any())
            <div class="mb-4 p-3 bg-red-50 text-red-600 rounded-lg text-sm border border-red-200">
                Mohon periksa kembali isian Anda.
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Lengkap</label>
                <div class="relative">
                    <i class="fas fa-user absolute left-4 top-3.5 text-gray-400"></i>
                    <input type="text" name="name" value="{{ old('name') }}" required autofocus
                        class="w-full pl-11 pr-4 py-3 rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all">
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Email Aktif</label>
                <div class="relative">
                    <i class="fas fa-envelope absolute left-4 top-3.5 text-gray-400"></i>
                    <input type="email" name="email" value="{{ old('email') }}" required
                        class="w-full pl-11 pr-4 py-3 rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all">
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Kata Sandi</label>
                <div class="relative">
                    <i class="fas fa-lock absolute left-4 top-3.5 text-gray-400"></i>
                    <input type="password" name="password" required
                        class="w-full pl-11 pr-4 py-3 rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all">
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Konfirmasi Sandi</label>
                <div class="relative">
                    <i class="fas fa-check-double absolute left-4 top-3.5 text-gray-400"></i>
                    <input type="password" name="password_confirmation" required
                        class="w-full pl-11 pr-4 py-3 rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all">
                </div>
            </div>

            <button type="submit"
                class="w-full py-3.5 mt-2 bg-gradient-to-r from-teal-500 to-emerald-500 text-white font-bold rounded-xl hover:from-teal-600 hover:to-emerald-600 shadow-lg hover:shadow-teal-500/30 transition-all active:scale-95">
                Daftar & Langsung Masuk
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-gray-500">
            Kembali ke <a href="{{ url('/') }}" class="text-teal-600 font-bold hover:underline">Halaman Utama</a>
        </p>
    </div>
</body>

</html>
