<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dataraga — Mencatat Gerak, Membangun Bangsa</title>
    <link rel="icon" href="/storage/logo.png" type="image/png">
    <link rel="apple-touch-icon" href="/storage/logo.png">
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#2563eb">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="Dataraga">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        [x-cloak] { display: none !important; }
        .text-gradient {
            background: linear-gradient(135deg, #1e40af, #0ea5e9);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .hero-bg {
            background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 50%, #0369a1 100%);
        }
        @keyframes float-up {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        @keyframes float-up-delay {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-14px); }
        }
        .float-1 { animation: float-up 4s ease-in-out infinite; }
        .float-2 { animation: float-up-delay 4s ease-in-out infinite; animation-delay: 0.8s; }
    </style>
</head>
<body class="antialiased font-sans bg-white text-gray-800"
    x-data="{ modal: '{{ $errors->any() ? (old('name') ? 'register' : 'login') : '' }}' }">

    <div class="min-h-screen flex flex-col">

        <!-- Navbar -->
        <nav class="shrink-0 bg-white/90 backdrop-blur-md border-b border-gray-100 sticky top-0 z-30">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-14">
                    <a href="{{ url('/') }}" class="flex items-center gap-2.5">
                        <img src="/storage/logo.png" alt="Dataraga" class="h-9 w-9 object-contain">
                        <span class="font-bold text-lg text-gray-900 tracking-tight">Dataraga</span>
                    </a>
                    <div class="flex items-center gap-2">
                        @guest
                            <button @click="modal = 'login'" class="text-gray-600 hover:text-blue-600 font-medium px-3 py-1.5 transition text-sm">Masuk</button>
                            <button @click="modal = 'register'" class="px-4 py-1.5 bg-blue-600 text-white font-semibold rounded-full hover:bg-blue-700 transition text-sm">Daftar</button>
                        @else
                            <span class="text-gray-500 text-sm hidden sm:inline mr-1">Halo, {{ Auth::user()->name }}</span>
                            <a href="{{ url('/dashboard') }}" class="px-4 py-1.5 bg-blue-600 text-white font-semibold rounded-full hover:bg-blue-700 transition text-sm">Dasbor</a>
                        @endguest
                    </div>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <main class="flex-1">
            <div class="relative overflow-hidden hero-bg">
                <!-- Decorative blobs -->
                <div class="absolute top-0 left-0 w-96 h-96 bg-blue-500 rounded-full mix-blend-multiply filter blur-[120px] opacity-20 pointer-events-none"></div>
                <div class="absolute bottom-0 right-0 w-96 h-96 bg-teal-500 rounded-full mix-blend-multiply filter blur-[120px] opacity-20 pointer-events-none"></div>

                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="grid lg:grid-cols-2 min-h-[88vh] items-center gap-8 py-12 lg:py-0">

                        <!-- Left: Copy -->
                        <div class="text-center lg:text-left order-2 lg:order-1 pb-8 lg:pb-0">
                            <span class="inline-block px-3 py-1 rounded-full bg-blue-500/20 text-blue-300 text-[11px] font-semibold tracking-widest mb-5 border border-blue-400/30">PENDATAAN KEOLAHRAGAAN BERBASIS MASYARAKAT</span>
                            <h1 class="text-4xl sm:text-5xl lg:text-[3.2rem] font-extrabold text-white leading-[1.1] mb-5">
                                Setiap Lapangan<br>Tercatat,
                                <span class="block text-transparent bg-clip-text bg-gradient-to-r from-teal-300 to-sky-300">Setiap Atlet Terhitung</span>
                            </h1>
                            <p class="text-blue-100/80 text-base leading-relaxed mb-8 max-w-lg mx-auto lg:mx-0">
                                Platform pelaporan data keolahragaan daerah yang menghubungkan relawan, pengurus, dan pemangku kepentingan — dari desa hingga provinsi.
                            </p>

                            <!-- Stats Row -->
                            <div class="flex justify-center lg:justify-start gap-6 mb-8">
                                <div class="text-center">
                                    <p class="text-2xl font-black text-white">{{ $stats['totalPrasarana'] }}</p>
                                    <p class="text-xs text-blue-200">Prasarana</p>
                                </div>
                                <div class="w-px bg-white/10"></div>
                                <div class="text-center">
                                    <p class="text-2xl font-black text-white">{{ $stats['totalEvents'] }}</p>
                                    <p class="text-xs text-blue-200">Event</p>
                                </div>
                                <div class="w-px bg-white/10"></div>
                                <div class="text-center">
                                    <p class="text-2xl font-black text-white">{{ $stats['totalClubs'] }}</p>
                                    <p class="text-xs text-blue-200">Klub Aktif</p>
                                </div>
                            </div>

                            <div class="flex flex-col sm:flex-row gap-3 justify-center lg:justify-start">
                                @auth
                                    <a href="{{ url('/dashboard') }}" class="px-8 py-3.5 bg-white text-blue-700 font-bold rounded-xl hover:bg-blue-50 transition shadow-lg text-sm text-center">Masuk ke Dasbor</a>
                                @else
                                    <button @click="modal = 'register'" class="px-8 py-3.5 bg-white text-blue-700 font-bold rounded-xl hover:bg-blue-50 transition shadow-lg text-sm">Mulai Berkontribusi</button>
                                    <button @click="modal = 'login'" class="px-8 py-3.5 bg-white/10 text-white font-semibold rounded-xl hover:bg-white/20 transition border border-white/20 text-sm">Masuk Sekarang</button>
                                @endauth
                            </div>
                        </div>

                        <!-- Right: Characters -->
                        <div class="order-1 lg:order-2 flex items-end justify-center lg:justify-end gap-0 relative pt-8 lg:pt-0">
                            <!-- Floating badge cards -->
                            <div class="absolute top-4 lg:top-8 left-0 lg:left-4 bg-white/10 backdrop-blur-sm border border-white/20 rounded-2xl p-3 text-white z-20 shadow-lg">
                                <p class="text-[10px] text-blue-200 font-medium mb-0.5">Event Aktif</p>
                                <p class="text-xl font-black leading-none">{{ $stats['totalEvents'] }}</p>
                                <div class="mt-1 flex gap-0.5">
                                    @for ($i = 0; $i < 5; $i++)
                                        <div class="h-1 w-4 rounded-full {{ $i < 3 ? 'bg-teal-400' : 'bg-white/20' }}"></div>
                                    @endfor
                                </div>
                            </div>
                            <div class="absolute top-4 lg:top-8 right-0 lg:right-4 bg-white/10 backdrop-blur-sm border border-white/20 rounded-2xl p-3 text-white z-20 shadow-lg">
                                <p class="text-[10px] text-blue-200 font-medium mb-0.5">Tervalidasi</p>
                                <p class="text-xl font-black leading-none">{{ $stats['totalPrasarana'] }}</p>
                                <p class="text-[10px] text-teal-300 mt-0.5 flex items-center gap-1">
                                    <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                    Prasarana
                                </p>
                            </div>

                            <!-- Boy character (3.png) -->
                            <div class="float-1 relative z-10 -mr-8 sm:-mr-12">
                                <img src="/storage/karakter/3.png" alt="Relawan Dataraga"
                                    class="h-56 sm:h-72 lg:h-80 xl:h-[22rem] w-auto object-contain drop-shadow-2xl select-none">
                            </div>
                            <!-- Girl character (2.png) -->
                            <div class="float-2 relative z-20">
                                <img src="/storage/karakter/2.png" alt="Relawan Dataraga"
                                    class="h-64 sm:h-80 lg:h-96 xl:h-[26rem] w-auto object-contain drop-shadow-2xl select-none">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Access Cards -->
            <div class="bg-white py-8">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-4">
                        <a href="{{ route('prasarana.index') }}" class="flex items-center gap-3 p-4 bg-blue-50 rounded-2xl hover:bg-blue-100 hover:shadow-md transition group">
                            <div class="w-11 h-11 bg-blue-100 rounded-xl flex items-center justify-center text-blue-600 group-hover:bg-white transition shrink-0">
                                <i class="fas fa-building text-base"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-bold text-gray-900 truncate">Prasarana</p>
                                <p class="text-xs text-gray-500">{{ $stats['totalPrasarana'] }} fasilitas</p>
                            </div>
                        </a>
                        <a href="{{ route('events.index') }}" class="flex items-center gap-3 p-4 bg-sky-50 rounded-2xl hover:bg-sky-100 hover:shadow-md transition group">
                            <div class="w-11 h-11 bg-sky-100 rounded-xl flex items-center justify-center text-sky-600 group-hover:bg-white transition shrink-0">
                                <i class="fas fa-calendar-alt text-base"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-bold text-gray-900 truncate">Event</p>
                                <p class="text-xs text-gray-500">{{ $stats['totalEvents'] }} event</p>
                            </div>
                        </a>
                        <a href="{{ route('clubs.index') }}" class="flex items-center gap-3 p-4 bg-indigo-50 rounded-2xl hover:bg-indigo-100 hover:shadow-md transition group">
                            <div class="w-11 h-11 bg-indigo-100 rounded-xl flex items-center justify-center text-indigo-600 group-hover:bg-white transition shrink-0">
                                <i class="fas fa-shield-alt text-base"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-bold text-gray-900 truncate">Klub</p>
                                <p class="text-xs text-gray-500">{{ $stats['totalClubs'] }} aktif</p>
                            </div>
                        </a>
                        <a href="{{ route('kalender.index') }}" class="flex items-center gap-3 p-4 bg-emerald-50 rounded-2xl hover:bg-emerald-100 hover:shadow-md transition group">
                            <div class="w-11 h-11 bg-emerald-100 rounded-xl flex items-center justify-center text-emerald-600 group-hover:bg-white transition shrink-0">
                                <i class="fas fa-calendar-check text-base"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-bold text-gray-900 truncate">Kalender</p>
                                <p class="text-xs text-gray-500">Jadwal kegiatan</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="shrink-0 bg-gray-900 text-gray-400 py-5">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col sm:flex-row justify-between items-center gap-2">
                    <div class="flex items-center gap-2">
                        <img src="/storage/logo.png" alt="Dataraga" class="h-6 w-6 object-contain brightness-0 invert">
                        <span class="text-white font-semibold text-sm">Dataraga</span>
                        <span class="text-gray-500 text-xs hidden sm:inline">|  Mencatat Gerak, Membangun Bangsa</span>
                    </div>
                    <p class="text-xs">&copy; {{ date('Y') }} Dataraga</p>
                </div>
            </div>
        </footer>
    </div>

    <!-- ========================================================
         LOGIN MODAL — Karakter Boy (3.png)
    ======================================================== -->
    <div x-show="modal === 'login'" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/70 backdrop-blur-sm">
        <div x-show="modal === 'login'" @click.away="modal = ''"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="bg-white rounded-3xl shadow-2xl w-full max-w-2xl overflow-hidden flex">

            <!-- Left: Character Panel -->
            <div class="hidden sm:flex w-5/12 relative overflow-hidden flex-col items-center justify-end"
                 style="background: linear-gradient(160deg, #1e3a8a 0%, #0369a1 60%, #0e7490 100%);">
                <div class="absolute inset-0 pointer-events-none">
                    <div class="absolute top-0 left-0 w-32 h-32 bg-white/5 rounded-full -translate-x-8 -translate-y-8"></div>
                    <div class="absolute bottom-16 right-0 w-20 h-20 bg-white/5 rounded-full translate-x-8"></div>
                </div>
                <div class="relative z-10 text-center px-4 pt-8 pb-0">
                    <p class="text-white font-black text-xl leading-tight mb-1">Selamat<br>Datang!</p>
                    <p class="text-blue-200 text-xs font-medium">Masuk untuk mulai berkontribusi</p>
                </div>
                <img src="/storage/karakter/3.png" alt="Relawan"
                     class="relative z-10 w-full max-w-[200px] object-contain object-bottom select-none drop-shadow-2xl">
            </div>

            <!-- Right: Form -->
            <div class="flex-1 p-7 relative">
                <button @click="modal = ''" class="absolute top-4 right-4 w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-400 hover:bg-red-50 hover:text-red-500 transition text-sm">
                    <i class="fas fa-times"></i>
                </button>

                <div class="mb-6">
                    <h3 class="text-2xl font-black text-gray-900 mb-1">Masuk</h3>
                    <p class="text-gray-500 text-sm">Masuk ke akun Dataraga kamu.</p>
                </div>

                @if ($errors->any() && !old('name'))
                    <div class="mb-4 p-3 bg-red-50 text-red-600 rounded-xl text-sm border border-red-100">
                        <i class="fas fa-exclamation-circle mr-1"></i> Email atau kata sandi tidak sesuai.
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Email</label>
                        <div class="relative">
                            <i class="fas fa-envelope absolute left-3.5 top-3 text-gray-400 text-sm"></i>
                            <input type="email" name="email" value="{{ old('email') }}" required autofocus
                                placeholder="email@kamu.com"
                                class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-sm">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Kata Sandi</label>
                        <div class="relative">
                            <i class="fas fa-lock absolute left-3.5 top-3 text-gray-400 text-sm"></i>
                            <input type="password" name="password" required placeholder="••••••••"
                                class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-sm">
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2 text-sm text-gray-500">
                            <input type="checkbox" name="remember" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            Ingat saya
                        </label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-xs text-blue-600 hover:underline font-semibold">Lupa sandi?</a>
                        @endif
                    </div>
                    <button type="submit"
                        class="w-full py-3 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 shadow-lg transition active:scale-95 text-sm">
                        Masuk Sistem
                    </button>
                </form>

                <p class="mt-5 text-center text-xs text-gray-500">
                    Belum punya akun?
                    <button @click="modal = 'register'" class="text-blue-600 font-bold hover:underline">Daftar di sini</button>
                </p>
            </div>
        </div>
    </div>

    <!-- ========================================================
         REGISTER MODAL — Karakter Girl (2.png)
    ======================================================== -->
    <div x-show="modal === 'register'" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/70 backdrop-blur-sm">
        <div x-show="modal === 'register'" @click.away="modal = ''"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="bg-white rounded-3xl shadow-2xl w-full max-w-2xl overflow-hidden flex max-h-[95vh]">

            <!-- Left: Character Panel -->
            <div class="hidden sm:flex w-5/12 relative overflow-hidden flex-col items-center justify-end"
                 style="background: linear-gradient(160deg, #0f766e 0%, #0d9488 60%, #059669 100%);">
                <div class="absolute inset-0 pointer-events-none">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-white/5 rounded-full translate-x-8 -translate-y-8"></div>
                    <div class="absolute bottom-16 left-0 w-20 h-20 bg-white/5 rounded-full -translate-x-8"></div>
                </div>
                <div class="relative z-10 text-center px-4 pt-8 pb-0">
                    <p class="text-white font-black text-xl leading-tight mb-1">Bergabung<br>Sekarang!</p>
                    <p class="text-teal-200 text-xs font-medium">Jadilah agen perubahan olahraga</p>
                </div>
                <img src="/storage/karakter/2.png" alt="Relawan"
                     class="relative z-10 w-full max-w-[200px] object-contain object-bottom select-none drop-shadow-2xl">
            </div>

            <!-- Right: Form -->
            <div class="flex-1 p-7 overflow-y-auto relative">
                <button @click="modal = ''" class="absolute top-4 right-4 w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-400 hover:bg-red-50 hover:text-red-500 transition text-sm">
                    <i class="fas fa-times"></i>
                </button>

                <div class="mb-5">
                    <h3 class="text-2xl font-black text-gray-900 mb-1">Daftar Relawan</h3>
                    <p class="text-gray-500 text-sm">Bergabunglah menjadi penggerak olahraga daerah.</p>
                </div>

                @if ($errors->any() && old('name'))
                    <div class="mb-4 p-3 bg-red-50 text-red-600 rounded-xl text-sm border border-red-100">
                        <i class="fas fa-exclamation-circle mr-1"></i> Mohon periksa kembali isian Anda.
                    </div>
                @endif

                <form method="POST" action="{{ route('register') }}" class="space-y-3.5">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nama Lengkap</label>
                        <div class="relative">
                            <i class="fas fa-user absolute left-3.5 top-3 text-gray-400 text-sm"></i>
                            <input type="text" name="name" value="{{ old('name') }}" required autofocus
                                placeholder="Nama kamu"
                                class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all text-sm">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Email Aktif</label>
                        <div class="relative">
                            <i class="fas fa-envelope absolute left-3.5 top-3 text-gray-400 text-sm"></i>
                            <input type="email" name="email" value="{{ old('email') }}" required
                                placeholder="email@kamu.com"
                                class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all text-sm">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Kata Sandi</label>
                        <div class="relative">
                            <i class="fas fa-lock absolute left-3.5 top-3 text-gray-400 text-sm"></i>
                            <input type="password" name="password" required placeholder="Min. 8 karakter"
                                class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all text-sm">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Konfirmasi Sandi</label>
                        <div class="relative">
                            <i class="fas fa-check-double absolute left-3.5 top-3 text-gray-400 text-sm"></i>
                            <input type="password" name="password_confirmation" required placeholder="Ulangi sandi"
                                class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all text-sm">
                        </div>
                    </div>
                    <button type="submit"
                        class="w-full py-3 bg-teal-600 text-white font-bold rounded-xl hover:bg-teal-700 shadow-lg transition active:scale-95 text-sm mt-1">
                        Daftar &amp; Langsung Masuk
                    </button>
                </form>

                <p class="mt-4 text-center text-xs text-gray-500">
                    Sudah punya akun?
                    <button @click="modal = 'login'" class="text-teal-600 font-bold hover:underline">Masuk di sini</button>
                </p>
            </div>
        </div>
    </div>

    <x-pwa-install />
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js').catch(() => {});
            });
        }
    </script>
</body>
</html>
