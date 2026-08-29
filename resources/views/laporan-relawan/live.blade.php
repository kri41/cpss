<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <meta http-equiv="refresh" content="180">
    <title>Laporan Kinerja — {{ $relawan->name }} | Dataraga</title>
    <link rel="icon" href="/storage/logo.png" type="image/png">
    @vite(['resources/css/app.css'])
</head>
@php
    $idDate = fn ($d, $fmt = 'D MMMM YYYY') => $d ? \Carbon\Carbon::parse($d)->locale('id')->isoFormat($fmt) : '-';
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
    $statusLabel = fn ($s) => match ($s) { 'validated' => 'Tervalidasi', 'rejected' => 'Ditolak', default => 'Menunggu' };
@endphp
<body class="antialiased font-sans text-gray-800" style="background: linear-gradient(180deg,#1e3a8a 0%,#2563eb 22%,#dbeafe 55%,#f1f5f9 100%); background-attachment: fixed; min-height:100vh;">

<div class="max-w-4xl mx-auto px-4 sm:px-6 py-8 space-y-5">

    {{-- ===== HEADER ===== --}}
    <div class="flex items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <img src="/storage/logo.png" alt="Dataraga" class="h-11 w-11 object-contain brightness-0 invert">
            <div>
                <p class="text-white font-black text-lg tracking-tight leading-none">Dataraga</p>
                <p class="text-white/60 text-[11px] font-medium">Laporan Kinerja Relawan &middot; Live</p>
            </div>
        </div>
        <a href="{{ route('public-report.pdf', $relawan->public_report_token) }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-white/15 hover:bg-white/25 border border-white/25 text-white text-sm font-semibold rounded-xl backdrop-blur transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            PDF
        </a>
    </div>

    {{-- ===== IDENTITAS ===== --}}
    <div class="bg-white rounded-2xl shadow-lg border border-white/40 overflow-hidden">
        <div class="h-14 bg-gradient-to-r from-blue-700 to-sky-500"></div>
        <div class="px-6 pb-6">
            <div class="flex flex-col sm:flex-row sm:items-end gap-4 -mt-7">
                <div class="w-16 h-16 rounded-2xl border-4 border-white shadow-md bg-gradient-to-br from-blue-500 to-sky-400 flex items-center justify-center text-white font-bold text-2xl shrink-0">
                    {{ strtoupper(substr($relawan->name, 0, 1)) }}
                </div>
                <div class="pb-0.5">
                    <h1 class="text-xl font-bold text-gray-900">{{ $relawan->name }}</h1>
                    <p class="text-xs text-gray-500 mt-0.5">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 font-semibold">Relawan</span>
                        <span class="ml-2">{{ collect([$relawan->kecamatan, $relawan->kabupaten])->filter()->implode(', ') ?: 'Wilayah belum diatur' }}</span>
                        <span class="ml-2">&middot; Bergabung {{ $idDate($relawan->created_at, 'MMM YYYY') }}</span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== KPI ===== --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="bg-gradient-to-br from-blue-600 to-sky-500 rounded-2xl shadow p-4 text-white">
            <p class="text-[11px] font-medium text-blue-100 uppercase tracking-wide">Total Poin</p>
            <p class="text-2xl font-extrabold mt-1">{{ number_format($relawan->total_poin ?? 0) }}</p>
            <p class="text-[11px] text-blue-200 mt-0.5">+{{ number_format($poinBulanIni) }} bulan ini</p>
        </div>
        <div class="bg-white rounded-2xl shadow p-4">
            <p class="text-[11px] font-medium text-gray-500 uppercase tracking-wide">Peringkat</p>
            <p class="text-2xl font-extrabold text-gray-900 mt-1">#{{ $rank }}</p>
            <p class="text-[11px] text-gray-400 mt-0.5">dari {{ $totalRelawan }} relawan</p>
        </div>
        <div class="bg-white rounded-2xl shadow p-4">
            <p class="text-[11px] font-medium text-gray-500 uppercase tracking-wide">Kontribusi</p>
            <p class="text-2xl font-extrabold text-gray-900 mt-1">{{ $totalKontribusi }}</p>
            <p class="text-[11px] text-emerald-600 mt-0.5">{{ $totalValid }} tervalidasi</p>
        </div>
        <div class="bg-white rounded-2xl shadow p-4">
            <p class="text-[11px] font-medium text-gray-500 uppercase tracking-wide">Lencana</p>
            <p class="text-2xl font-extrabold text-gray-900 mt-1">{{ $badges->count() }}</p>
            <p class="text-[11px] text-gray-400 mt-0.5">pencapaian</p>
        </div>
    </div>

    {{-- ===== POIN PER KATEGORI ===== --}}
    <div class="bg-white rounded-2xl shadow p-5">
        <h2 class="text-sm font-bold text-gray-900 mb-3">Perolehan Poin per Kategori</h2>
        @if($poinPerKategori->sum() === 0)
            <p class="text-sm text-gray-400">Belum ada poin tervalidasi.</p>
        @else
            <div class="space-y-2.5">
                @foreach($kategoriMeta as $key => $meta)
                    @php $val = $poinPerKategori[$key] ?? 0; @endphp
                    <div class="flex items-center gap-3">
                        <span class="w-28 sm:w-32 text-xs text-gray-600 shrink-0">{{ $meta['label'] }}</span>
                        <div class="flex-1 h-2.5 bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full {{ $meta['bar'] }} rounded-full" style="width: {{ round($val / $poinMax * 100) }}%"></div>
                        </div>
                        <span class="w-12 text-right text-xs font-bold text-gray-900">{{ number_format($val) }}</span>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- ===== LENCANA ===== --}}
    @if($badges->isNotEmpty())
    <div class="bg-white rounded-2xl shadow p-5">
        <h2 class="text-sm font-bold text-gray-900 mb-3">Lencana</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
            @foreach($badges as $b)
                <div class="flex items-start gap-3 p-3 rounded-xl bg-blue-50 border border-blue-100">
                    <div class="w-8 h-8 rounded-lg bg-blue-600 text-white flex items-center justify-center shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-bold text-gray-900">{{ $b->nama }}</p>
                        <p class="text-[11px] text-gray-500">{{ $b->deskripsi }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ===== TABEL KONTRIBUSI ===== --}}
    @php
        $tabel = [
            ['judul' => 'Prasarana Olahraga',   'data' => $prasarana,   'stat' => $stats['prasarana']],
            ['judul' => 'Klub / Komunitas',     'data' => $clubs,       'stat' => $stats['clubs']],
            ['judul' => 'Event Olahraga',       'data' => $events,      'stat' => $stats['events']],
            ['judul' => 'Partisipasi Kegiatan', 'data' => $partisipasi, 'stat' => $stats['partisipasi']],
            ['judul' => 'Kampung Olahraga',     'data' => $kampung,     'stat' => $stats['kampung']],
        ];
        $namaOf = fn ($row) => $row->nama_fasilitas ?? $row->nama_club ?? $row->nama_event ?? $row->lokasi_observasi ?? $row->nama_kampung ?? '-';
    @endphp

    @foreach($tabel as $t)
        @if($t['data']->isNotEmpty())
        <div class="bg-white rounded-2xl shadow overflow-hidden">
            <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between">
                <h2 class="text-sm font-bold text-gray-900">{{ $t['judul'] }}</h2>
                <span class="text-[11px] text-gray-500">{{ $t['stat']['total'] }} entri &middot; {{ $t['stat']['valid'] }} tervalidasi</span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-[11px] text-gray-500 uppercase tracking-wide">
                        <tr>
                            <th class="px-5 py-2.5 text-left w-8">#</th>
                            <th class="px-3 py-2.5 text-left">Nama</th>
                            <th class="px-3 py-2.5 text-left">Lokasi</th>
                            <th class="px-3 py-2.5 text-left">Status</th>
                            <th class="px-3 py-2.5 text-left">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($t['data'] as $i => $row)
                            <tr>
                                <td class="px-5 py-2.5 text-gray-400">{{ $i + 1 }}</td>
                                <td class="px-3 py-2.5 font-medium text-gray-900">{{ $namaOf($row) }}</td>
                                <td class="px-3 py-2.5 text-gray-600">{{ collect([$row->desa, $row->kecamatan, $row->kabupaten])->filter()->implode(', ') ?: '-' }}</td>
                                <td class="px-3 py-2.5"><span class="inline-flex px-2 py-0.5 rounded-full text-[11px] font-semibold {{ $statusPill($row->status_validasi) }}">{{ $statusLabel($row->status_validasi) }}</span></td>
                                <td class="px-3 py-2.5 text-gray-500 whitespace-nowrap">{{ $idDate($row->created_at, 'D MMM YYYY') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    @endforeach

    {{-- ===== FOOTER ===== --}}
    <div class="text-center text-slate-500 text-[11px] py-6 space-y-1">
        <p><span class="inline-block w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1 align-middle"></span>Diperbarui otomatis tiap 3 menit &middot; data per {{ now()->timezone('Asia/Jakarta')->locale('id')->isoFormat('D MMM YYYY, HH:mm') }} WIB</p>
        <p>Laporan resmi dihasilkan oleh sistem <span class="font-semibold text-slate-700">Dataraga</span> &mdash; Kamu Gerak, Indonesia Tahu</p>
    </div>

</div>
</body>
</html>
