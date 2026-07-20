<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'Dataraga')) — Kamu Gerak, Indonesia Tahu</title>
    <link rel="icon" href="/storage/logo.png" type="image/png">
    <link rel="apple-touch-icon" href="/storage/logo.png">
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#2563eb">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="Dataraga">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>[x-cloak]{display:none!important}</style>
    @stack('styles')
</head>
<body class="font-sans antialiased text-gray-800" style="background: linear-gradient(160deg, #1e3a8a 0%, #2563eb 30%, #60a5fa 60%, #dbeafe 85%, #f0f9ff 100%); background-attachment: fixed; min-height: 100vh;" x-data="{ mobileMenu: false }">

    <!-- Navbar Publik -->
    <nav class="sticky top-0 z-40 border-b border-white/10" style="background: rgba(15,23,42,0.45); backdrop-filter: blur(16px);">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-14">
                <!-- Logo -->
                <a href="{{ url('/') }}" class="flex items-center gap-2.5">
                    <img src="/storage/logo.png" alt="Dataraga" class="h-9 w-9 object-contain brightness-0 invert">
                    <span class="font-bold text-lg text-white tracking-tight">Dataraga</span>
                </a>

                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center space-x-1">
                    <a href="{{ route('prasarana.index') }}" class="px-3 py-2 text-sm font-medium rounded-lg transition {{ request()->routeIs('prasarana.*') ? 'bg-white/15 text-white' : 'text-white/75 hover:text-white hover:bg-white/10' }}">Prasarana</a>
                    <a href="{{ route('events.index') }}" class="px-3 py-2 text-sm font-medium rounded-lg transition {{ request()->routeIs('events.index') ? 'bg-white/15 text-white' : 'text-white/75 hover:text-white hover:bg-white/10' }}">Event</a>
                    <a href="{{ route('events.peta') }}" class="px-3 py-2 text-sm font-medium rounded-lg transition {{ request()->routeIs('events.peta') ? 'bg-white/15 text-white' : 'text-white/75 hover:text-white hover:bg-white/10' }}">Peta Event</a>
                    <a href="{{ route('clubs.index') }}" class="px-3 py-2 text-sm font-medium rounded-lg transition {{ request()->routeIs('clubs.*') ? 'bg-white/15 text-white' : 'text-white/75 hover:text-white hover:bg-white/10' }}">Klub/Komunitas</a>
                    <a href="{{ route('kalender.index') }}" class="px-3 py-2 text-sm font-medium rounded-lg transition {{ request()->routeIs('kalender.*') ? 'bg-white/15 text-white' : 'text-white/75 hover:text-white hover:bg-white/10' }}">Kalender</a>
                </div>

                <!-- Auth -->
                <div class="hidden md:flex items-center space-x-2">
                    @guest
                        <a href="{{ route('login') }}" class="text-white/75 hover:text-white font-medium px-3 py-1.5 transition text-sm">Masuk</a>
                        <a href="{{ route('register') }}" class="px-4 py-1.5 bg-white text-blue-800 font-bold rounded-full hover:bg-blue-50 transition-all text-sm">Daftar</a>
                    @else
                        <span class="text-white/70 text-sm mr-1">{{ Auth::user()->name }}</span>
                        <a href="{{ url('/dashboard') }}" class="px-4 py-1.5 bg-gradient-to-r from-blue-600 to-sky-500 text-white font-medium rounded-full shadow-sm hover:shadow-md transition text-sm">Dasbor</a>
                    @endguest
                </div>

                <!-- Mobile button -->
                <button @click="mobileMenu = !mobileMenu" class="md:hidden p-2 rounded-lg text-white/80 hover:bg-white/10">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                </button>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div x-show="mobileMenu" @click.away="mobileMenu = false" x-cloak x-transition class="md:hidden border-t border-white/10" style="background: rgba(15,23,42,0.55); backdrop-filter: blur(16px);">
            <div class="px-4 py-3 space-y-1">
                <a href="{{ route('prasarana.index') }}" class="block px-4 py-2.5 text-sm font-medium rounded-lg {{ request()->routeIs('prasarana.*') ? 'bg-white/15 text-white' : 'text-white/75 hover:bg-white/10 hover:text-white' }}">Prasarana</a>
                <a href="{{ route('events.index') }}" class="block px-4 py-2.5 text-sm font-medium rounded-lg {{ request()->routeIs('events.index') ? 'bg-white/15 text-white' : 'text-white/75 hover:bg-white/10 hover:text-white' }}">Event</a>
                <a href="{{ route('events.peta') }}" class="block px-4 py-2.5 text-sm font-medium rounded-lg {{ request()->routeIs('events.peta') ? 'bg-white/15 text-white' : 'text-white/75 hover:bg-white/10 hover:text-white' }}">Peta Event</a>
                <a href="{{ route('clubs.index') }}" class="block px-4 py-2.5 text-sm font-medium rounded-lg {{ request()->routeIs('clubs.*') ? 'bg-white/15 text-white' : 'text-white/75 hover:bg-white/10 hover:text-white' }}">Klub/Komunitas</a>
                <a href="{{ route('kalender.index') }}" class="block px-4 py-2.5 text-sm font-medium rounded-lg {{ request()->routeIs('kalender.*') ? 'bg-white/15 text-white' : 'text-white/75 hover:bg-white/10 hover:text-white' }}">Kalender</a>
                <div class="pt-2 border-t border-white/10">
                    @guest
                        <a href="{{ route('login') }}" class="block px-4 py-2.5 text-sm font-medium text-white/75 hover:bg-white/10 hover:text-white rounded-lg">Masuk</a>
                        <a href="{{ route('register') }}" class="block px-4 py-2.5 text-sm font-bold text-white hover:bg-white/10 rounded-lg">Daftar</a>
                    @else
                        <a href="{{ url('/dashboard') }}" class="block px-4 py-2.5 text-sm font-bold text-white hover:bg-white/10 rounded-lg">Buka Dasbor</a>
                    @endguest
                </div>
            </div>
        </div>
    </nav>

    <!-- Content -->
    <main>
        @yield('content')
    </main>

    @stack('scripts')
    @stack('mascot')
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
