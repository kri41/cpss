@extends('layouts.app')

@section('title', 'Laporan ' . $relawan->name . ' - Dataraga')

@section('content')
@php
    $kategoriMeta = [
        'prasarana'        => ['label' => 'Prasarana',        'bar' => 'bg-blue-500'],
        'club'             => ['label' => 'Klub/Komunitas',   'bar' => 'bg-indigo-500'],
        'event'            => ['label' => 'Event',            'bar' => 'bg-sky-500'],
        'partisipasi'      => ['label' => 'Partisipasi',      'bar' => 'bg-emerald-500'],
        'kampung_olahraga' => ['label' => 'Kampung Olahraga', 'bar' => 'bg-amber-500'],
    ];
    $poinMax = max(1, $poinPerKategori->max() ?? 1);
    $statusPill = fn ($s) => match ($s) {
        'validated' => 'bg-emerald-100 text-emerald-700',
        'rejected'  => 'bg-red-100 text-red-700',
        default     => 'bg-amber-100 text-amber-700',
    };
    $statusLabel = fn ($s) => match ($s) {
        'validated' => 'Tervalidasi',
        'rejected'  => 'Ditolak',
        default     => 'Menunggu',
    };
@endphp

<div class="py-6">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        <a href="{{ route('laporan-relawan.index') }}" class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800 font-medium">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali ke Daftar Relawan
        </a>

        @if(session('success'))
            <div class="p-3 bg-green-50 border border-green-200 rounded-xl text-sm text-green-700">{{ session('success') }}</div>
        @endif

        {{-- ===== HEADER ===== --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="h-16 bg-gradient-to-r from-blue-700 to-sky-500"></div>
            <div class="px-6 pb-6">
                <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 -mt-8">
                    <div class="flex items-end gap-4">
                        <div class="w-20 h-20 rounded-2xl border-4 border-white shadow-md bg-gradient-to-br from-blue-500 to-sky-400 flex items-center justify-center text-white font-bold text-3xl shrink-0">
                            {{ strtoupper(substr($relawan->name, 0, 1)) }}
                        </div>
                        <div class="pb-1">
                            <h1 class="text-xl font-bold text-gray-900">{{ $relawan->name }}</h1>
                            <p class="text-sm text-gray-500">{{ $relawan->email }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">
                                {{ collect([$relawan->desa, $relawan->kecamatan, $relawan->kabupaten])->filter()->implode(', ') ?: 'Wilayah belum diatur' }}
                                &middot; Bergabung {{ $relawan->created_at->locale('id')->isoFormat('D MMM YYYY') }}
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('laporan-relawan.pdf', $relawan) }}"
                       class="pb-1 inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl transition shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        Unduh Laporan PDF
                    </a>
                </div>
            </div>
        </div>

        {{-- ===== LINK LAPORAN PUBLIK (LIVE) ===== --}}
        @if($liveUrl)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5" x-data="{ copied: false }">
            <div class="flex items-center gap-2 mb-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 010 5.656l-3 3a4 4 0 01-5.656-5.656l1.5-1.5M10.172 13.828a4 4 0 010-5.656l3-3a4 4 0 015.656 5.656l-1.5 1.5"/></svg>
                <h3 class="text-sm font-bold text-gray-900">Link Laporan Publik (Live)</h3>
            </div>
            <p class="text-xs text-gray-500 mb-3">Salin dan bagikan link ini. Siapa pun yang membukanya melihat laporan relawan ini secara <strong>live</strong> tanpa perlu login. Link berupa token acak yang tidak bisa ditebak.</p>
            <div class="flex flex-col sm:flex-row gap-2">
                <input type="text" readonly x-ref="liveLink" value="{{ $liveUrl }}"
                       class="flex-1 rounded-lg border-gray-200 bg-gray-50 text-sm text-gray-600 focus:ring-blue-500 focus:border-blue-500">
                <button type="button"
                        @click="navigator.clipboard.writeText($refs.liveLink.value); copied = true; setTimeout(() => copied = false, 2000)"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition whitespace-nowrap">
                    <span x-show="!copied">Salin Link</span>
                    <span x-show="copied" x-cloak>✓ Tersalin</span>
                </button>
                <a href="{{ $liveUrl }}" target="_blank" rel="noopener"
                   class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-lg transition text-center">Buka</a>
                <form method="POST" action="{{ route('laporan-relawan.regenerate-token', $relawan) }}"
                      onsubmit="return confirm('Ganti link? Link lama akan langsung berhenti berfungsi.')">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-red-50 hover:bg-red-100 text-red-700 text-sm font-semibold rounded-lg transition whitespace-nowrap">Ganti Link</button>
                </form>
            </div>
        </div>
        @endif

        {{-- ===== KPI ===== --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-gradient-to-br from-blue-600 to-sky-500 rounded-2xl shadow-sm p-5 text-white">
                <p class="text-xs font-medium text-blue-100 uppercase tracking-wide">Total Poin</p>
                <p class="text-3xl font-extrabold mt-1">{{ number_format($relawan->total_poin ?? 0) }}</p>
                <p class="text-xs text-blue-200 mt-1">+{{ number_format($poinBulanIni) }} bulan ini</p>
            </div>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Peringkat</p>
                <p class="text-3xl font-extrabold text-gray-900 mt-1">#{{ $rank }}</p>
                <p class="text-xs text-gray-400 mt-1">dari {{ $totalRelawan }} relawan</p>
            </div>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Kontribusi</p>
                <p class="text-3xl font-extrabold text-gray-900 mt-1">{{ $totalKontribusi }}</p>
                <p class="text-xs text-emerald-600 mt-1">{{ $totalValid }} tervalidasi</p>
            </div>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Lencana</p>
                <p class="text-3xl font-extrabold text-gray-900 mt-1">{{ $badges->count() }}</p>
                <p class="text-xs text-gray-400 mt-1">pencapaian diraih</p>
            </div>
        </div>

        {{-- ===== POIN PER KATEGORI ===== --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-base font-bold text-gray-900 mb-4">Perolehan Poin per Kategori</h2>
            @if($poinPerKategori->sum() === 0)
                <p class="text-sm text-gray-400">Belum ada poin tervalidasi.</p>
            @else
                <div class="space-y-3">
                    @foreach($kategoriMeta as $key => $meta)
                        @php $val = $poinPerKategori[$key] ?? 0; @endphp
                        <div class="flex items-center gap-3">
                            <span class="w-32 text-sm text-gray-600 shrink-0">{{ $meta['label'] }}</span>
                            <div class="flex-1 h-3 bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full {{ $meta['bar'] }} rounded-full" style="width: {{ round($val / $poinMax * 100) }}%"></div>
                            </div>
                            <span class="w-16 text-right text-sm font-bold text-gray-900">{{ number_format($val) }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- ===== LENCANA ===== --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-base font-bold text-gray-900 mb-4">Lencana</h2>
            @if($badges->isEmpty())
                <p class="text-sm text-gray-400">Relawan ini belum meraih lencana apa pun.</p>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach($badges as $b)
                        <div class="flex items-start gap-3 p-3 rounded-xl bg-blue-50 border border-blue-100">
                            <div class="w-9 h-9 rounded-lg bg-blue-600 text-white flex items-center justify-center shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.196-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-bold text-gray-900">{{ $b->nama }}</p>
                                <p class="text-xs text-gray-500">{{ $b->deskripsi }}</p>
                                @if($b->pivot?->earned_at)
                                    <p class="text-[11px] text-blue-600 mt-0.5">Diraih {{ \Carbon\Carbon::parse($b->pivot->earned_at)->locale('id')->isoFormat('D MMM YYYY') }}</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- ===== TABEL KONTRIBUSI ===== --}}
        @php
            $tabel = [
                ['judul' => 'Prasarana Olahraga',  'data' => $prasarana,   'stat' => $stats['prasarana'],
                 'kolom' => ['Nama Fasilitas', 'Kategori', 'Lokasi', 'Status', 'Tanggal']],
                ['judul' => 'Klub / Komunitas',    'data' => $clubs,       'stat' => $stats['clubs'],
                 'kolom' => ['Nama Klub', 'Cabang', 'Lokasi', 'Status', 'Tanggal']],
                ['judul' => 'Event Olahraga',      'data' => $events,      'stat' => $stats['events'],
                 'kolom' => ['Nama Event', 'Tingkat', 'Lokasi', 'Status', 'Tanggal']],
                ['judul' => 'Partisipasi Kegiatan','data' => $partisipasi, 'stat' => $stats['partisipasi'],
                 'kolom' => ['Lokasi Observasi', 'Est. Orang', 'Kehadiran', 'Status', 'Tanggal']],
                ['judul' => 'Kampung Olahraga',    'data' => $kampung,     'stat' => $stats['kampung'],
                 'kolom' => ['Nama Kampung', 'Check-in', 'Lokasi', 'Status', 'Tanggal']],
            ];
        @endphp

        @foreach($tabel as $t)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="text-base font-bold text-gray-900">{{ $t['judul'] }}</h2>
                    <span class="text-xs text-gray-500">{{ $t['stat']['total'] }} entri &middot; {{ $t['stat']['valid'] }} tervalidasi</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wide">
                            <tr>
                                <th class="px-6 py-3 text-left w-10">#</th>
                                @foreach($t['kolom'] as $c)
                                    <th class="px-4 py-3 text-left">{{ $c }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($t['data'] as $i => $row)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-3 text-gray-400">{{ $i + 1 }}</td>
                                    @if($t['judul'] === 'Prasarana Olahraga')
                                        <td class="px-4 py-3 font-medium text-gray-900">{{ $row->nama_fasilitas }}</td>
                                        <td class="px-4 py-3 text-gray-600">{{ $row->kategori_olahraga_label }}</td>
                                        <td class="px-4 py-3 text-gray-600">{{ collect([$row->desa, $row->kecamatan, $row->kabupaten])->filter()->implode(', ') ?: '-' }}</td>
                                    @elseif($t['judul'] === 'Klub / Komunitas')
                                        <td class="px-4 py-3 font-medium text-gray-900">{{ $row->nama_club }}</td>
                                        <td class="px-4 py-3 text-gray-600">{{ $row->jenisOlahraga?->nama ?? '-' }}</td>
                                        <td class="px-4 py-3 text-gray-600">{{ collect([$row->desa, $row->kecamatan, $row->kabupaten])->filter()->implode(', ') ?: '-' }}</td>
                                    @elseif($t['judul'] === 'Event Olahraga')
                                        <td class="px-4 py-3 font-medium text-gray-900">{{ $row->nama_event }}</td>
                                        <td class="px-4 py-3 text-gray-600">{{ $row->tingkat ?? '-' }}</td>
                                        <td class="px-4 py-3 text-gray-600">{{ collect([$row->desa, $row->kecamatan, $row->kabupaten])->filter()->implode(', ') ?: '-' }}</td>
                                    @elseif($t['judul'] === 'Partisipasi Kegiatan')
                                        <td class="px-4 py-3 font-medium text-gray-900">{{ $row->lokasi_observasi ?? '-' }}</td>
                                        <td class="px-4 py-3 text-gray-600">{{ number_format($row->estimasi_jumlah_orang ?? 0) }}</td>
                                        <td class="px-4 py-3 text-gray-600">{{ $row->kehadiran_count }} tercatat</td>
                                    @else
                                        <td class="px-4 py-3 font-medium text-gray-900">{{ $row->nama_kampung }}</td>
                                        <td class="px-4 py-3 text-gray-600">{{ $row->checkins_count }}</td>
                                        <td class="px-4 py-3 text-gray-600">{{ collect([$row->desa, $row->kecamatan, $row->kabupaten])->filter()->implode(', ') ?: '-' }}</td>
                                    @endif
                                    <td class="px-4 py-3">
                                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold {{ $statusPill($row->status_validasi) }}">{{ $statusLabel($row->status_validasi) }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-gray-500 whitespace-nowrap">{{ $row->created_at->locale('id')->isoFormat('D MMM YYYY') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ count($t['kolom']) + 1 }}" class="px-6 py-8 text-center text-gray-400 italic">Belum ada data.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach

        {{-- ===== RIWAYAT POIN ===== --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-base font-bold text-gray-900">Riwayat Transaksi Poin</h2>
                <p class="text-xs text-gray-500 mt-0.5">{{ $transaksi->count() }} transaksi &middot; {{ number_format($poinValid) }} poin valid</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wide">
                        <tr>
                            <th class="px-6 py-3 text-left w-10">#</th>
                            <th class="px-4 py-3 text-left">Tanggal</th>
                            <th class="px-4 py-3 text-left">Aktivitas</th>
                            <th class="px-4 py-3 text-left">Entitas</th>
                            <th class="px-4 py-3 text-left">Poin</th>
                            <th class="px-4 py-3 text-left">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($transaksi as $i => $tx)
                            @php $target = $targetNama[$tx->id] ?? null; @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-3 text-gray-400">{{ $i + 1 }}</td>
                                <td class="px-4 py-3 text-gray-500 whitespace-nowrap">{{ $tx->created_at->locale('id')->isoFormat('D MMM YYYY') }}</td>
                                <td class="px-4 py-3 text-gray-700">
                                    {{ $target['label'] ?? ucfirst($tx->related_type) }}
                                    <span class="text-xs text-gray-400">({{ $tx->jenis_aksi }})</span>
                                </td>
                                <td class="px-4 py-3 text-gray-600">{{ $target['nama'] ?? '-' }}</td>
                                <td class="px-4 py-3 font-bold {{ $tx->status === 'valid' ? 'text-emerald-600' : 'text-gray-400 line-through' }}">+{{ $tx->poin }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold {{ $tx->status === 'valid' ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">
                                        {{ $tx->status === 'valid' ? 'Valid' : 'Dibatalkan' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-6 py-8 text-center text-gray-400 italic">Belum ada transaksi poin.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection
