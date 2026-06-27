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
                <div class="flex-shrink-0 flex items-center gap-3">
                    <div class="w-9 h-9 bg-gradient-to-br from-blue-600 to-sky-400 rounded-lg shadow-md flex items-center justify-center text-white font-bold text-lg">C</div>
                    <span class="font-bold text-xl text-gray-900 tracking-tight">CPSS</span>
                </div>
                <div class="hidden md:flex items-center space-x-3">
                    @guest
                        <button @click="modal = 'login'" class="text-gray-600 hover:text-blue-600 font-medium px-4 py-2 transition cursor-pointer text-sm">Masuk</button>
                        <button @click="modal = 'register'" class="px-5 py-2 bg-blue-600 text-white font-medium rounded-full hover:bg-blue-700 transition-all duration-300 text-sm cursor-pointer">Daftar Relawan</button>
                    @else
                        <span class="text-gray-500 text-sm mr-2">Halo, {{ Auth::user()->name }}</span>
                        <a href="{{ url('/dashboard') }}" class="px-5 py-2 bg-gradient-to-r from-blue-600 to-sky-500 text-white font-medium rounded-full shadow-md hover:shadow-lg transition-all duration-300 text-sm">Buka Dasbor</a>
                    @endguest
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative pt-28 pb-16 md:pt-36 md:pb-24 overflow-hidden">
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute top-0 right-0 w-1/2 h-full bg-gradient-to-bl from-blue-50 to-transparent opacity-70"></div>
            <div class="absolute bottom-0 left-0 w-96 h-96 bg-sky-100 rounded-full mix-blend-multiply filter blur-3xl opacity-40"></div>
        </div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <div>
                    <span class="inline-block px-4 py-1.5 rounded-full bg-blue-50 text-blue-700 text-xs font-semibold tracking-wide mb-6 border border-blue-100">PLATFORM KEOLAHRAGAAN BERBASIS BUKTI</span>
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold text-gray-900 leading-tight mb-6">
                        Satu Data <br><span class="text-gradient">Untuk Olahraga Daerah</span>
                    </h1>
                    <p class="text-lg text-gray-600 leading-relaxed mb-8 max-w-lg">
                        CPSS adalah ruang kolaborasi digital bagi para penggerak olahraga di seluruh Indonesia. Laporkan fasilitas, bagikan aktivitas komunitas, dan temukan data keolahragaan daerah Anda — semua dalam satu platform terintegrasi.
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
                <div class="hidden lg:block">
                    <div class="relative">
                        <div class="absolute -inset-4 bg-gradient-to-r from-blue-100 to-sky-100 rounded-3xl transform rotate-3"></div>
                        <div class="relative bg-white rounded-2xl shadow-xl border border-gray-100 p-6 space-y-4">
                            <div class="flex items-center gap-4 p-4 bg-blue-50 rounded-xl">
                                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center text-blue-600"><i class="fas fa-building text-xl"></i></div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">Lapangan Sepak Bola</p>
                                    <p class="text-xs text-gray-500">Baru dilaporkan di Banyuwangi</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-4 p-4 bg-sky-50 rounded-xl">
                                <div class="w-12 h-12 bg-sky-100 rounded-lg flex items-center justify-center text-sky-600"><i class="fas fa-users text-xl"></i></div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">Fun Run Glagah</p>
                                    <p class="text-xs text-gray-500">120 orang berpartisipasi</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-4 p-4 bg-indigo-50 rounded-xl">
                                <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center text-indigo-600"><i class="fas fa-shield-alt text-xl"></i></div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">Persisaga Banyuwangi</p>
                                    <p class="text-xs text-gray-500">Klub aktif dengan 45 anggota</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Statistics Section -->
    <section class="bg-gray-50 py-12 border-y border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <div class="bg-white rounded-2xl p-6 text-center shadow-sm border border-gray-100 hover:shadow-md transition-all">
                    <div class="w-12 h-12 mx-auto mb-3 bg-blue-100 rounded-xl flex items-center justify-center text-blue-600 text-xl"><i class="fas fa-building"></i></div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-1">{{ number_format($stats['totalPrasarana']) }}</h3>
                    <p class="text-gray-500 text-sm">Prasarana Tercatat</p>
                </div>
                <div class="bg-white rounded-2xl p-6 text-center shadow-sm border border-gray-100 hover:shadow-md transition-all">
                    <div class="w-12 h-12 mx-auto mb-3 bg-sky-100 rounded-xl flex items-center justify-center text-sky-600 text-xl"><i class="fas fa-users"></i></div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-1">{{ number_format($stats['totalClubs']) }}</h3>
                    <p class="text-gray-500 text-sm">Klub & Komunitas</p>
                </div>
                <div class="bg-white rounded-2xl p-6 text-center shadow-sm border border-gray-100 hover:shadow-md transition-all">
                    <div class="w-12 h-12 mx-auto mb-3 bg-indigo-100 rounded-xl flex items-center justify-center text-indigo-600 text-xl"><i class="fas fa-calendar-alt"></i></div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-1">{{ number_format($stats['totalEvents']) }}</h3>
                    <p class="text-gray-500 text-sm">Event Tercatat</p>
                </div>
                <div class="bg-white rounded-2xl p-6 text-center shadow-sm border border-gray-100 hover:shadow-md transition-all">
                    <div class="w-12 h-12 mx-auto mb-3 bg-emerald-100 rounded-xl flex items-center justify-center text-emerald-600 text-xl"><i class="fas fa-running"></i></div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-1">{{ number_format($stats['totalPartisipasi']) }}</h3>
                    <p class="text-gray-500 text-sm">Partisipasi Masyarakat</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Latest Prasarana -->
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-1">Prasarana Terbaru</h2>
                    <p class="text-gray-500">Fasilitas olahraga yang baru dilaporkan relawan.</p>
                </div>
                <a href="{{ route('prasarana.index') }}" class="hidden md:inline-flex items-center gap-2 text-blue-600 hover:text-blue-800 font-medium transition text-sm">Lihat Semua <i class="fas fa-arrow-right"></i></a>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($latestPrasarana as $p)
                    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm hover:shadow-lg hover:border-blue-100 transition-all duration-300 group">
                        <div class="flex items-start justify-between mb-4">
                            <div class="w-11 h-11 bg-blue-50 rounded-xl flex items-center justify-center text-blue-600 text-lg group-hover:bg-blue-100 transition"><i class="fas fa-map-marker-alt"></i></div>
                            <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $p->status_color }}">{{ $p->status }}</span>
                        </div>
                        <h3 class="text-base font-bold text-gray-900 mb-1 truncate">{{ $p->nama_fasilitas }}</h3>
                        <p class="text-gray-500 text-sm mb-3">{{ $p->kategori_olahraga }}</p>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500"><i class="fas fa-city mr-1 text-gray-400"></i> {{ $p->kabupaten ?? '-' }}</span>
                            <span class="text-blue-600 font-semibold">Kondisi {{ $p->average_kondisi }}/5</span>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12 text-gray-400"><i class="fas fa-inbox text-4xl mb-3 opacity-50"></i><p>Belum ada data prasarana.</p></div>
                @endforelse
            </div>
            <div class="mt-6 text-center md:hidden">
                <a href="{{ route('prasarana.index') }}" class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-800 font-medium transition text-sm">Lihat Semua <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>
    </section>

    <!-- Upcoming Events -->
    <section class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-1">Event Akan Datang</h2>
                    <p class="text-gray-500">Jadwal kegiatan olahraga terdekat di daerah Anda.</p>
                </div>
                <a href="{{ route('events.index') }}" class="hidden md:inline-flex items-center gap-2 text-blue-600 hover:text-blue-800 font-medium transition text-sm">Lihat Semua <i class="fas fa-arrow-right"></i></a>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @forelse($upcomingEvents as $event)
                    <div class="flex items-center gap-5 bg-white rounded-2xl p-5 border border-gray-100 shadow-sm hover:shadow-md transition-all duration-300">
                        <div class="flex-shrink-0 w-16 h-16 bg-gradient-to-br from-blue-500 to-sky-400 rounded-xl flex flex-col items-center justify-center text-white font-bold shadow-md">
                            <span class="text-xs uppercase">{{ $event->tanggal_mulai->format('M') }}</span>
                            <span class="text-xl leading-none">{{ $event->tanggal_mulai->format('d') }}</span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <h3 class="text-base font-bold text-gray-900 truncate mb-1">{{ $event->nama_event }}</h3>
                            <div class="flex items-center gap-3 text-sm text-gray-500">
                                <span class="inline-flex items-center gap-1"><i class="fas fa-layer-group text-blue-400"></i> {{ $event->tingkat }}</span>
                                <span class="inline-flex items-center gap-1"><i class="fas fa-map-pin text-sky-400"></i> {{ $event->kabupaten ?? '-' }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12 text-gray-400"><i class="fas fa-calendar-times text-4xl mb-3 opacity-50"></i><p>Tidak ada event yang akan datang.</p></div>
                @endforelse
            </div>
            <div class="mt-6 text-center md:hidden">
                <a href="{{ route('events.index') }}" class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-800 font-medium transition text-sm">Lihat Semua <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>
    </section>

    <!-- Active Clubs -->
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-1">Klub Aktif</h2>
                    <p class="text-gray-500">Komunitas olahraga yang aktif berlatih saat ini.</p>
                </div>
                <a href="{{ route('clubs.index') }}" class="hidden md:inline-flex items-center gap-2 text-blue-600 hover:text-blue-800 font-medium transition text-sm">Lihat Semua <i class="fas fa-arrow-right"></i></a>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @forelse($activeClubs as $club)
                    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm hover:shadow-lg hover:border-blue-100 transition-all duration-300 text-center">
                        <div class="w-14 h-14 mx-auto mb-4 bg-gradient-to-tr from-blue-400 to-sky-400 rounded-full flex items-center justify-center text-white text-2xl shadow-md"><i class="fas fa-shield-alt"></i></div>
                        <h3 class="text-base font-bold text-gray-900 mb-1 truncate">{{ $club->nama_club }}</h3>
                        <p class="text-gray-500 text-sm mb-3">Ketua: {{ $club->ketua_club }}</p>
                        @if($club->prasarana)
                            <span class="inline-block px-3 py-1 rounded-full bg-blue-50 text-blue-700 text-xs font-medium border border-blue-100"><i class="fas fa-building mr-1"></i> {{ $club->prasarana->nama_fasilitas }}</span>
                        @else
                            <span class="inline-block px-3 py-1 rounded-full bg-gray-50 text-gray-500 text-xs font-medium border border-gray-100">Belum memiliki prasarana</span>
                        @endif
                    </div>
                @empty
                    <div class="col-span-full text-center py-12 text-gray-400"><i class="fas fa-users-slash text-4xl mb-3 opacity-50"></i><p>Belum ada klub aktif.</p></div>
                @endforelse
            </div>
            <div class="mt-6 text-center md:hidden">
                <a href="{{ route('clubs.index') }}" class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-800 font-medium transition text-sm">Lihat Semua <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-16 bg-gradient-to-br from-blue-600 to-sky-500">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">Jadilah Bagian dari Perubahan</h2>
            <p class="text-blue-100 text-lg mb-8 max-w-2xl mx-auto">Bersama-sama kita bisa membangun peta keolahragaan daerah yang akurat dan bermanfaat untuk semua.</p>
            @guest
                <button @click="modal = 'register'" class="px-8 py-3.5 bg-white text-blue-600 font-bold rounded-xl shadow-lg hover:bg-blue-50 transition-all duration-300">Daftar Sebagai Relawan</button>
            @else
                <a href="{{ url('/dashboard') }}" class="px-8 py-3.5 bg-white text-blue-600 font-bold rounded-xl shadow-lg hover:bg-blue-50 transition-all duration-300">Buka Dasbor Saya</a>
            @endguest
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
