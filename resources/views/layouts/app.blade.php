<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@yield('title', config('app.name', 'CPSS')) - Kolaborasi Olahraga Daerah</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700&display=swap" rel="stylesheet" />
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            [x-cloak] { display: none !important; }
            .sidebar-scroll::-webkit-scrollbar { width: 5px; }
            .sidebar-scroll::-webkit-scrollbar-track { background: transparent; }
            .sidebar-scroll::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.1); border-radius: 10px; }
            .sidebar-scroll::-webkit-scrollbar-thumb:hover { background: rgba(0,0,0,0.2); }
            .sidebar-scroll { scrollbar-width: thin; scrollbar-color: rgba(0,0,0,0.1) transparent; }
        </style>
    </head>
    <body class="font-sans antialiased bg-gray-50 text-gray-800">
        <div x-data="{ sidebarOpen: false, dropdownOpen: {} }" class="min-h-screen">
            <div x-show="sidebarOpen" x-transition.opacity.duration.300ms @click="sidebarOpen = false" class="fixed inset-0 bg-gray-900/50 z-40 lg:hidden"></div>

            <!-- Sidebar -->
            <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed top-0 left-0 h-screen z-50 w-64 bg-white border-r border-gray-200 text-gray-700 transform transition-transform duration-300 ease-in-out lg:translate-x-0 flex flex-col sidebar-scroll shadow-sm">
                <!-- Logo Area -->
                <div class="p-5 border-b border-gray-100">
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-3">
                        <div class="w-9 h-9 bg-gradient-to-br from-blue-600 to-sky-500 rounded-lg flex items-center justify-center shadow-sm">
                            <span class="text-white font-bold text-sm">C</span>
                        </div>
                        <div>
                            <h1 class="text-lg font-bold text-gray-900 tracking-tight">CPSS</h1>
                            <p class="text-[10px] text-gray-400 font-medium">Kolaborasi Olahraga</p>
                        </div>
                    </a>
                </div>

                <!-- Navigation -->
                <nav class="flex-1 overflow-y-auto py-3 px-3 space-y-1 sidebar-scroll">
                    <p class="px-3 text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Menu Utama</p>

                    <a href="{{ route('dashboard') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 {{ request()->routeIs('dashboard') ? 'text-blue-600' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>
                        Dashboard
                    </a>

                    <a href="{{ route('prasarana.index') }}" class="flex items-center justify-between px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('prasarana.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <span class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 {{ request()->routeIs('prasarana.*') ? 'text-blue-600' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                            Prasarana
                        </span>
                        @auth
                            @php $pendingPrasarana = \App\Models\Prasarana::where('status_validasi','pending')->count(); @endphp
                            @if($pendingPrasarana > 0 && auth()->user()->isAdmin())
                                <span class="bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full min-w-[18px] text-center">{{ $pendingPrasarana }}</span>
                            @endif
                        @endauth
                    </a>

                    <a href="{{ route('clubs.index') }}" class="flex items-center justify-between px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('clubs.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <span class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 {{ request()->routeIs('clubs.*') ? 'text-blue-600' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                            Clubs
                        </span>
                        @auth
                            @php $pendingClubs = \App\Models\Club::where('status_validasi','pending')->count(); @endphp
                            @if($pendingClubs > 0 && auth()->user()->isAdmin())
                                <span class="bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full min-w-[18px] text-center">{{ $pendingClubs }}</span>
                            @endif
                        @endauth
                    </a>

                    <a href="{{ route('events.index') }}" class="flex items-center justify-between px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('events.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <span class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 {{ request()->routeIs('events.*') ? 'text-blue-600' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                            Events
                        </span>
                        @auth
                            @php $pendingEvents = \App\Models\Event::where('status_validasi','pending')->count(); @endphp
                            @if($pendingEvents > 0 && auth()->user()->isAdmin())
                                <span class="bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full min-w-[18px] text-center">{{ $pendingEvents }}</span>
                            @endif
                        @endauth
                    </a>

                    <a href="{{ route('kalender.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('kalender.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 {{ request()->routeIs('kalender.*') ? 'text-blue-600' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                        Kalender
                    </a>

                    @auth
                        @if(auth()->user()->isAdmin() || auth()->user()->isRelawan())
                            <a href="{{ route('partisipasi.index') }}" class="flex items-center justify-between px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('partisipasi.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                                <span class="flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 {{ request()->routeIs('partisipasi.*') ? 'text-blue-600' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                                    Partisipasi
                                </span>
                                @php $pendingPartisipasi = \App\Models\Partisipasi::where('status_validasi','pending')->count(); @endphp
                                @if($pendingPartisipasi > 0 && auth()->user()->isAdmin())
                                    <span class="bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full min-w-[18px] text-center">{{ $pendingPartisipasi }}</span>
                                @endif
                            </a>
                        @endif

                        <a href="{{ route('leaderboard.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('leaderboard.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 {{ request()->routeIs('leaderboard.*') ? 'text-blue-600' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                            Leaderboard
                        </a>

                        <a href="{{ route('relawan.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('relawan.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 {{ request()->routeIs('relawan.*') ? 'text-blue-600' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                            Daftar Relawan
                        </a>

                        @if(auth()->user()->isAdmin() || auth()->user()->isSuperAdmin())
                            <div class="pt-3 mt-2 border-t border-gray-100">
                                <p class="px-3 text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Manajemen</p>

                                <a href="{{ route('talenta.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('talenta.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 {{ request()->routeIs('talenta.*') ? 'text-blue-600' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" /></svg>
                                    Talenta
                                </a>

                                <a href="{{ route('tenaga-ahli.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('tenaga-ahli.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 {{ request()->routeIs('tenaga-ahli.*') ? 'text-blue-600' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                                    Tenaga Ahli
                                </a>

                                <a href="{{ route('audit-logs.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('audit-logs.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 {{ request()->routeIs('audit-logs.*') ? 'text-blue-600' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" /></svg>
                                    Audit Log
                                </a>

                                @if(auth()->user()->isSuperAdmin())
                                    <a href="{{ route('users.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('users.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 {{ request()->routeIs('users.*') ? 'text-blue-600' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                                        Users
                                    </a>
                                @endif
                            </div>
                        @endif
                    @endauth
                </nav>

                <!-- User Profile Section -->
                <div class="p-3 border-t border-gray-100">
                    @auth
                    <div class="relative">
                        <button @click="dropdownOpen.profile = !dropdownOpen.profile" class="w-full flex items-center space-x-3 p-2 rounded-xl hover:bg-gray-50 transition-all duration-200">
                            <div class="w-9 h-9 rounded-full bg-gradient-to-br from-blue-500 to-sky-400 flex items-center justify-center text-white font-semibold text-sm">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                            <div class="flex-1 text-left min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-gray-500 capitalize">{{ Auth::user()->role }}</p>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                        </button>
                        <div x-show="dropdownOpen.profile" @click.away="dropdownOpen.profile = false" x-transition x-cloak class="absolute bottom-full left-0 right-0 mb-2 bg-white rounded-xl shadow-xl border border-gray-200 overflow-hidden">
                            <a href="{{ route('leaderboard.my-points') }}" class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50">Poin & Lencana Saya</a>
                            <a href="{{ route('profile.edit') }}" class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50">Profile</a>
                            <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="w-full text-left px-4 py-2.5 text-sm text-red-600 hover:bg-red-50">Log Out</button></form>
                        </div>
                    </div>
                    @else
                    <a href="{{ route('login') }}" class="flex items-center justify-center w-full px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-medium text-sm transition-all duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" /></svg>
                        Masuk untuk Melapor
                    </a>
                    @endauth
                </div>
            </aside>

            <!-- Main Content -->
            <div class="lg:ml-64 flex-1 flex flex-col min-h-screen overflow-hidden">
                <header class="lg:hidden bg-white shadow-sm border-b border-gray-200">
                    <div class="flex items-center justify-between px-4 py-3">
                        <button @click="sidebarOpen = !sidebarOpen" class="p-2 rounded-lg text-gray-600 hover:bg-gray-100">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                        </button>
                        <span class="text-lg font-bold text-gray-800">CPSS</span>
                        <div class="w-8"></div>
                    </div>
                </header>
                @isset($header)
                    <header class="bg-white shadow-sm border-b border-gray-200">
                        <div class="max-w-7xl mx-auto py-5 px-4 sm:px-6 lg:px-8 flex justify-between items-center gap-4">
                            <div class="flex-1 min-w-0">{{ $header }}</div>
                            @auth
                                @php
                                    $notifUnreadCount = \App\Models\UserNotification::where('user_id', auth()->id())->whereNull('read_at')->count();
                                    $notifRecent = \App\Models\UserNotification::where('user_id', auth()->id())->latest()->take(5)->get();
                                @endphp
                                <div class="relative shrink-0" x-data="{ notifOpen: false }">
                                    <button @click="notifOpen = !notifOpen" class="relative p-2.5 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-xl transition-all">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                        </svg>
                                        @if($notifUnreadCount > 0)
                                            <span class="absolute top-1.5 right-1.5 min-w-[18px] h-[18px] flex items-center justify-center bg-red-500 text-white text-[10px] font-bold rounded-full px-1">{{ $notifUnreadCount }}</span>
                                        @endif
                                    </button>
                                    <div x-show="notifOpen" @click.away="notifOpen = false" x-cloak x-transition class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-xl border border-gray-200 overflow-hidden z-50">
                                        <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
                                            <h4 class="text-sm font-semibold text-gray-800">Notifikasi</h4>
                                            @if($notifUnreadCount > 0)
                                                <form method="POST" action="{{ route('notifications.read-all') }}">@csrf
                                                    <button type="submit" class="text-xs text-blue-600 hover:text-blue-800 font-medium">Tandai semua baca</button>
                                                </form>
                                            @endif
                                        </div>
                                        <div class="max-h-80 overflow-y-auto">
                                            @forelse($notifRecent as $n)
                                                <div class="px-4 py-3 border-b border-gray-50 hover:bg-gray-50 transition {{ is_null($n->read_at) ? 'bg-blue-50/30' : '' }}">
                                                    <div class="flex items-start gap-3">
                                                        <div class="shrink-0 mt-0.5">{!! $n->icon !!}</div>
                                                        <div class="flex-1 min-w-0">
                                                            <p class="text-sm font-medium text-gray-800">{{ $n->title }}</p>
                                                            <p class="text-xs text-gray-500 mt-0.5">{{ $n->message }}</p>
                                                            <p class="text-[10px] text-gray-400 mt-1">{{ $n->created_at->diffForHumans() }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            @empty
                                                <div class="px-4 py-6 text-center text-sm text-gray-400">Tidak ada notifikasi</div>
                                            @endforelse
                                        </div>
                                        <div class="px-4 py-2 border-t border-gray-100 bg-gray-50 text-center">
                                            <a href="{{ route('leaderboard.my-points') }}" class="text-xs text-blue-600 hover:text-blue-800 font-medium">Lihat Poin & Lencana Saya</a>
                                        </div>
                                    </div>
                                </div>
                            @endauth
                        </div>
                    </header>
                @endisset
                <main class="flex-1 overflow-y-auto bg-gray-50">
                    @isset($slot){{ $slot }}@else @yield('content') @endisset
                </main>
            </div>
        </div>
        @stack('scripts')
    </body>
</html>
