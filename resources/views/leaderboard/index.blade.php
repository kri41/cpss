@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6 flex items-start justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Leaderboard</h1>
                <p class="mt-1 text-sm text-gray-500">Peringkat kontributor data keolahragaan</p>
            </div>
            @if(auth()->user()?->isAdmin())
            <a href="{{ route('export.leaderboard') }}" class="flex items-center gap-1.5 px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition shadow-sm shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Export CSV
            </a>
            @endif
        </div>

        <!-- Top-level Tabs -->
        <div class="mb-6 flex gap-2">
            <a href="{{ route('leaderboard.index', ['tab' => 'relawan']) }}"
               class="px-4 py-2 rounded-xl text-sm font-semibold transition {{ $tab === 'relawan' ? 'bg-blue-600 text-white shadow-sm' : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50' }}">
                Relawan
            </a>
            <a href="{{ route('leaderboard.index', ['tab' => 'kampung']) }}"
               class="px-4 py-2 rounded-xl text-sm font-semibold transition {{ $tab === 'kampung' ? 'bg-blue-600 text-white shadow-sm' : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50' }}">
                Kampung Olahraga
            </a>
            <a href="{{ route('leaderboard.index', ['tab' => 'klub']) }}"
               class="px-4 py-2 rounded-xl text-sm font-semibold transition {{ $tab === 'klub' ? 'bg-blue-600 text-white shadow-sm' : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50' }}">
                Klub/Komunitas
            </a>
        </div>

        @if($tab === 'kampung')
        <!-- Kampung Olahraga Leaderboard -->
        <div class="space-y-3">
            @forelse($kampungLeaderboard as $index => $k)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex items-center gap-4">
                <div class="flex-shrink-0 w-10 flex justify-center">
                    @if($index === 0)
                        <span class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-yellow-400 text-white text-lg font-bold shadow-sm">1</span>
                    @elseif($index === 1)
                        <span class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-gray-300 text-gray-800 text-lg font-bold shadow-sm">2</span>
                    @elseif($index === 2)
                        <span class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-orange-400 text-white text-lg font-bold shadow-sm">3</span>
                    @else
                        <span class="text-lg font-bold text-gray-400">{{ $index + 1 }}</span>
                    @endif
                </div>
                <div class="flex-1 min-w-0">
                    <a href="{{ route('kampung.show', $k) }}" class="text-sm font-bold text-gray-900 hover:text-blue-600 truncate block">{{ $k->nama_kampung }}</a>
                    <div class="text-xs text-gray-500 truncate">
                        @if($k->rt_rw_label)
                        {{ $k->rt_rw_label }} &middot;
                        @endif
                        {{ collect([$k->desa, $k->kecamatan, $k->kabupaten])->filter()->implode(', ') ?: '-' }}
                        &middot; {{ $k->fasil->count() }} fasil &middot; {{ number_format($k->checkins_count) }} check-in
                    </div>
                </div>
                <div class="flex-shrink-0">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-blue-50 text-blue-700">
                        {{ $k->skorPoin() }} poin
                    </span>
                </div>
            </div>
            @empty
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-10 text-center text-gray-500">
                Belum ada Kampung Olahraga yang terverifikasi.
            </div>
            @endforelse
        </div>
        @elseif($tab === 'klub')
        <!-- Klub/Komunitas Leaderboard -->
        <div class="space-y-3">
            @forelse($klubLeaderboard as $index => $klub)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex items-center gap-4">
                <div class="flex-shrink-0 w-10 flex justify-center">
                    @if($index === 0)
                        <span class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-yellow-400 text-white text-lg font-bold shadow-sm">1</span>
                    @elseif($index === 1)
                        <span class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-gray-300 text-gray-800 text-lg font-bold shadow-sm">2</span>
                    @elseif($index === 2)
                        <span class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-orange-400 text-white text-lg font-bold shadow-sm">3</span>
                    @else
                        <span class="text-lg font-bold text-gray-400">{{ $index + 1 }}</span>
                    @endif
                </div>
                <div class="flex-1 min-w-0">
                    <a href="{{ route('clubs.show', $klub) }}" class="text-sm font-bold text-gray-900 hover:text-blue-600 truncate block">{{ $klub->nama_club }}</a>
                    <div class="text-xs text-gray-500 truncate">{{ $klub->jenisOlahraga?->nama ?? '-' }}</div>
                </div>
                <div class="flex-shrink-0">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-blue-50 text-blue-700">
                        {{ number_format($klub->checkins_count) }} check-in
                    </span>
                </div>
            </div>
            @empty
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-10 text-center text-gray-500">
                Belum ada Klub/Komunitas yang aktif.
            </div>
            @endforelse
        </div>
        @else

        <!-- Personal Rank Card -->
        @if($personalRank)
        <div class="mb-6 bg-gradient-to-r from-blue-700 to-sky-500 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm">Peringkat Anda saat ini</p>
                    <p class="text-3xl font-bold mt-1">#{{ $personalRank }}</p>
                </div>
                <div class="text-right">
                    <p class="text-blue-100 text-sm">Total Poin</p>
                    <p class="text-3xl font-bold mt-1">{{ auth()->user()->total_poin ?? 0 }}</p>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('leaderboard.my-points') }}" class="inline-flex items-center text-sm font-medium text-white hover:text-blue-100">
                    Lihat detail poin &rarr;
                </a>
            </div>
        </div>
        @endif

        <!-- Period Tabs -->
        <div class="mb-6 border-b border-gray-200">
            <nav class="-mb-px flex space-x-8">
                <a href="{{ route('leaderboard.index', ['periode' => 'mingguan']) }}"
                   class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm {{ $periode === 'mingguan' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Minggu Ini
                </a>
                <a href="{{ route('leaderboard.index', ['periode' => 'bulanan']) }}"
                   class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm {{ $periode === 'bulanan' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Bulan Ini
                </a>
                <a href="{{ route('leaderboard.index', ['periode' => 'total']) }}"
                   class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm {{ $periode === 'total' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Total Program
                </a>
            </nav>
        </div>

        <!-- Leaderboard Cards -->
        <div class="space-y-3">
            @forelse($leaderboard as $index => $user)
            @php $rank = $leaderboard->firstItem() + $index; @endphp
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex items-center gap-4">
                <!-- Rank -->
                <div class="flex-shrink-0 w-10 flex justify-center">
                    @if($rank === 1)
                        <span class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-yellow-400 text-white text-lg font-bold shadow-sm">1</span>
                    @elseif($rank === 2)
                        <span class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-gray-300 text-gray-800 text-lg font-bold shadow-sm">2</span>
                    @elseif($rank === 3)
                        <span class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-orange-400 text-white text-lg font-bold shadow-sm">3</span>
                    @else
                        <span class="text-lg font-bold text-gray-400">{{ $rank }}</span>
                    @endif
                </div>

                <!-- Avatar & Info -->
                <div class="flex items-center gap-3 flex-1 min-w-0">
                    <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-sm flex-shrink-0">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <div class="text-sm font-bold text-gray-900 truncate">{{ $user->name }}</div>
                        <div class="text-xs text-gray-500 truncate">{{ $user->email }}</div>
                        @if($user->desa || $user->kecamatan)
                            <div class="text-xs text-gray-400 truncate">
                                {{ $user->desa }}{{ $user->desa && $user->kecamatan ? ', ' : '' }}{{ $user->kecamatan }}
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Points -->
                <div class="flex-shrink-0">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-blue-50 text-blue-700">
                        {{ match($periode) {
                            'mingguan' => $user->poin_mingguan ?? 0,
                            'bulanan' => $user->poin_bulanan ?? 0,
                            default => $user->poin_total ?? 0,
                        } }} poin
                    </span>
                </div>
            </div>
            @empty
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-10 text-center text-gray-500">
                Belum ada data kontributor.
            </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $leaderboard->appends(['periode' => $periode])->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
