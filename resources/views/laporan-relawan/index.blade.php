<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Laporan Relawan</h2>
                <p class="text-sm text-gray-500 mt-1">Rekap kinerja, poin, dan kontribusi tiap relawan penggerak olahraga.</p>
            </div>
            <a href="{{ route('laporan-relawan.rekap-pdf') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl transition shadow-sm shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Rekap PDF Semua Relawan
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- ===== RINGKASAN ===== --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                @php
                    $cards = [
                        ['label' => 'Total Relawan',    'value' => number_format($agg['total_relawan']), 'sub' => $agg['aktif'] . ' aktif berkontribusi'],
                        ['label' => 'Poin Terkumpul',   'value' => number_format($agg['total_poin']),    'sub' => 'akumulasi seluruh relawan'],
                        ['label' => 'Kontribusi Valid', 'value' => number_format($agg['kontribusi']),    'sub' => 'entri tervalidasi & berpoin'],
                        ['label' => 'Rata-rata Poin',   'value' => number_format($agg['total_relawan'] ? round($agg['total_poin'] / $agg['total_relawan']) : 0), 'sub' => 'per relawan terdaftar'],
                    ];
                @endphp
                @foreach($cards as $c)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ $c['label'] }}</p>
                        <p class="text-3xl font-extrabold text-blue-700 mt-2">{{ $c['value'] }}</p>
                        <p class="text-xs text-gray-400 mt-1">{{ $c['sub'] }}</p>
                    </div>
                @endforeach
            </div>

            {{-- ===== FILTER ===== --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <form method="GET" action="{{ route('laporan-relawan.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                    <div class="lg:col-span-2">
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Cari</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama atau email relawan"
                               class="w-full rounded-lg border-gray-200 text-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Kab/Kota</label>
                        <select name="kabupaten" class="w-full rounded-lg border-gray-200 text-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Semua</option>
                            @foreach($filterKabupaten as $k)
                                <option value="{{ $k }}" @selected(request('kabupaten') === $k)>{{ $k }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Kecamatan</label>
                        <select name="kecamatan" class="w-full rounded-lg border-gray-200 text-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Semua</option>
                            @foreach($filterKecamatan as $k)
                                <option value="{{ $k }}" @selected(request('kecamatan') === $k)>{{ $k }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Urutkan</label>
                        <select name="sort" class="w-full rounded-lg border-gray-200 text-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="poin"       @selected($sort === 'poin')>Poin tertinggi</option>
                            <option value="kontribusi" @selected($sort === 'kontribusi')>Kontribusi terbanyak</option>
                            <option value="nama"       @selected($sort === 'nama')>Nama (A-Z)</option>
                            <option value="terbaru"    @selected($sort === 'terbaru')>Terbaru bergabung</option>
                        </select>
                    </div>
                    <div class="sm:col-span-2 lg:col-span-5 flex gap-2">
                        <button type="submit" class="px-5 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">Terapkan</button>
                        @if(request()->hasAny(['search', 'kabupaten', 'kecamatan', 'sort']))
                            <a href="{{ route('laporan-relawan.index') }}" class="px-5 py-2 bg-gray-100 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-200 transition">Reset</a>
                        @endif
                    </div>
                </form>
            </div>

            {{-- ===== DAFTAR RELAWAN ===== --}}
            <div class="space-y-3">
                @forelse($relawan as $r)
                    @php
                        $kontribusi = [
                            ['label' => 'Prasarana',   'total' => $r->prasarana_count,       'valid' => $r->prasarana_valid_count],
                            ['label' => 'Klub',        'total' => $r->clubs_count,           'valid' => $r->clubs_valid_count],
                            ['label' => 'Event',       'total' => $r->events_count,          'valid' => $r->events_valid_count],
                            ['label' => 'Partisipasi', 'total' => $r->partisipasi_count,     'valid' => $r->partisipasi_valid_count],
                            ['label' => 'Kampung',     'total' => $r->kampung_olahraga_count,'valid' => $r->kampung_valid_count],
                        ];
                        $totalKontribusi = collect($kontribusi)->sum('total');
                        $peringkat = ($relawan->currentPage() - 1) * $relawan->perPage() + $loop->iteration;
                    @endphp
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-md hover:border-blue-100 transition"
                         x-data="{ copied: false }">
                        <div class="flex flex-col lg:flex-row lg:items-center gap-4">

                            {{-- Identitas --}}
                            <div class="flex items-center gap-4 lg:w-72 shrink-0">
                                <div class="relative">
                                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-500 to-sky-400 flex items-center justify-center text-white font-bold text-lg shadow">
                                        {{ strtoupper(substr($r->name, 0, 1)) }}
                                    </div>
                                    @if($sort === 'poin')
                                        <span class="absolute -top-1 -left-1 w-5 h-5 rounded-full bg-white border border-gray-200 text-[10px] font-bold text-gray-600 flex items-center justify-center shadow-sm">{{ $peringkat }}</span>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <h3 class="text-sm font-bold text-gray-900 truncate">{{ $r->name }}</h3>
                                    <p class="text-xs text-gray-500 truncate">{{ $r->email }}</p>
                                    <p class="text-xs text-gray-400 truncate">{{ collect([$r->kecamatan, $r->kabupaten])->filter()->implode(', ') ?: 'Wilayah belum diatur' }}</p>
                                </div>
                            </div>

                            {{-- Poin --}}
                            <div class="lg:w-32 shrink-0 flex lg:flex-col items-baseline lg:items-start gap-2 lg:gap-0">
                                <p class="text-2xl font-extrabold text-blue-700">{{ number_format($r->total_poin ?? 0) }}</p>
                                <p class="text-xs text-gray-400">poin &middot; {{ $r->badges_count }} lencana</p>
                            </div>

                            {{-- Kontribusi chips --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex flex-wrap gap-2">
                                    @foreach($kontribusi as $k)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs border
                                            {{ $k['total'] > 0 ? 'bg-gray-50 border-gray-200 text-gray-700' : 'bg-gray-50 border-gray-100 text-gray-300' }}">
                                            {{ $k['label'] }}
                                            <strong class="{{ $k['total'] > 0 ? 'text-gray-900' : 'text-gray-300' }}">{{ $k['total'] }}</strong>
                                            @if($k['valid'] > 0)
                                                <span class="text-emerald-600 font-semibold">&check;{{ $k['valid'] }}</span>
                                            @endif
                                        </span>
                                    @endforeach
                                </div>
                                <p class="text-xs text-gray-400 mt-2">{{ $totalKontribusi }} total entri kontribusi</p>
                            </div>

                            {{-- Aksi --}}
                            <div class="flex flex-wrap gap-2 shrink-0">
                                <button type="button"
                                        data-live-url="{{ $r->publicReportUrl() }}"
                                        @click="navigator.clipboard.writeText($event.currentTarget.dataset.liveUrl); copied = true; setTimeout(() => copied = false, 2000)"
                                        class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold rounded-lg transition"
                                        :class="copied ? 'text-emerald-700 bg-emerald-50' : 'text-teal-700 bg-teal-50 hover:bg-teal-100'">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 010 5.656l-3 3a4 4 0 01-5.656-5.656l1.5-1.5M10.172 13.828a4 4 0 010-5.656l3-3a4 4 0 015.656 5.656l-1.5 1.5"/></svg>
                                    <span x-show="!copied">Salin Link Live</span>
                                    <span x-show="copied" x-cloak>✓ Tersalin</span>
                                </button>
                                <a href="{{ route('laporan-relawan.show', $r) }}"
                                   class="px-3 py-2 text-xs font-semibold text-blue-700 bg-blue-50 hover:bg-blue-100 rounded-lg transition">Detail</a>
                                <a href="{{ route('laporan-relawan.pdf', $r) }}"
                                   class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    PDF
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-16 text-center text-gray-400">
                        <svg class="w-14 h-14 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <p class="text-base font-medium text-gray-600">Tidak ada relawan yang cocok dengan filter.</p>
                    </div>
                @endforelse
            </div>

            <div>{{ $relawan->links() }}</div>
        </div>
    </div>
</x-app-layout>
