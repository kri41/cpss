<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CPSS - Pembangunan Olahraga Daerah</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* Mencegah modal berkedip saat halaman baru dimuat */
        [x-cloak] {
            display: none !important;
        }

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
    class="antialiased font-sans animate-bg bg-gradient-to-br from-blue-700 via-indigo-600 to-purple-800 text-gray-800 selection:bg-teal-300 selection:text-teal-900"
    x-data="{ modal: '{{ $errors->any() ? (old('name') ? 'register' : 'login') : '' }}' }">

    <nav
        class="fixed w-full z-40 top-0 transition-all duration-300 bg-white/10 backdrop-blur-md border-b border-white/20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <div class="flex-shrink-0 flex items-center gap-3">
                    <div
                        class="w-10 h-10 bg-gradient-to-tr from-teal-400 to-emerald-300 rounded-xl shadow-lg flex items-center justify-center text-white font-bold text-xl">
                        C
                    </div>
                    <span class="font-bold text-2xl text-white tracking-wider">CPSS</span>
                </div>

                <div class="hidden md:flex items-center space-x-4">
                    @if (Route::has('login'))
                        @auth
                            <span class="text-white/80 text-sm mr-4">Halo, {{ Auth::user()->name }}</span>
                            <a href="{{ url('/dashboard') }}"
                                class="px-6 py-2.5 bg-gradient-to-r from-teal-400 to-emerald-400 text-teal-950 font-bold rounded-full shadow-lg hover:scale-105 transition-all duration-300">
                                Buka Dasbor
                            </a>
                        @else
                            <button @click="modal = 'login'"
                                class="text-white hover:text-teal-300 font-semibold px-4 py-2 transition cursor-pointer">
                                Log In
                            </button>
                            <button @click="modal = 'register'"
                                class="px-6 py-2.5 bg-white/20 text-white border border-white/50 font-bold rounded-full hover:bg-white hover:text-indigo-700 hover:scale-105 transition-all duration-300 backdrop-blur-sm cursor-pointer">
                                Daftar Relawan
                            </button>
                        @endauth
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <div class="relative min-h-screen flex flex-col justify-center items-center overflow-hidden pt-20">
        <div
            class="absolute top-20 left-10 w-96 h-96 bg-blue-400 rounded-full mix-blend-multiply filter blur-[100px] opacity-60 animate-pulse">
        </div>
        <div class="absolute bottom-10 right-10 w-96 h-96 bg-pink-500 rounded-full mix-blend-multiply filter blur-[100px] opacity-60 animate-pulse"
            style="animation-delay: 2s;"></div>

        <div class="relative z-10 w-full max-w-5xl mx-auto px-6 text-center" x-data="{ show: false }"
            x-init="setTimeout(() => show = true, 100)">
            <div x-show="show" x-transition:enter="transition ease-out duration-1000 transform"
                x-transition:enter-start="opacity-0 translate-y-12" x-transition:enter-end="opacity-100 translate-y-0"
                class="bg-white/10 backdrop-blur-2xl border border-white/20 p-12 md:p-20 rounded-[3rem] shadow-2xl">
                <span
                    class="inline-block py-1 px-3 rounded-full bg-white/20 border border-white/30 text-teal-100 text-sm font-semibold tracking-widest mb-6">PROTOTIPE
                    DISERTASI S3</span>
                <h1 class="text-5xl md:text-7xl font-extrabold text-white mb-8 tracking-tight leading-tight">
                    Cloud-Participatory <br>
                    <span
                        class="text-transparent bg-clip-text bg-gradient-to-r from-teal-300 to-emerald-200 drop-shadow-sm">Sport
                        Sensing</span>
                </h1>
                <p class="text-xl md:text-2xl text-blue-50 mb-12 leading-relaxed font-light max-w-3xl mx-auto">
                    Platform (<i>crowdsourcing</i>) terintegrasi untuk memetakan fasilitas dan aktivitas
                    olahraga daerah berbasis bukti.
                </p>

                <div class="flex justify-center">
                    @auth
                        <a href="{{ url('/dashboard') }}"
                            class="px-10 py-4 bg-white text-indigo-700 text-lg font-extrabold rounded-full shadow-[0_0_30px_rgba(255,255,255,0.4)] hover:scale-105 transition-all duration-300">
                            Masuk ke Dasbor Utama
                        </a>
                    @else
                        <button @click="modal = 'register'"
                            class="px-10 py-4 bg-gradient-to-r from-teal-400 to-emerald-400 text-teal-950 text-lg font-extrabold rounded-full shadow-[0_0_30px_rgba(45,212,191,0.4)] hover:scale-105 transition-all duration-300">
                            Mulai Berkontribusi Sekarang
                        </button>
                    @endauth
                </div>
            </div>
        </div>
    </div>

    <div x-show="modal === 'login'" x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-indigo-900/60 backdrop-blur-sm">

        <div x-show="modal === 'login'" @click.away="modal = ''" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-90 translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-90 translate-y-4"
            class="bg-white rounded-[2rem] shadow-2xl w-full max-w-md overflow-hidden relative">

            <button @click="modal = ''"
                class="absolute top-4 right-4 text-gray-400 hover:text-red-500 transition text-xl w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center">
                <i class="fas fa-times"></i>
            </button>

            <div class="p-8">
                <h3 class="text-2xl font-black text-indigo-900 mb-2">Log In Relawan</h3>
                <p class="text-gray-500 text-sm mb-6">Silakan masuk untuk mulai memetakan data olahraga.</p>

                @if ($errors->any() && !old('name'))
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
                    </div>

                    <button type="submit"
                        class="w-full py-3.5 bg-indigo-600 text-white font-bold rounded-xl hover:bg-indigo-700 shadow-lg hover:shadow-indigo-500/30 transition-all active:scale-95">
                        Masuk Sistem
                    </button>
                </form>

                <p class="mt-6 text-center text-sm text-gray-500">
                    Belum punya akun?
                    <button @click="modal = 'register'" class="text-indigo-600 font-bold hover:underline">Daftar di
                        sini</button>
                </p>
            </div>
        </div>
    </div>

    <div x-show="modal === 'register'" x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-teal-900/60 backdrop-blur-sm">

        <div x-show="modal === 'register'" @click.away="modal = ''"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-90 translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-90 translate-y-4"
            class="bg-white rounded-[2rem] shadow-2xl w-full max-w-md overflow-hidden relative max-h-[90vh] overflow-y-auto">

            <button @click="modal = ''"
                class="absolute top-4 right-4 text-gray-400 hover:text-red-500 transition text-xl w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center">
                <i class="fas fa-times"></i>
            </button>

            <div class="p-8">
                <h3 class="text-2xl font-black text-teal-700 mb-2">Daftar Relawan CPSS</h3>
                <p class="text-gray-500 text-sm mb-6">Bergabunglah menjadi agen perubahan olahraga daerah.</p>

                @if ($errors->any() && old('name'))
                    <div class="mb-4 p-3 bg-red-50 text-red-600 rounded-lg text-sm border border-red-200">
                        Mohon periksa kembali isian Anda. (Mungkin email sudah terdaftar).
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
                    Sudah mendaftar sebelumnya?
                    <button @click="modal = 'login'" class="text-teal-600 font-bold hover:underline">Log In di
                        sini</button>
                </p>
            </div>
        </div>
    </div>

</body>

</html>
