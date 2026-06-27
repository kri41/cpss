<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CPSS - Kolaborasi Olahraga Daerah</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        [x-cloak] { display: none !important; }
        .text-gradient {
            background: linear-gradient(135deg, #1e40af, #0ea5e9);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>
</head>
<body class="antialiased font-sans bg-white text-gray-800"
    x-data="{ modal: '{{ $errors->any() ? (old('name') ? 'register' : 'login') : '' }}' }">

    <!-- Navbar -->
    <nav class="fixed w-full z-40 top-0 bg-white/90 backdrop-blur-md border-b border-gray-100 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <a href="{{ url('/') }}" class="flex items-center gap-3">
                    <div class="w-9 h-9 bg-gradient-to-br from-blue-600 to-sky-400 rounded-lg shadow-md flex items-center justify-center text-white font-bold text-lg">C</div>
                    <span class="font-bold text-xl text-gray-900 tracking-tight">CPSS</span>
                </a>

                <!-- Menu Tengah -->
                <div class="hidden md:flex items-center space-x-1">
                    <a href="{{ route('prasarana.index') }}" class="px-3 py-2 text-sm font-medium text-gray-600 hover:text-blue-600 rounded-lg hover:bg-blue-50/50 transition">Prasarana</a>
                    <a href="{{ route('events.index') }}" class="px-3 py-2 text-sm font-medium text-gray-600 hover:text-blue-600 rounded-lg hover:bg-blue-50/50 transition">Event</a>
                    <a href="{{ route('clubs.index') }}" class="px-3 py-2 text-sm font-medium text-gray-600 hover:text-blue-600 rounded-lg hover:bg-blue-50/50 transition">Klub</a>
                    <a href="{{ route('kalender.index') }}" class="px-3 py-2 text-sm font-medium text-gray-600 hover:text-blue-600 rounded-lg hover:bg-blue-50/50 transition">Kalender</a>
                </div>

                <!-- Auth Kanan -->
                <div class="hidden md:flex items-center space-x-3">
                    @guest
                        <button @click="modal = 'login'" class="text-gray-600 hover:text-blue-600 font-medium px-4 py-2 transition cursor-pointer text-sm">Masuk</button>
                        <button @click="modal = 'register'" class="px-5 py-2 bg-blue-600 text-white font-medium rounded-full hover:bg-blue-700 transition-all duration-300 text-sm cursor-pointer">Daftar Relawan</button>
                    @else
                        <span class="text-gray-500 text-sm mr-2">Halo, {{ Auth::user()->name }}</span>
                        <a href="{{ url('/dashboard') }}" class="px-5 py-2 bg-gradient-to-r from-blue-600 to-sky-500 text-white font-medium rounded-full shadow-md hover:shadow-lg transition-all duration-300 text-sm">Buka Dasbor</a>
                    @endguest
                </div>

                <!-- Mobile menu button -->
                <div class="md:hidden flex items-center" x-data="{ mobileOpen: false }">
                    <button @click="mobileOpen = !mobileOpen" class="p-2 rounded-lg text-gray-600 hover:bg-gray-100">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                    </button>
                    <div x-show="mobileOpen" @click.away="mobileOpen = false" x-cloak x-transition class="absolute top-16 left-0 right-0 bg-white border-b border-gray-200 shadow-lg p-4 space-y-2">
                        <a href="{{ route('prasarana.index') }}" class="block px-4 py-2 text-sm font-medium text-gray-700 hover:bg-blue-50 rounded-lg">Prasarana</a>
                        <a href="{{ route('events.index') }}" class="block px-4 py-2 text-sm font-medium text-gray-700 hover:bg-blue-50 rounded-lg">Event</a>
                        <a href="{{ route('clubs.index') }}" class="block px-4 py-2 text-sm font-medium text-gray-700 hover:bg-blue-50 rounded-lg">Klub</a>
                        <a href="{{ route('kalender.index') }}" class="block px-4 py-2 text-sm font-medium text-gray-700 hover:bg-blue-50 rounded-lg">Kalender</a>
                        <div class="pt-2 border-t border-gray-100">
                            @guest
                                <button @click="modal = 'login'; mobileOpen = false" class="block w-full text-left px-4 py-2 text-sm font-medium text-gray-700 hover:bg-blue-50 rounded-lg">Masuk</button>
                                <button @click="modal = 'register'; mobileOpen = false" class="block w-full text-left px-4 py-2 text-sm font-medium text-blue-600 hover:bg-blue-50 rounded-lg">Daftar Relawan</button>
                            @else
                                <a href="{{ url('/dashboard') }}" class="block px-4 py-2 text-sm font-medium text-blue-600 hover:bg-blue-50 rounded-lg">Buka Dasbor</a>
                            @endguest
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative pt-28 pb-20 md:pt-36 md:pb-28 overflow-hidden min-h-[90vh] flex items-center">
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute top-0 right-0 w-1/2 h-full bg-gradient-to-bl from-blue-50 to-transparent opacity-70"></div>
            <div class="absolute bottom-0 left-0 w-96 h-96 bg-sky-100 rounded-full mix-blend-multiply filter blur-3xl opacity-40"></div>
        </div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <!-- Kiri: Headline -->
                <div>
                    <span class="inline-block px-4 py-1.5 rounded-full bg-blue-50 text-blue-700 text-xs font-semibold tracking-wide mb-6 border border-blue-100">PLATFORM KEOLAHRAGAAN BERBASIS BUKTI</span>
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold text-gray-900 leading-tight mb-6">
                        Satu Data <br><span class="text-gradient">Untuk Olahraga Daerah</span>
                    </h1>
                    <p class="text-lg text-gray-600 leading-relaxed mb-8 max-w-lg">
                        CPSS adalah ruang kolaborasi digital bagi para penggerak olahraga di seluruh Indonesia. Laporkan fasilitas, bagikan aktivitas komunitas, dan temukan data keolahragaan daerah Anda.
                    </p>
                    <div class="flex flex-wrap gap-4">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="px-8 py-3.5 bg-blue-600 text-white font-semibold rounded-xl shadow-lg hover:bg-blue-700 transition-all duration-300">Masuk ke Dasbor</a>
                        @else
                            <button @click="modal = 'register'" class="px-8 py-3.5 bg-blue-600 text-white font-semibold rounded-xl shadow-lg hover:bg-blue-700 transition-all duration-300">Mulai Berkontribusi</button>
                            <button @click="modal = 'login'" class="px-8 py-3.5 bg-white text-gray-700 font-semibold rounded-xl border border-gray-200 hover:border-blue-300 hover:text-blue-600 transition-all duration-300">Masuk sebagai Relawan</button>
                        @endauth
                    </div>
                </div>

                <!-- Kanan: Menu Card + Statistik -->
                <div class="hidden lg:block">
                    <div class="relative">
                        <div class="absolute -inset-4 bg-gradient-to-r from-blue-100 to-sky-100 rounded-3xl transform rotate-3"></div>
                        <div class="relative bg-white rounded-2xl shadow-xl border border-gray-100 p-6 space-y-5">
                            <!-- Statistik Bar -->
                            <div class="grid grid-cols-3 gap-3 pb-5 border-b border-gray-100">
                                <div class="text-center">
                                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['totalPrasarana']) }}</p>
                                    <p class="text-xs text-gray-500 mt-0.5">Prasarana</p>
                                </div>
                                <div class="text-center border-x border-gray-100">
                                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['totalEvents']) }}</p>
                                    <p class="text-xs text-gray-500 mt-0.5">Event</p>
                                </div>
                                <div class="text-center">
                                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['totalClubs']) }}</p>
                                    <p class="text-xs text-gray-500 mt-0.5">Klub Aktif</p>
                                </div>
                            </div>

                            <!-- Menu Cards -->
                            <a href="{{ route('prasarana.index') }}" class="flex items-center gap-4 p-4 bg-blue-50 rounded-xl hover:bg-blue-100 transition group">
                                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center text-blue-600 group-hover:bg-white transition"><i class="fas fa-building text-xl"></i></div>
                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-gray-900">Jelajahi Prasarana</p>
                                    <p class="text-xs text-gray-500">{{ $stats['totalPrasarana'] }} fasilitas tervalidasi</p>
                                </div>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 group-hover:text-blue-600 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                            </a>

                            <a href="{{ route('events.index') }}" class="flex items-center gap-4 p-4 bg-sky-50 rounded-xl hover:bg-sky-100 transition group">
                                <div class="w-12 h-12 bg-sky-100 rounded-xl flex items-center justify-center text-sky-600 group-hover:bg-white transition"><i class="fas fa-calendar-alt text-xl"></i></div>
                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-gray-900">Lihat Event</p>
                                    <p class="text-xs text-gray-500">{{ $stats['totalEvents'] }} event tervalidasi</p>
                                </div>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 group-hover:text-sky-600 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                            </a>

                            <a href="{{ route('clubs.index') }}" class="flex items-center gap-4 p-4 bg-indigo-50 rounded-xl hover:bg-indigo-100 transition group">
                                <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center text-indigo-600 group-hover:bg-white transition"><i class="fas fa-shield-alt text-xl"></i></div>
                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-gray-900">Temukan Klub</p>
                                    <p class="text-xs text-gray-500">{{ $stats['totalClubs'] }} klub aktif</p>
                                </div>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 group-hover:text-indigo-600 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                            </a>

                            <a href="{{ route('kalender.index') }}" class="flex items-center gap-4 p-4 bg-emerald-50 rounded-xl hover:bg-emerald-100 transition group">
                                <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center text-emerald-600 group-hover:bg-white transition"><i class="fas fa-calendar-check text-xl"></i></div>
                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-gray-900">Kalender Kegiatan</p>
                                    <p class="text-xs text-gray-500">Jadwal event & latihan</p>
                                </div>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 group-hover:text-emerald-600 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Mobile Menu Cards (tampil di bawah hero untuk mobile) -->
    <section class="lg:hidden pb-12 px-4 sm:px-6">
        <div class="max-w-xl mx-auto space-y-3">
            <div class="grid grid-cols-3 gap-3 mb-4">
                <div class="bg-white rounded-xl p-3 text-center border border-gray-100 shadow-sm">
                    <p class="text-xl font-bold text-gray-900">{{ number_format($stats['totalPrasarana']) }}</p>
                    <p class="text-[10px] text-gray-500 uppercase tracking-wide">Prasarana</p>
                </div>
                <div class="bg-white rounded-xl p-3 text-center border border-gray-100 shadow-sm">
                    <p class="text-xl font-bold text-gray-900">{{ number_format($stats['totalEvents']) }}</p>
                    <p class="text-[10px] text-gray-500 uppercase tracking-wide">Event</p>
                </div>
                <div class="bg-white rounded-xl p-3 text-center border border-gray-100 shadow-sm">
                    <p class="text-xl font-bold text-gray-900">{{ number_format($stats['totalClubs']) }}</p>
                    <p class="text-[10px] text-gray-500 uppercase tracking-wide">Klub</p>
                </div>
            </div>
            <a href="{{ route('prasarana.index') }}" class="flex items-center gap-4 p-4 bg-blue-50 rounded-xl hover:bg-blue-100 transition">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center text-blue-600"><i class="fas fa-building"></i></div>
                <div class="flex-1"><p class="text-sm font-semibold text-gray-900">Jelajahi Prasarana</p></div>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
            </a>
            <a href="{{ route('events.index') }}" class="flex items-center gap-4 p-4 bg-sky-50 rounded-xl hover:bg-sky-100 transition">
                <div class="w-10 h-10 bg-sky-100 rounded-lg flex items-center justify-center text-sky-600"><i class="fas fa-calendar-alt"></i></div>
                <div class="flex-1"><p class="text-sm font-semibold text-gray-900">Lihat Event</p></div>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
            </a>
            <a href="{{ route('clubs.index') }}" class="flex items-center gap-4 p-4 bg-indigo-50 rounded-xl hover:bg-indigo-100 transition">
                <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center text-indigo-600"><i class="fas fa-shield-alt"></i></div>
                <div class="flex-1"><p class="text-sm font-semibold text-gray-900">Temukan Klub</p></div>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
            </a>
            <a href="{{ route('kalender.index') }}" class="flex items-center gap-4 p-4 bg-emerald-50 rounded-xl hover:bg-emerald-100 transition">
                <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center text-emerald-600"><i class="fas fa-calendar-check"></i></div>
                <div class="flex-1"><p class="text-sm font-semibold text-gray-900">Kalender Kegiatan</p></div>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-400 py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-sky-400 rounded-lg flex items-center justify-center text-white font-bold text-sm">C</div>
                    <span class="text-white font-semibold">CPSS</span>
                </div>
                <p class="text-sm text-center md:text-right">&copy; {{ date('Y') }} Cloud-Participatory Sport Sensing. Platform Kolaborasi Keolahragaan Daerah.</p>
            </div>
        </div>
    </footer>

    <!-- Login Modal -->
    <div x-show="modal === 'login'" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm">
        <div x-show="modal === 'login'" @click.away="modal = ''" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95 translate-y-4" x-transition:enter-end="opacity-100 scale-100 translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100 translate-y-0" x-transition:leave-end="opacity-0 scale-95 translate-y-4" class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden relative">
            <button @click="modal = ''" class="absolute top-4 right-4 text-gray-400 hover:text-red-500 transition text-xl w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center"><i class="fas fa-times"></i></button>
            <div class="p-8">
                <h3 class="text-2xl font-bold text-gray-900 mb-2">Masuk ke CPSS</h3>
                <p class="text-gray-500 text-sm mb-6">Silakan masuk untuk mulai memetakan data olahraga.</p>
                @if ($errors->any() && !old('name'))
                    <div class="mb-4 p-3 bg-red-50 text-red-600 rounded-lg text-sm border border-red-200">Email atau kata sandi tidak sesuai.</div>
                @endif
                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <div class="relative">
                            <i class="fas fa-envelope absolute left-4 top-3.5 text-gray-400"></i>
                            <input type="email" name="email" value="{{ old('email') }}" required autofocus class="w-full pl-11 pr-4 py-3 rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-sm">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kata Sandi</label>
                        <div class="relative">
                            <i class="fas fa-lock absolute left-4 top-3.5 text-gray-400"></i>
                            <input type="password" name="password" required class="w-full pl-11 pr-4 py-3 rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-sm">
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <label class="flex items-center">
                            <input type="checkbox" name="remember" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-600">Ingat Saya</span>
                        </label>
                    </div>
                    <button type="submit" class="w-full py-3.5 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 shadow-lg hover:shadow-blue-200 transition-all active:scale-95">Masuk Sistem</button>
                </form>
                <p class="mt-6 text-center text-sm text-gray-500">Belum punya akun? <button @click="modal = 'register'" class="text-blue-600 font-semibold hover:underline">Daftar di sini</button></p>
            </div>
        </div>
    </div>

    <!-- Register Modal -->
    <div x-show="modal === 'register'" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm">
        <div x-show="modal === 'register'" @click.away="modal = ''" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95 translate-y-4" x-transition:enter-end="opacity-100 scale-100 translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100 translate-y-0" x-transition:leave-end="opacity-0 scale-95 translate-y-4" class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden relative max-h-[90vh] overflow-y-auto">
            <button @click="modal = ''" class="absolute top-4 right-4 text-gray-400 hover:text-red-500 transition text-xl w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center"><i class="fas fa-times"></i></button>
            <div class="p-8">
                <h3 class="text-2xl font-bold text-gray-900 mb-2">Daftar Relawan CPSS</h3>
                <p class="text-gray-500 text-sm mb-6">Bergabunglah menjadi penggerak olahraga daerah.</p>
                @if ($errors->any() && old('name'))
                    <div class="mb-4 p-3 bg-red-50 text-red-600 rounded-lg text-sm border border-red-200">Mohon periksa kembali isian Anda. (Mungkin email sudah terdaftar).</div>
                @endif
                <form method="POST" action="{{ route('register') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                        <div class="relative">
                            <i class="fas fa-user absolute left-4 top-3.5 text-gray-400"></i>
                            <input type="text" name="name" value="{{ old('name') }}" required autofocus class="w-full pl-11 pr-4 py-3 rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-sm">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email Aktif</label>
                        <div class="relative">
                            <i class="fas fa-envelope absolute left-4 top-3.5 text-gray-400"></i>
                            <input type="email" name="email" value="{{ old('email') }}" required class="w-full pl-11 pr-4 py-3 rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-sm">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kata Sandi</label>
                        <div class="relative">
                            <i class="fas fa-lock absolute left-4 top-3.5 text-gray-400"></i>
                            <input type="password" name="password" required class="w-full pl-11 pr-4 py-3 rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-sm">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Sandi</label>
                        <div class="relative">
                            <i class="fas fa-check-double absolute left-4 top-3.5 text-gray-400"></i>
                            <input type="password" name="password_confirmation" required class="w-full pl-11 pr-4 py-3 rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-sm">
                        </div>
                    </div>
                    <button type="submit" class="w-full py-3.5 mt-2 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 shadow-lg hover:shadow-blue-200 transition-all active:scale-95">Daftar & Langsung Masuk</button>
                </form>
                <p class="mt-6 text-center text-sm text-gray-500">Sudah mendaftar sebelumnya? <button @click="modal = 'login'" class="text-blue-600 font-semibold hover:underline">Log In di sini</button></p>
            </div>
        </div>
    </div>

</body>
</html>
