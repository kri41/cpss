<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <meta http-equiv="refresh" content="180">
    <title>Rekap Status Relawan | Dataraga</title>
    <link rel="icon" href="/storage/logo.png" type="image/png">
    @vite(['resources/css/app.css'])
</head>
<body class="antialiased font-sans text-gray-800" style="background: linear-gradient(180deg,#1e3a8a 0%,#2563eb 22%,#dbeafe 55%,#f1f5f9 100%); background-attachment: fixed; min-height:100vh;">

<div class="max-w-4xl mx-auto px-4 sm:px-6 py-8 space-y-5">

    {{-- ===== HEADER ===== --}}
    <div class="flex items-center gap-3">
        <img src="/storage/logo.png" alt="Dataraga" class="h-11 w-11 object-contain brightness-0 invert">
        <div>
            <p class="text-white font-black text-lg tracking-tight leading-none">Dataraga</p>
            <p class="text-white/60 text-[11px] font-medium">Rekap Status Relawan &middot; Live</p>
        </div>
    </div>

    {{-- ===== RINGKASAN ===== --}}
    <div class="bg-white rounded-2xl shadow-lg border border-white/40 p-5 sm:p-6">
        <h1 class="text-lg sm:text-xl font-bold text-gray-900">Status Pemenuhan Poin Relawan</h1>
        <p class="text-xs text-gray-500 mt-1">Ambang sertifikasi: <strong>{{ $minPoin }} poin tervalidasi</strong>. Diperbarui otomatis tiap 3 menit.</p>

        <div class="grid grid-cols-3 gap-3 mt-4">
            <div class="rounded-xl bg-gray-50 border border-gray-100 p-3 text-center">
                <p class="text-2xl font-extrabold text-gray-900">{{ $agg['total_relawan'] }}</p>
                <p class="text-[11px] text-gray-500 uppercase tracking-wide mt-0.5">Total Relawan</p>
            </div>
            <div class="rounded-xl bg-emerald-50 border border-emerald-100 p-3 text-center">
                <p class="text-2xl font-extrabold text-emerald-700">{{ $agg['memenuhi'] }}</p>
                <p class="text-[11px] text-emerald-600 uppercase tracking-wide mt-0.5">Sudah Memenuhi</p>
            </div>
            <div class="rounded-xl bg-amber-50 border border-amber-100 p-3 text-center">
                <p class="text-2xl font-extrabold text-amber-700">{{ $agg['belum'] }}</p>
                <p class="text-[11px] text-amber-600 uppercase tracking-wide mt-0.5">Belum Memenuhi</p>
            </div>
        </div>
    </div>

    {{-- ===== CARI ===== --}}
    <div class="bg-white rounded-2xl shadow p-4">
        <input type="text" id="cari-relawan" placeholder="Cari nama relawan..."
               class="w-full rounded-xl border-gray-200 text-sm focus:border-blue-500 focus:ring-blue-500"
               oninput="document.querySelectorAll('#tabel-relawan tr').forEach(function(tr){ tr.style.display = tr.dataset.nama.includes(this.value.toLowerCase()) ? '' : 'none'; }, this)">
    </div>

    {{-- ===== TABEL ===== --}}
    <div class="bg-white rounded-2xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-[11px] text-gray-500 uppercase tracking-wide">
                    <tr>
                        <th class="px-5 py-2.5 text-left w-8">#</th>
                        <th class="px-3 py-2.5 text-left">Nama</th>
                        <th class="px-3 py-2.5 text-left">Wilayah</th>
                        <th class="px-3 py-2.5 text-right">Poin</th>
                        <th class="px-3 py-2.5 text-left">Status</th>
                    </tr>
                </thead>
                <tbody id="tabel-relawan" class="divide-y divide-gray-100">
                    @forelse($relawan as $i => $r)
                        @php $penuhi = ($r->total_poin ?? 0) >= $minPoin; @endphp
                        <tr data-nama="{{ strtolower($r->name) }}">
                            <td class="px-5 py-2.5 text-gray-400">{{ $i + 1 }}</td>
                            <td class="px-3 py-2.5 font-medium text-gray-900">{{ $r->name }}</td>
                            <td class="px-3 py-2.5 text-gray-500">{{ collect([$r->kecamatan, $r->kabupaten])->filter()->implode(', ') ?: '-' }}</td>
                            <td class="px-3 py-2.5 text-right font-bold text-gray-900">{{ number_format($r->total_poin ?? 0) }}</td>
                            <td class="px-3 py-2.5">
                                @if($penuhi)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-100 text-emerald-700">&check; Memenuhi</span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-amber-100 text-amber-700">Belum &mdash; butuh {{ $minPoin - ($r->total_poin ?? 0) }} poin lagi</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-10 text-center text-gray-400 italic">Belum ada data relawan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ===== FOOTER ===== --}}
    <div class="text-center text-slate-500 text-[11px] py-6 space-y-1">
        <p><span class="inline-block w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1 align-middle"></span>Diperbarui otomatis tiap 3 menit &middot; data per {{ now()->timezone('Asia/Jakarta')->locale('id')->isoFormat('D MMM YYYY, HH:mm') }} WIB</p>
        <p>Laporan resmi dihasilkan oleh sistem <span class="font-semibold text-slate-700">Dataraga</span> &mdash; Kamu Gerak, Indonesia Tahu</p>
    </div>

</div>
</body>
</html>
