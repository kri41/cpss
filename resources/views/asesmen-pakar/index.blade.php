@extends('layouts.app')

@section('title', 'Asesmen Pakar - Dataraga')

@section('content')
<div class="py-6">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Asesmen Pakar</h1>
                <p class="text-sm text-gray-500 mt-1">Transkripsi lembar audit asesor &amp; pembandingan dengan data relawan (uji kesepakatan &amp; efisiensi).</p>
            </div>
            <a href="{{ route('asesmen-pakar.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl transition shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Input Lembar Asesor
            </a>
        </div>

        @if(session('success'))
            <div class="p-3 bg-green-50 border border-green-200 rounded-xl text-sm text-green-700">{{ session('success') }}</div>
        @endif

        {{-- 3 langkah alur --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('asesmen-pakar.lokasi') }}" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:border-blue-200 hover:shadow-md transition">
                <div class="flex items-center gap-2 text-blue-700 font-bold text-sm mb-1">
                    <span class="w-6 h-6 rounded-full bg-blue-100 flex items-center justify-center text-xs">1</span> Daftar Lokasi Sasaran
                </div>
                <p class="text-xs text-gray-500">Cetak/bagikan ke asesor sebelum berangkat. Hanya nama, koordinat, wilayah &mdash; <strong>tanpa data kondisi</strong> (blind).</p>
            </a>
            <a href="{{ route('asesmen-pakar.create') }}" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:border-blue-200 hover:shadow-md transition">
                <div class="flex items-center gap-2 text-blue-700 font-bold text-sm mb-1">
                    <span class="w-6 h-6 rounded-full bg-blue-100 flex items-center justify-center text-xs">2</span> Transkripsi Lembar
                </div>
                <p class="text-xs text-gray-500">Salin hasil audit kertas asesor ke sistem: kondisi, aksesibilitas, kategori, waktu kerja.</p>
            </a>
            <a href="{{ route('asesmen-pakar.banding') }}" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:border-blue-200 hover:shadow-md transition">
                <div class="flex items-center gap-2 text-blue-700 font-bold text-sm mb-1">
                    <span class="w-6 h-6 rounded-full bg-blue-100 flex items-center justify-center text-xs">3</span> Pembanding &amp; Ekspor
                </div>
                <p class="text-xs text-gray-500">Relawan vs asesor berdampingan + <strong>Ekspor CSV</strong> untuk hitung Cohen's Kappa &amp; uji efisiensi.</p>
            </a>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex items-center gap-6">
            <div>
                <p class="text-3xl font-extrabold text-gray-900">{{ $sudahDiasesmen }}<span class="text-lg text-gray-300 font-normal">/{{ $totalPrasarana }}</span></p>
                <p class="text-xs text-gray-500 uppercase tracking-wide">Prasarana sudah diasesmen pakar</p>
            </div>
            <div class="flex-1 h-2 bg-gray-100 rounded-full overflow-hidden">
                <div class="h-full bg-blue-500 rounded-full" style="width: {{ $totalPrasarana ? round($sudahDiasesmen / $totalPrasarana * 100) : 0 }}%"></div>
            </div>
        </div>

        {{-- Daftar --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h2 class="text-base font-bold text-gray-900">Data Asesmen ({{ $asesmen->total() }})</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wide">
                        <tr>
                            <th class="px-5 py-3 text-left">Prasarana</th>
                            <th class="px-4 py-3 text-left">Asesor</th>
                            <th class="px-4 py-3 text-left">Tanggal</th>
                            <th class="px-4 py-3 text-left">Durasi Asesmen</th>
                            <th class="px-4 py-3 text-left">Durasi Laporan</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($asesmen as $a)
                            <tr class="hover:bg-gray-50">
                                <td class="px-5 py-3">
                                    <p class="font-medium text-gray-900">{{ $a->prasarana?->nama_fasilitas ?? '(dihapus)' }}</p>
                                    <p class="text-xs text-gray-400">{{ collect([$a->prasarana?->kecamatan, $a->prasarana?->kabupaten])->filter()->implode(', ') }}</p>
                                </td>
                                <td class="px-4 py-3 text-gray-700">{{ $a->nama_asesor }}</td>
                                <td class="px-4 py-3 text-gray-500 whitespace-nowrap">{{ $a->tanggal_asesmen?->locale('id')->isoFormat('D MMM YYYY') ?? '-' }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ $a->durasi_asesmen_detik ? number_format($a->durasi_asesmen_detik / 60, 1) . ' mnt' : '-' }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ $a->durasi_laporan_detik ? number_format($a->durasi_laporan_detik / 60, 1) . ' mnt' : '-' }}</td>
                                <td class="px-4 py-3 text-right whitespace-nowrap">
                                    <a href="{{ route('asesmen-pakar.edit', $a) }}" class="text-blue-600 hover:text-blue-800 text-xs font-semibold">Edit</a>
                                    <form action="{{ route('asesmen-pakar.destroy', $a) }}" method="POST" class="inline" onsubmit="return confirm('Hapus data asesmen ini?')">
                                        @csrf @method('DELETE')
                                        <button class="text-red-600 hover:text-red-800 text-xs font-semibold ml-2">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-5 py-10 text-center text-gray-400 italic">Belum ada data asesmen. Mulai dari langkah 2 (Transkripsi Lembar).</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div>{{ $asesmen->links() }}</div>
    </div>
</div>
@endsection
