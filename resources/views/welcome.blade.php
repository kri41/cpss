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
        @media (min-width: 1024px) {
            .landing-desktop { height: 100vh; overflow: hidden; }
        }
    </style>
</head>
<body class="antialiased font-sans bg-white text-gray-800 landing-desktop"
    x-data="{ modal: '{{ $errors->any() ? (old('name') ? 'register' : 'login') : '' }}' }">

    <!-- Navbar Minimal -->
    <nav class="fixed w-full z-40 top-0 bg-white/90 backdrop-blur-md border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-14">
                <!-- Logo -->
                <a href="{{ url('/') }}" class="flex items-center gap-2.5">
                    <div class="w-8 h-8 bg-gradient-to-br from-blue-600 to-sky-400 rounded-lg shadow-sm flex items-center justify-center text-white font-bold text-sm">C</div>
                    <span class="font-bold text-lg text-gray-900 tracking-tight">CPSS</span>
                </a>

                <!-- Auth Kanan -->
                <div class="flex items-center space-x-2">
                    @guest
                        <button @click="modal = 'login'" class="text-gray-600 hover:text-blue-600 font-medium px-3 py-1.5 transition cursor-pointer text-sm">Masuk</button>
                        <button @click="modal = 'register'" class="px-4 py-1.5 bg-blue-600 text-white font-medium rounded-full hover:bg-blue-700 transition-all duration-300 text-sm cursor-pointer">Daftar</button>
                    @else
                        <span class="text-gray-500 text-sm hidden sm:inline mr-1">Halo, {{ Auth::user()->name }}</span>
                        <a href="{{ url('/dashboard') }}" class="px-4 py-1.5 bg-gradient-to-r from-blue-600 to-sky-500 text-white font-medium rounded-full shadow-sm hover:shadow-md transition-all duration-300 text-sm">Dasbor</a>
                    @endguest
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section — Fullscreen Desktop -->
    <section class="relative pt-14 lg:pt-0 lg:h-screen lg:flex lg:items-center overflow-hidden">
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute top-0 right-0 w-1/2 h-full bg-gradient-to-bl from-blue-50 to-transparent opacity-60"></div>
            <div class="absolute bottom-0 left-0 w-72 h-72 bg-sky-100 rounded-full mix-blend-multiply filter blur-3xl opacity-30"></div>
        </div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full py-10 lg:py-0">
            <div class="grid lg:grid-cols-2 gap-8 lg:gap-12 items-center">
                <!-- Kiri: Headline -->
                <div class="text-center lg:text-left">
                    <span class="inline-block px-3 py-1 rounded-full bg-blue-50 text-blue-700 text-[11px] font-semibold tracking-wide mb-4 border border-blue-100">PLATFORM KEOLAHRAGAAN BERBASIS BUKTI</span>
                    <h1 class="text-3xl sm:text-4xl lg:text-[3.25rem] font-extrabold text-gray-900 leading-[1.15] mb-4">
                        Satu Data <br><span class="text-gradient">Untuk Olahraga Daerah</span>
                    </h1>
                    <p class="text-base text-gray-600 leading-relaxed mb-6 max-w-md mx-auto lg:mx-0">
                        Ruang kolaborasi digital bagi penggerak olahraga. Laporkan fasilitas, bagikan aktivitas komunitas, dan temukan data keolahragaan daerah Anda.
                    </p>
                    <div class="flex flex-wrap gap-3 justify-center lg:justify-start">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="px-6 py-2.5 bg-blue-600 text-white font-semibold rounded-xl shadow-lg hover:bg-blue-700 transition-all text-sm">Masuk ke Dasbor</a>
                        @else
                            <button @click="modal = 'register'" class="px-6 py-2.5 bg-blue-600 text-white font-semibold rounded-xl shadow-lg hover:bg-blue-700 transition-all text-sm">Mulai Berkontribusi</button>
                            <button @click="modal = 'login'" class="px-6 py-2.5 bg-white text-gray-700 font-semibold rounded-xl border border-gray-200 hover:border-blue-300 hover:text-blue-600 transition-all text-sm">Masuk</button>
                        @endauth
                    </div>
                </div>

                <!-- Kanan: Compact Menu Card -->
                <div class="hidden lg:block">
                    <div class="relative max-w-sm mx-auto">
                        <div class="absolute -inset-3 bg-gradient-to-r from-blue-100 to-sky-100 rounded-2xl transform rotate-2"></div>
                        <div class="relative bg-white rounded-xl shadow-lg border border-gray-100 p-5 space-y-3">
                            <!-- Compact Stat -->
                            <div class="grid grid-cols-3 gap-2 pb-3 border-b border-gray-100">
                                <div class="text-center">
                                    <p class="text-lg font-bold text-gray-900 leading-none">{{ number_format($stats['totalPrasarana']) }}</p>
                                    <p class="text-[10px] text-gray-500 mt-1 uppercase tracking-wide">Prasarana</p>
                                </div>
                                <div class="text-center border-x border-gray-100">
                                    <p class="text-lg font-bold text-gray-900 leading-none">{{ number_format($stats['totalEvents']) }}</p>
                                    <p class="text-[10px] text-gray-500 mt-1 uppercase tracking-wide">Event</p>
                                </div>
                                <div class="text-center">
                                    <p class="text-lg font-bold text-gray-900 leading-none">{{ number_format($stats['totalClubs']) }}</p>
                                    <p class="text-[10px] text-gray-500 mt-1 uppercase tracking-wide">Klub</p>
                                </div>
                            </div>

                            <!-- Menu Items -->
                            <a href="{{ route('prasarana.index') }}" class="flex items-center gap-3 p-3 bg-blue-50 rounded-lg hover:bg-blue-100 transition group">
                                <div class="w-9 h-9 bg-blue-100 rounded-lg flex items-center justify-center text-blue-600 text-sm group-hover:bg-white transition"><i class="fas fa-building"></i></div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-900">Prasarana</p>
                                    <p class="text-[11px] text-gray-500">{{ $stats['totalPrasarana'] }} fasilitas tervalidasi</p>
                                </div>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 group-hover:text-blue-600 transition shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                            </a>

                            <a href="{{ route('events.index') }}" class="flex items-center gap-3 p-3 bg-sky-50 rounded-lg hover:bg-sky-100 transition group">
                                <div class="w-9 h-9 bg-sky-100 rounded-lg flex items-center justify-center text-sky-600 text-sm group-hover:bg-white transition"><i class="fas fa-calendar-alt"></i></div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-900">Event</p>
                                    <p class="text-[11px] text-gray-500">{{ $stats['totalEvents'] }} event tervalidasi</p>
                                </div>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 group-hover:text-sky-600 transition shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                            </a>

                            <a href="{{ route('clubs.index') }}" class="flex items-center gap-3 p-3 bg-indigo-50 rounded-lg hover:bg-indigo-100 transition group">
                                <div class="w-9 h-9 bg-indigo-100 rounded-lg flex items-center justify-center text-indigo-600 text-sm group-hover:bg-white transition"><i class="fas fa-shield-alt"></i></div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-900">Klub</p>
                                    <p class="text-[11px] text-gray-500">{{ $stats['totalClubs'] }} klub aktif</p>
                                </div>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 group-hover:text-indigo-600 transition shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                            </a>

                            <a href="{{ route('kalender.index') }}" class="flex items-center gap-3 p-3 bg-emerald-50 rounded-lg hover:bg-emerald-100 transition group">
                                <div class="w-9 h-9 bg-emerald-100 rounded-lg flex items-center justify-center text-emerald-600 text-sm group-hover:bg-white transition"><i class="fas fa-calendar-check"></i></div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-900">Kalender</p>
                                    <p class="text-[11px] text-gray-500">Jadwal & kegiatan</p>
                                </div>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 group-hover:text-emerald-600 transition shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Mobile Menu Cards (scrollable) -->
    <section class="lg:hidden pb-8 px-4 sm:px-6">
        <div class="max-w-sm mx-auto space-y-2.5">
            <div class="grid grid-cols-3 gap-2 mb-3">
                <div class="bg-white rounded-lg p-2.5 text-center border border-gray-100 shadow-sm">
                    <p class="text-lg font-bold text-gray-900">{{ number_format($stats['totalPrasarana']) }}</p>
                    <p class="text-[10px] text-gray-500 uppercase tracking-wide">Prasarana</p>
                </div>
                <div class="bg-white rounded-lg p-2.5 text-center border border-gray-100 shadow-sm">
                    <p class="text-lg font-bold text-gray-900">{{ number_format($stats['totalEvents']) }}</p>
                    <p class="text-[10px] text-gray-500 uppercase tracking-wide">Event</p>
                </div>
                <div class="bg-white rounded-lg p-2.5 text-center border border-gray-100 shadow-sm">
                    <p class="text-lg font-bold text-gray-900">{{ number_format($stats['totalClubs']) }}</p>
                    <p class="text-[10px] text-gray-500 uppercase tracking-wide">Klub</p>
                </div>
            </div>
            <a href="{{ route('prasarana.index') }}" class="flex items-center gap-3 p-3 bg-blue-50 rounded-lg hover:bg-blue-100 transition">
                <div class="w-9 h-9 bg-blue-100 rounded-lg flex items-center justify-center text-blue-600 text-sm"><i class="fas fa-building"></i></div>
                <div class="flex-1"><p class="text-sm font-semibold text-gray-900">Prasarana</p></div>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
            </a>
            <a href="{{ route('events.index') }}" class="flex items-center gap-3 p-3 bg-sky-50 rounded-lg hover:bg-sky-100 transition">
                <div class="w-9 h-9 bg-sky-100 rounded-lg flex items-center justify-center text-sky-600 text-sm"><i class="fas fa-calendar-alt"></i></div>
                <div class="flex-1"><p class="text-sm font-semibold text-gray-900">Event</p></div>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
            </a>
            <a href="{{ route('clubs.index') }}" class="flex items-center gap-3 p-3 bg-indigo-50 rounded-lg hover:bg-indigo-100 transition">
                <div class="w-9 h-9 bg-indigo-100 rounded-lg flex items-center justify-center text-indigo-600 text-sm"><i class="fas fa-shield-alt"></i></div>
                <div class="flex-1"><p class="text-sm font-semibold text-gray-900">Klub</p></div>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
            </a>
            <a href="{{ route('kalender.index') }}" class="flex items-center gap-3 p-3 bg-emerald-50 rounded-lg hover:bg-emerald-100 transition">
                <div class="w-9 h-9 bg-emerald-100 rounded-lg flex items-center justify-center text-emerald-600 text-sm"><i class="fas fa-calendar-check"></i></div>
                <div class="flex-1"><p class="text-sm font-semibold text-gray-900">Kalender</p></div>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
            </a>
        </div>
    </section>

    <!-- Footer — Mobile Only -->
    <footer class="lg:hidden bg-gray-900 text-gray-400 py-6 mt-4">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="flex items-center justify-center gap-3 mb-2">
                <div class="w-7 h-7 bg-gradient-to-br from-blue-500 to-sky-400 rounded-lg flex items-center justify-center text-white font-bold text-xs">C</div>
                <span class="text-white font-semibold text-sm">CPSS</span>
            </div>
            <p class="text-xs">&copy; {{ date('Y') }} Cloud-Participatory Sport Sensing</p>
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
                    <div class="mb-4 p-3 bg-red-50 text-red-600 rounded-lg text-sm border border-red-200">Mohon periksa kembali isian Anda.</div>
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
                <p class="mt-6 text-center text-sm text-gray-500">Sudah mendaftar? <button @click="modal = 'login'" class="text-blue-600 font-semibold hover:underline">Log In di sini</button></p>
            </div>
        </div>
    </div>

</body>
</html>
