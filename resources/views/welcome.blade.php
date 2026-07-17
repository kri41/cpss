<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dataraga — Mencatat Gerak, Membangun Bangsa</title>
    <link rel="icon" href="/storage/logo.png" type="image/png">
    <link rel="apple-touch-icon" href="/storage/logo.png">
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#1e3a8a">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="Dataraga">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        [x-cloak] { display: none !important; }
        html, body { height: 100%; overflow: hidden; }
        .hero-bg {
            background: linear-gradient(145deg, #0f172a 0%, #1e3a8a 45%, #075985 100%);
        }
        .text-glow {
            background: linear-gradient(120deg, #38bdf8, #34d399, #60a5fa);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        @keyframes bob {
            0%,100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        @keyframes bob2 {
            0%,100% { transform: translateY(0); }
            50% { transform: translateY(-14px); }
        }
        .bob  { animation: bob  4s ease-in-out infinite; }
        .bob2 { animation: bob2 4s ease-in-out infinite; animation-delay: .7s; }
    </style>
</head>
<body class="antialiased font-sans hero-bg"
      x-data="{ modal: '{{ $errors->any() ? (old('name') ? 'register' : 'login') : '' }}' }">

    <div class="h-screen flex flex-col">

        <!-- ── NAVBAR ─────────────────────────────────────────────────── -->
        <nav class="shrink-0 bg-white/5 backdrop-blur border-b border-white/10 z-30">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-14">
                    <a href="{{ url('/') }}" class="flex items-center gap-2.5">
                        <img src="/storage/logo.png" alt="Dataraga" class="h-9 w-9 object-contain brightness-0 invert">
                        <span class="font-bold text-lg text-white tracking-tight">Dataraga</span>
                    </a>
                    <div class="flex items-center gap-2">
                        @guest
                            <button @click="modal = 'login'"
                                class="text-white/70 hover:text-white font-medium px-3 py-1.5 transition text-sm">Masuk</button>
                            <button @click="modal = 'register'"
                                class="px-4 py-1.5 bg-white text-blue-800 font-bold rounded-full hover:bg-blue-50 transition text-sm shadow-lg">Daftar</button>
                        @else
                            <span class="text-white/60 text-sm hidden sm:inline mr-1">Halo, {{ Auth::user()->name }}</span>
                            <a href="{{ url('/dashboard') }}"
                                class="px-4 py-1.5 bg-white text-blue-800 font-bold rounded-full hover:bg-blue-50 transition text-sm shadow-lg">Dasbor</a>
                        @endguest
                    </div>
                </div>
            </div>
        </nav>

        <!-- ── HERO (satu screen penuh) ───────────────────────────────── -->
        <main class="flex-1 flex overflow-hidden">

            {{-- GIRL — KIRI --}}
            <div class="hidden lg:flex w-52 xl:w-64 2xl:w-72 shrink-0 items-end justify-center overflow-hidden">
                <div class="bob2">
                    <img src="/storage/karakter/2.png" alt="Relawan Dataraga"
                         class="h-[78vh] max-h-[540px] w-auto object-contain object-bottom select-none drop-shadow-2xl">
                </div>
            </div>

            {{-- KONTEN TENGAH --}}
            <div class="flex-1 flex flex-col items-center justify-center px-4 sm:px-8 text-center py-6 overflow-hidden">

                {{-- Badge --}}
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 border border-white/15 text-white/80 text-[11px] font-semibold tracking-widest mb-5">
                    <span class="w-1.5 h-1.5 rounded-full bg-teal-400 animate-pulse"></span>
                    PLATFORM OLAHRAGA MASYARAKAT INDONESIA
                </span>

                {{-- Headline --}}
                <h1 class="text-3xl sm:text-4xl xl:text-5xl font-extrabold text-white leading-[1.15] mb-4">
                    Olahraga Rakyat<br>
                    Dicatat, Didata,<br>
                    <span class="text-glow">Dibanggakan!</span>
                </h1>

                {{-- Desc --}}
                <p class="text-white/70 text-sm sm:text-base max-w-md leading-relaxed mb-6">
                    Jadilah relawan data — laporkan lapangan, catat event, dan dukung kebijakan olahraga daerahmu dari tingkat RT hingga provinsi.
                </p>

                {{-- Stats row --}}
                <div class="flex items-center gap-5 mb-6">
                    <div class="text-center">
                        <p class="text-xl font-black text-white">{{ $stats['totalPrasarana'] }}</p>
                        <p class="text-[10px] text-white/50 font-medium uppercase tracking-wide">Prasarana</p>
                    </div>
                    <div class="w-px h-8 bg-white/15"></div>
                    <div class="text-center">
                        <p class="text-xl font-black text-white">{{ $stats['totalEvents'] }}</p>
                        <p class="text-[10px] text-white/50 font-medium uppercase tracking-wide">Event</p>
                    </div>
                    <div class="w-px h-8 bg-white/15"></div>
                    <div class="text-center">
                        <p class="text-xl font-black text-white">{{ $stats['totalClubs'] }}</p>
                        <p class="text-[10px] text-white/50 font-medium uppercase tracking-wide">Klub Aktif</p>
                    </div>
                </div>

                {{-- CTA --}}
                <div class="flex flex-col sm:flex-row gap-2.5 mb-7">
                    @auth
                        <a href="{{ url('/dashboard') }}"
                           class="px-7 py-3 bg-white text-blue-800 font-bold rounded-xl hover:bg-blue-50 transition shadow-lg text-sm text-center">
                           Masuk ke Dasbor
                        </a>
                    @else
                        <button @click="modal = 'register'"
                            class="px-7 py-3 bg-white text-blue-800 font-bold rounded-xl hover:bg-blue-50 transition shadow-lg text-sm">
                            Mulai Berkontribusi!
                        </button>
                        <button @click="modal = 'login'"
                            class="px-7 py-3 bg-white/10 text-white font-semibold rounded-xl hover:bg-white/20 transition border border-white/20 text-sm">
                            Sudah punya akun?
                        </button>
                    @endauth
                </div>

                {{-- 4 Menu Cards --}}
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 w-full max-w-sm sm:max-w-lg">
                    <a href="{{ route('prasarana.index') }}"
                       class="group flex flex-col items-center gap-2 py-3 px-2 bg-white/8 hover:bg-white/15 backdrop-blur border border-white/10 hover:border-white/25 rounded-2xl transition-all">
                        <div class="w-9 h-9 rounded-xl bg-blue-500/30 group-hover:bg-blue-500/50 flex items-center justify-center transition">
                            <i class="fas fa-building text-white text-sm"></i>
                        </div>
                        <span class="text-white text-xs font-semibold">Prasarana</span>
                        <span class="text-white/40 text-[10px] font-medium">{{ $stats['totalPrasarana'] }} fasilitas</span>
                    </a>
                    <a href="{{ route('events.index') }}"
                       class="group flex flex-col items-center gap-2 py-3 px-2 bg-white/8 hover:bg-white/15 backdrop-blur border border-white/10 hover:border-white/25 rounded-2xl transition-all">
                        <div class="w-9 h-9 rounded-xl bg-sky-500/30 group-hover:bg-sky-500/50 flex items-center justify-center transition">
                            <i class="fas fa-calendar-alt text-white text-sm"></i>
                        </div>
                        <span class="text-white text-xs font-semibold">Event</span>
                        <span class="text-white/40 text-[10px] font-medium">{{ $stats['totalEvents'] }} event</span>
                    </a>
                    <a href="{{ route('clubs.index') }}"
                       class="group flex flex-col items-center gap-2 py-3 px-2 bg-white/8 hover:bg-white/15 backdrop-blur border border-white/10 hover:border-white/25 rounded-2xl transition-all">
                        <div class="w-9 h-9 rounded-xl bg-indigo-500/30 group-hover:bg-indigo-500/50 flex items-center justify-center transition">
                            <i class="fas fa-shield-alt text-white text-sm"></i>
                        </div>
                        <span class="text-white text-xs font-semibold">Klub</span>
                        <span class="text-white/40 text-[10px] font-medium">{{ $stats['totalClubs'] }} aktif</span>
                    </a>
                    <a href="{{ route('kalender.index') }}"
                       class="group flex flex-col items-center gap-2 py-3 px-2 bg-white/8 hover:bg-white/15 backdrop-blur border border-white/10 hover:border-white/25 rounded-2xl transition-all">
                        <div class="w-9 h-9 rounded-xl bg-emerald-500/30 group-hover:bg-emerald-500/50 flex items-center justify-center transition">
                            <i class="fas fa-calendar-check text-white text-sm"></i>
                        </div>
                        <span class="text-white text-xs font-semibold">Kalender</span>
                        <span class="text-white/40 text-[10px] font-medium">Jadwal kegiatan</span>
                    </a>
                </div>
            </div>

            {{-- BOY — KANAN --}}
            <div class="hidden lg:flex w-52 xl:w-64 2xl:w-72 shrink-0 items-end justify-center overflow-hidden">
                <div class="bob">
                    <img src="/storage/karakter/3.png" alt="Relawan Dataraga"
                         class="h-[78vh] max-h-[540px] w-auto object-contain object-bottom select-none drop-shadow-2xl">
                </div>
            </div>
        </main>
    </div>

    <!-- ================================================================
         LOGIN MODAL
    ================================================================ -->
    <div x-show="modal === 'login'" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/70 backdrop-blur-sm">
        <div x-show="modal === 'login'" @click.away="modal = ''"
             x-transition:enter="transition ease-out duration-250"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="bg-white rounded-3xl shadow-2xl w-full max-w-2xl overflow-hidden flex">

            {{-- Kiri: karakter BOY --}}
            <div class="hidden sm:flex w-[40%] relative overflow-hidden flex-col items-center justify-end"
                 style="background: linear-gradient(160deg,#1e3a8a 0%,#1d4ed8 50%,#0284c7 100%)">
                <div class="absolute inset-0 pointer-events-none">
                    <div class="absolute top-0 left-0 w-40 h-40 bg-white/5 rounded-full -translate-x-1/2 -translate-y-1/2"></div>
                    <div class="absolute bottom-12 right-0 w-24 h-24 bg-white/5 rounded-full translate-x-1/2"></div>
                </div>
                <div class="relative z-10 text-center px-4 pt-6 pb-0">
                    <p class="text-white font-black text-lg leading-snug">"Hai! Yuk masuk<br>dan catat olahraga<br>daerahmu!"</p>
                </div>
                <img src="/storage/karakter/3.png" alt="Karakter"
                     class="relative z-10 w-full max-w-[190px] object-contain object-bottom select-none">
            </div>

            {{-- Kanan: Form --}}
            <div class="flex-1 p-7 relative">
                <button @click="modal = ''"
                    class="absolute top-4 right-4 w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-400 hover:bg-red-50 hover:text-red-500 transition text-xs">
                    <i class="fas fa-times"></i>
                </button>
                <h3 class="text-2xl font-black text-gray-900 mb-1">Masuk</h3>
                <p class="text-gray-500 text-sm mb-5">Masuk ke akun Dataraga kamu.</p>

                @if ($errors->any() && !old('name'))
                    <div class="mb-4 flex items-center gap-2 p-3 bg-red-50 text-red-700 rounded-xl text-sm border border-red-100">
                        <i class="fas fa-exclamation-circle text-red-400"></i> Email atau kata sandi tidak sesuai.
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
                                class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-sm">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Kata Sandi</label>
                        <div class="relative">
                            <i class="fas fa-lock absolute left-3.5 top-3 text-gray-400 text-sm"></i>
                            <input type="password" name="password" required placeholder="••••••••"
                                class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-sm">
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2 text-sm text-gray-500 cursor-pointer">
                            <input type="checkbox" name="remember" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            Ingat saya
                        </label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-xs text-blue-600 font-semibold hover:underline">Lupa sandi?</a>
                        @endif
                    </div>
                    <button type="submit"
                        class="w-full py-3 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 shadow-lg transition active:scale-95 text-sm">
                        Masuk Sekarang
                    </button>
                </form>
                <p class="mt-5 text-center text-xs text-gray-500">
                    Belum punya akun?
                    <button @click="modal = 'register'" class="text-blue-600 font-bold hover:underline">Daftar di sini</button>
                </p>
            </div>
        </div>
    </div>

    <!-- ================================================================
         REGISTER MODAL
    ================================================================ -->
    <div x-show="modal === 'register'" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/70 backdrop-blur-sm">
        <div x-show="modal === 'register'" @click.away="modal = ''"
             x-transition:enter="transition ease-out duration-250"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="bg-white rounded-3xl shadow-2xl w-full max-w-2xl overflow-hidden flex max-h-[95vh]">

            {{-- Kiri: karakter GIRL --}}
            <div class="hidden sm:flex w-[40%] relative overflow-hidden flex-col items-center justify-end"
                 style="background: linear-gradient(160deg,#0f766e 0%,#0d9488 50%,#059669 100%)">
                <div class="absolute inset-0 pointer-events-none">
                    <div class="absolute top-0 right-0 w-40 h-40 bg-white/5 rounded-full translate-x-1/2 -translate-y-1/2"></div>
                    <div class="absolute bottom-12 left-0 w-24 h-24 bg-white/5 rounded-full -translate-x-1/2"></div>
                </div>
                <div class="relative z-10 text-center px-4 pt-6 pb-0">
                    <p class="text-white font-black text-lg leading-snug">"Daftar sekarang<br>dan jadilah bagian<br>gerakan olahraga!"</p>
                </div>
                <img src="/storage/karakter/2.png" alt="Karakter"
                     class="relative z-10 w-full max-w-[190px] object-contain object-bottom select-none">
            </div>

            {{-- Kanan: Form --}}
            <div class="flex-1 p-7 overflow-y-auto relative">
                <button @click="modal = ''"
                    class="absolute top-4 right-4 w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-400 hover:bg-red-50 hover:text-red-500 transition text-xs">
                    <i class="fas fa-times"></i>
                </button>
                <h3 class="text-2xl font-black text-gray-900 mb-1">Daftar Relawan</h3>
                <p class="text-gray-500 text-sm mb-5">Bergabunglah menjadi penggerak olahraga daerah.</p>

                @if ($errors->any() && old('name'))
                    <div class="mb-4 flex items-center gap-2 p-3 bg-red-50 text-red-700 rounded-xl text-sm border border-red-100">
                        <i class="fas fa-exclamation-circle text-red-400"></i> Mohon periksa kembali isian Anda.
                    </div>
                @endif

                <form method="POST" action="{{ route('register') }}" class="space-y-3.5">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nama Lengkap</label>
                        <div class="relative">
                            <i class="fas fa-user absolute left-3.5 top-3 text-gray-400 text-sm"></i>
                            <input type="text" name="name" value="{{ old('name') }}" required autofocus
                                placeholder="Nama lengkap kamu"
                                class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition text-sm">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Email Aktif</label>
                        <div class="relative">
                            <i class="fas fa-envelope absolute left-3.5 top-3 text-gray-400 text-sm"></i>
                            <input type="email" name="email" value="{{ old('email') }}" required
                                placeholder="email@kamu.com"
                                class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition text-sm">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Kata Sandi</label>
                        <div class="relative">
                            <i class="fas fa-lock absolute left-3.5 top-3 text-gray-400 text-sm"></i>
                            <input type="password" name="password" required placeholder="Min. 8 karakter"
                                class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition text-sm">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Konfirmasi Sandi</label>
                        <div class="relative">
                            <i class="fas fa-check-double absolute left-3.5 top-3 text-gray-400 text-sm"></i>
                            <input type="password" name="password_confirmation" required placeholder="Ulangi sandi"
                                class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition text-sm">
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
