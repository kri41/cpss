<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Log In - CPSS Dispora</title>
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
    class="antialiased font-sans animate-bg bg-gradient-to-br from-blue-700 via-indigo-600 to-purple-800 min-h-screen flex justify-center items-center p-4 relative overflow-hidden">

    <div
        class="absolute top-1/4 left-1/4 w-96 h-96 bg-blue-400 rounded-full mix-blend-multiply filter blur-[100px] opacity-60 animate-pulse">
    </div>
    <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-pink-500 rounded-full mix-blend-multiply filter blur-[100px] opacity-60 animate-pulse"
        style="animation-delay: 2s;"></div>

    <div class="bg-white rounded-[2rem] shadow-2xl w-full max-w-md overflow-hidden relative z-10 p-8">

        <div class="text-center mb-8">
            <div
                class="w-16 h-16 bg-gradient-to-tr from-teal-400 to-emerald-300 rounded-2xl shadow-lg flex items-center justify-center text-white font-bold text-3xl mx-auto mb-4">
                C</div>
            <h3 class="text-2xl font-black text-indigo-900 mb-2">Log In Relawan</h3>
            <p class="text-gray-500 text-sm">Sesi Anda telah diatur ulang. Silakan masuk kembali.</p>
        </div>

        @if ($errors->any())
            <div class="mb-4 p-3 bg-red-50 text-red-600 rounded-lg text-sm border border-red-200">
                Email atau kata sandi tidak sesuai.
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Email Aktif</label>
                <div class="relative">
                    <i class="fas fa-envelope absolute left-4 top-3.5 text-gray-400"></i>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                        class="w-full pl-11 pr-4 py-3 rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Kata Sandi</label>
                <div class="relative">
                    <i class="fas fa-lock absolute left-4 top-3.5 text-gray-400"></i>
                    <input type="password" name="password" required
                        class="w-full pl-11 pr-4 py-3 rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                </div>
            </div>

            <div class="flex items-center justify-between">
                <label class="flex items-center">
                    <input type="checkbox" name="remember"
                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                    <span class="ml-2 text-sm text-gray-600">Ingat Saya</span>
                </label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}"
                        class="text-sm text-indigo-600 hover:underline font-semibold">Lupa Sandi?</a>
                @endif
            </div>

            <button type="submit"
                class="w-full py-3.5 bg-indigo-600 text-white font-bold rounded-xl hover:bg-indigo-700 shadow-lg hover:shadow-indigo-500/30 transition-all active:scale-95">
                Masuk Sistem
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-gray-500">
            Kembali ke <a href="{{ url('/') }}" class="text-indigo-600 font-bold hover:underline">Halaman Utama</a>
        </p>
    </div>
</body>

</html>
