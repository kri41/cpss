@extends('layouts.app')

@section('title', 'Pembanding Relawan vs Pakar - Dataraga')

@section('content')
@php
    $rl = \App\Models\Prasarana::RATING_LABELS;
    $b = fn ($v) => $v === null ? '—' : ($v ? 'Ya' : 'Tidak');
    $fmtMnt = fn ($d) => $d ? number_format($d / 60, 1) . ' mnt' : '—';
@endphp
<div class="py-6">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <a href="{{ route('asesmen-pakar.index') }}" class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800 font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    Kembali
                </a>
                <h1 class="text-2xl font-bold text-gray-900 mt-1">Pembanding: Relawan vs Asesor Pakar</h1>
                <p class="text-sm text-gray-500 mt-1">{{ $pasangan->count() }} prasarana berpasangan. Ekspor CSV untuk hitung Cohen's Kappa (RM3) &amp; uji efisiensi waktu (RM4).</p>
            </div>
            <a href="{{ route('asesmen-pakar.banding.csv') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl transition shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Ekspor CSV Berpasangan
            </a>
        </div>

        @if($pasangan->isEmpty())
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-16 text-center text-gray-400">
                Belum ada prasarana yang punya data asesmen pakar.
                <a href="{{ route('asesmen-pakar.create') }}" class="text-blue-600 font-semibold">Input lembar asesor →</a>
            </div>
        @else

        {{-- Ringkasan kesepakatan cepat --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-base font-bold text-gray-900">Persentase Kesepakatan (indikatif)</h2>
            <p class="text-xs text-gray-400 mb-4">Sekadar cek cepat kecocokan mentah. Nilai Kappa yang sesungguhnya dihitung dari CSV (memperhitungkan kesepakatan kebetulan).</p>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                @foreach(\App\Models\AsesmenPakar::KONDISI as $key => $label)
                    @php $r = $ringkasan['kondisi'][$key]; @endphp
                    <div class="rounded-xl bg-gray-50 border border-gray-100 p-3">
                        <p class="text-[11px] text-gray-500 uppercase tracking-wide truncate">{{ $label }}</p>
                        <p class="text-lg font-extrabold text-gray-900 mt-0.5">{{ $r['persen'] !== null ? $r['persen'].'%' : '—' }}</p>
                        <p class="text-[11px] text-gray-400">{{ $r['sepakat'] }}/{{ $r['total'] }} sepakat</p>
                    </div>
                @endforeach
                @foreach(\App\Models\AsesmenPakar::AKSES as $key => $label)
                    @php $r = $ringkasan['akses'][$key]; @endphp
                    <div class="rounded-xl bg-blue-50 border border-blue-100 p-3">
                        <p class="text-[11px] text-blue-600 uppercase tracking-wide truncate">{{ $label }}</p>
                        <p class="text-lg font-extrabold text-blue-800 mt-0.5">{{ $r['persen'] !== null ? $r['persen'].'%' : '—' }}</p>
                        <p class="text-[11px] text-blue-400">{{ $r['sepakat'] }}/{{ $r['total'] }} sepakat</p>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Per prasarana --}}
        <div class="space-y-4" x-data="{ open: null }">
            @foreach($pasangan as $i => $x)
                @php $p = $x['prasarana']; $a = $x['asesmen']; $d = $x['durasi']; @endphp
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <button type="button" @click="open = (open === {{ $i }} ? null : {{ $i }})"
                            class="w-full flex items-center justify-between gap-3 px-5 py-4 text-left hover:bg-gray-50">
                        <div class="min-w-0">
                            <p class="font-bold text-gray-900 truncate">#{{ $p->id }} — {{ $p->nama_fasilitas }}</p>
                            <p class="text-xs text-gray-400">{{ collect([$p->kecamatan, $p->kabupaten])->filter()->implode(', ') }} &middot; Relawan: {{ $p->user?->name ?? '-' }} &middot; Asesor: {{ $a->nama_asesor }}</p>
                        </div>
                        <svg class="h-4 w-4 text-gray-400 shrink-0" :class="open === {{ $i }} && 'rotate-180'" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>

                    <div x-show="open === {{ $i }}" x-cloak x-transition class="border-t border-gray-100 p-5 space-y-5">
                        {{-- Waktu --}}
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-sm">
                            <div class="rounded-lg bg-gray-50 p-3">
                                <p class="text-[11px] text-gray-500 uppercase">Relawan — Durasi Input</p>
                                <p class="font-bold text-gray-900">{{ $fmtMnt($d?->durasi_detik) }}</p>
                                <p class="text-[11px] text-gray-400">{{ $d?->sumber ?? 'tidak tercatat' }}</p>
                            </div>
                            <div class="rounded-lg bg-gray-50 p-3">
                                <p class="text-[11px] text-gray-500 uppercase">Asesor — Menilai (T1)</p>
                                <p class="font-bold text-gray-900">{{ $fmtMnt($a->durasi_asesmen_detik) }}</p>
                            </div>
                            <div class="rounded-lg bg-gray-50 p-3">
                                <p class="text-[11px] text-gray-500 uppercase">Asesor — Laporan (T2)</p>
                                <p class="font-bold text-gray-900">{{ $fmtMnt($a->durasi_laporan_detik) }}</p>
                            </div>
                            <div class="rounded-lg bg-blue-50 p-3">
                                <p class="text-[11px] text-blue-600 uppercase">Asesor — Total</p>
                                <p class="font-bold text-blue-800">{{ $fmtMnt(($a->durasi_asesmen_detik ?? 0) + ($a->durasi_laporan_detik ?? 0)) }}</p>
                            </div>
                        </div>

                        {{-- Kondisi --}}
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead class="bg-gray-50 text-[11px] text-gray-500 uppercase">
                                    <tr><th class="px-3 py-2 text-left">Komponen</th><th class="px-3 py-2 text-left">Relawan</th><th class="px-3 py-2 text-left">Asesor</th><th class="px-3 py-2 text-center w-12"></th></tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach(\App\Models\AsesmenPakar::KONDISI as $key => $label)
                                        @php $vr = $p->{$key}; $va = $a->{$key}; $match = ($vr !== null && $va !== null) ? ((int)$vr === (int)$va) : null; @endphp
                                        <tr>
                                            <td class="px-3 py-2 text-gray-600">{{ $label }}</td>
                                            <td class="px-3 py-2 text-gray-900">{{ $vr ? $vr.' — '.($rl[$vr] ?? '') : '—' }}</td>
                                            <td class="px-3 py-2 text-gray-900">{{ $va ? $va.' — '.($rl[$va] ?? '') : '—' }}</td>
                                            <td class="px-3 py-2 text-center">
                                                @if($match === true)<span class="text-emerald-600">✓</span>
                                                @elseif($match === false)<span class="text-red-500">✗</span>
                                                @else<span class="text-gray-300">–</span>@endif
                                            </td>
                                        </tr>
                                    @endforeach
                                    @foreach(\App\Models\AsesmenPakar::AKSES as $key => $label)
                                        @php $vr = $p->{$key}; $va = $a->{$key}; $match = ($vr !== null && $va !== null) ? ((bool)$vr === (bool)$va) : null; @endphp
                                        <tr class="bg-blue-50/40">
                                            <td class="px-3 py-2 text-gray-600">{{ $label }}</td>
                                            <td class="px-3 py-2 text-gray-900">{{ $b($vr) }}</td>
                                            <td class="px-3 py-2 text-gray-900">{{ $b($va) }}</td>
                                            <td class="px-3 py-2 text-center">
                                                @if($match === true)<span class="text-emerald-600">✓</span>
                                                @elseif($match === false)<span class="text-red-500">✗</span>
                                                @else<span class="text-gray-300">–</span>@endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                            <div><span class="text-xs text-gray-500 uppercase">Kategori — Relawan:</span> {{ $p->jenisOlahraga->pluck('nama')->implode(', ') ?: '—' }}</div>
                            <div><span class="text-xs text-gray-500 uppercase">Kategori — Asesor:</span> {{ \App\Models\JenisOlahraga::whereIn('id', $a->kategori_olahraga_ids ?? [])->pluck('nama')->implode(', ') ?: '—' }}</div>
                        </div>
                        @if($a->catatan)
                            <p class="text-xs text-gray-500"><span class="font-semibold">Catatan asesor:</span> {{ $a->catatan }}</p>
                        @endif
                        <a href="{{ route('asesmen-pakar.edit', $a) }}" class="text-xs text-blue-600 font-semibold">Edit data asesmen ini →</a>
                    </div>
                </div>
            @endforeach
        </div>
        @endif
    </div>
</div>
@endsection
