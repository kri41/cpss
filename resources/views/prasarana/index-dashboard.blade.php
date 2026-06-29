@extends('layouts.app')

@section('title', 'Prasarana Olahraga - CPSS')

@section('content')
@php
    $items = $prasarana instanceof \Illuminate\Contracts\Pagination\Paginator ? $prasarana->getCollection() : $prasarana;
    $totalPrasarana = $prasarana instanceof \Illuminate\Contracts\Pagination\Paginator ? $prasarana->total() : $prasarana->count();
    $avgKondisi = $items->count() > 0 ? round($items->avg('average_kondisi'), 1) : 0;
    $wilayahTerbanyak = $items->countBy('kabupaten')->sortDesc()->keys()->first() ?? '-';
    $tervalidasi = $items->where('status_validasi', 'validated')->count();
@endphp

<div x-data="{ verifyOpen: false, unverifyOpen: false, selected: null }">
    <!-- Sticky Stats & Filter Bar -->
    <div class="sticky top-0 z-30 bg-gray-50/95 backdrop-blur border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <!-- Stats Cards -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-3 flex items-center gap-3">
                    <div class="p-2 bg-blue-100 rounded-lg text-blue-600"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg></div>
                    <div>
                        <p class="text-xs text-gray-500">Total Prasarana</p>
                        <p class="text-lg font-bold text-gray-900">{{ $totalPrasarana }}</p>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-3 flex items-center gap-3">
                    <div class="p-2 bg-amber-100 rounded-lg text-amber-600"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg></div>
                    <div>
                        <p class="text-xs text-gray-500">Rata-rata Kondisi</p>
                        <p class="text-lg font-bold text-gray-900">{{ $avgKondisi }}<span class="text-sm font-normal text-gray-400">/5</span></p>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-3 flex items-center gap-3">
                    <div class="p-2 bg-blue-100 rounded-lg text-blue-600"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg></div>
                    <div>
                        <p class="text-xs text-gray-500">Wilayah Terbanyak</p>
                        <p class="text-lg font-bold text-gray-900 truncate">{{ $wilayahTerbanyak }}</p>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-3 flex items-center gap-3">
                    <div class="p-2 bg-emerald-100 rounded-lg text-emerald-600"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg></div>
                    <div>
                        <p class="text-xs text-gray-500">Tervalidasi</p>
                        <p class="text-lg font-bold text-gray-900">{{ $tervalidasi }}</p>
                    </div>
                </div>
            </div>

            <!-- Filter Row -->
            <form method="GET" action="{{ route('dashboard.prasarana') }}" class="flex flex-wrap items-end gap-3">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Cari Fasilitas</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama fasilitas..." class="w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500 shadow-sm">
                </div>
                <div class="w-44">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Kabupaten</label>
                    <select name="kabupaten" class="w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500 shadow-sm" onchange="this.form.submit()">
                        <option value="">Semua</option>
                        @foreach($kabupatenList as $k)
                            <option value="{{ $k }}" {{ request('kabupaten') == $k ? 'selected' : '' }}>{{ $k }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-44">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Kecamatan</label>
                    <select name="kecamatan" class="w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500 shadow-sm" onchange="this.form.submit()">
                        <option value="">Semua</option>
                        @foreach($kecamatanList as $k)
                            <option value="{{ $k }}" {{ request('kecamatan') == $k ? 'selected' : '' }}>{{ $k }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-48">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Kategori Olahraga</label>
                    <select name="kategori" class="w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500 shadow-sm" onchange="this.form.submit()">
                        <option value="">Semua</option>
                        @foreach($kategoriList as $k)
                            <option value="{{ $k }}" {{ request('kategori') == $k ? 'selected' : '' }}>{{ $k }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition shadow-sm">Filter</button>
                    <a href="{{ route('dashboard.prasarana') }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-50 transition shadow-sm">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- List Prasarana -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="grid grid-cols-1 gap-4">
            @forelse($prasarana as $item)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                        <div class="flex items-start gap-4 flex-1 min-w-0">
                            @if($item->foto_path)
                                <img src="{{ Storage::url($item->foto_path) }}" alt="" class="h-14 w-14 rounded-xl object-cover shrink-0 bg-gray-100">
                            @else
                                <div class="h-14 w-14 rounded-xl bg-blue-50 border border-blue-100 text-blue-600 flex items-center justify-center shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                                </div>
                            @endif
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h3 class="text-base font-semibold text-gray-900 truncate">{{ $item->nama_fasilitas }}</h3>
                                    @if($item->status_validasi === 'validated')
                                        <svg class="h-5 w-5 text-emerald-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                    @else
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-amber-50 text-amber-600 border border-amber-100">Pending</span>
                                    @endif
                                </div>
                                <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-2 text-sm text-gray-500">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-xs font-medium bg-blue-50 text-blue-700">{{ $item->kategori_olahraga }}</span>
                                    <span class="inline-flex items-center gap-1"><svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>{{ $item->desa ?? '-' }}, {{ $item->kecamatan ?? '-' }}</span>
                                    <span class="inline-flex items-center gap-1 text-xs">Kondisi: <strong class="text-gray-700">{{ $item->average_kondisi }}/5</strong><span class="inline-block w-12 h-1.5 bg-gray-200 rounded-full overflow-hidden align-middle ml-1"><span class="block h-full {{ $item->average_kondisi >= 4 ? 'bg-green-500' : ($item->average_kondisi >= 3 ? 'bg-blue-500' : ($item->average_kondisi >= 2 ? 'bg-amber-400' : 'bg-red-500')) }}" style="width: {{ min(100, max(0, ($item->average_kondisi / 5) * 100)) }}%"></span></span></span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 self-end sm:self-start shrink-0">
                            <a href="{{ route('prasarana.show', $item) }}" class="p-2 rounded-lg text-blue-600 hover:bg-blue-50 transition-colors" title="Detail"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg></a>
                            @auth
                                @if(auth()->user()->canEdit($item))
                                    <a href="{{ route('prasarana.edit', $item) }}" class="p-2 rounded-lg text-amber-600 hover:bg-amber-50 transition-colors" title="Edit"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg></a>
                                @endif
                                @if(auth()->user()->isAdmin() || auth()->user()->isRelawan())
                                    @if($item->status_validasi === 'pending')
                                        <button @click="selected = { id: {{ $item->id }}, nama: '{{ $item->nama_fasilitas }}', kategori: '{{ $item->kategori_olahraga }}', wilayah: '{{ $item->desa ?? '-' }} / {{ $item->kecamatan ?? '-' }} / {{ $item->kabupaten ?? '-' }}', kondisi: '{{ $item->average_kondisi }}', status: '{{ $item->status_validasi }}', action: '{{ route('prasarana.validate', $item) }}' }; verifyOpen = true" class="p-2 rounded-lg text-emerald-600 hover:bg-emerald-50 transition-colors" title="Verifikasi"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg></button>
                                    @else
                                        <button @click="selected = { id: {{ $item->id }}, nama: '{{ $item->nama_fasilitas }}', kategori: '{{ $item->kategori_olahraga }}', wilayah: '{{ $item->desa ?? '-' }} / {{ $item->kecamatan ?? '-' }} / {{ $item->kabupaten ?? '-' }}', kondisi: '{{ $item->average_kondisi }}', status: '{{ $item->status_validasi }}', action: '{{ route('prasarana.cancel-validate', $item) }}' }; unverifyOpen = true" class="p-2 rounded-lg text-red-600 hover:bg-red-50 transition-colors" title="Batalkan Verifikasi"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg></button>
                                    @endif
                                @endif
                            @endauth
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
                    <div class="flex flex-col items-center">
                        <div class="p-4 bg-gray-100 rounded-full mb-4"><svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg></div>
                        <p class="text-gray-500 text-sm">Tidak ada data prasarana.</p>
                    </div>
                </div>
            @endforelse
        </div>

        @if($prasarana instanceof \Illuminate\Contracts\Pagination\Paginator && $prasarana->hasPages())
            <div class="mt-6">{{ $prasarana->links() }}</div>
        @endif
    </div>

    <!-- Modal Verifikasi -->
    <div x-show="verifyOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/50 backdrop-blur-sm" @click="verifyOpen = false">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-lg mx-4 p-6" @click.stop>
            <h3 class="text-lg font-bold text-gray-900 mb-1">Verifikasi Prasarana</h3>
            <p class="text-sm text-gray-500 mb-4">Pastikan data berikut sudah benar sebelum memverifikasi.</p>

            <!-- Preview Data -->
            <div class="bg-gray-50 rounded-lg border border-gray-200 p-4 mb-5 space-y-2 text-sm">
                <div class="flex justify-between"><span class="text-gray-500">Nama Fasilitas</span><span class="font-semibold text-gray-900 text-right max-w-[60%]" x-text="selected?.nama"></span></div>
                <div class="flex justify-between"><span class="text-gray-500">Kategori</span><span class="font-medium text-gray-900" x-text="selected?.kategori"></span></div>
                <div class="flex justify-between"><span class="text-gray-500">Wilayah</span><span class="font-medium text-gray-900 text-right max-w-[60%]" x-text="selected?.wilayah"></span></div>
                <div class="flex justify-between"><span class="text-gray-500">Rata-rata Kondisi</span><span class="font-medium text-gray-900" x-text="selected?.kondisi + ' / 5'"></span></div>
                <div class="flex justify-between"><span class="text-gray-500">Status Saat Ini</span><span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-amber-50 text-amber-700 border border-amber-100">Pending</span></div>
            </div>

            <form :action="selected?.action" method="POST">
                @csrf
                <input type="hidden" name="_method" value="PATCH">
                <div class="space-y-3 mb-5">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Nama Verifikator</label>
                        <input type="text" value="{{ auth()->user()->name ?? '' }}" readonly class="w-full rounded-lg border-gray-200 bg-gray-50 text-sm text-gray-700">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Catatan / Komentar</label>
                        <textarea name="komentar_validasi" rows="3" class="w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500 shadow-sm" placeholder="Tulis catatan jika diperlukan..."></textarea>
                    </div>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" @click="verifyOpen = false" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 transition">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-medium hover:bg-emerald-700 transition shadow-sm">Verifikasi</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Batalkan Verifikasi -->
    <div x-show="unverifyOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/50 backdrop-blur-sm" @click="unverifyOpen = false">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-lg mx-4 p-6" @click.stop>
            <h3 class="text-lg font-bold text-gray-900 mb-1">Batalkan Verifikasi</h3>
            <p class="text-sm text-gray-500 mb-4">Data yang akan dibatalkan verifikasinya:</p>

            <!-- Preview Data -->
            <div class="bg-gray-50 rounded-lg border border-gray-200 p-4 mb-5 space-y-2 text-sm">
                <div class="flex justify-between"><span class="text-gray-500">Nama Fasilitas</span><span class="font-semibold text-gray-900 text-right max-w-[60%]" x-text="selected?.nama"></span></div>
                <div class="flex justify-between"><span class="text-gray-500">Kategori</span><span class="font-medium text-gray-900" x-text="selected?.kategori"></span></div>
                <div class="flex justify-between"><span class="text-gray-500">Wilayah</span><span class="font-medium text-gray-900 text-right max-w-[60%]" x-text="selected?.wilayah"></span></div>
                <div class="flex justify-between"><span class="text-gray-500">Rata-rata Kondisi</span><span class="font-medium text-gray-900" x-text="selected?.kondisi + ' / 5'"></span></div>
                <div class="flex justify-between"><span class="text-gray-500">Status Saat Ini</span><span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-100">Tervalidasi</span></div>
            </div>

            <form :action="selected?.action" method="POST">
                @csrf
                <input type="hidden" name="_method" value="PATCH">
                <p class="text-sm text-gray-600 mb-5">Apakah Anda yakin ingin membatalkan verifikasi data ini? Data akan kembali ke status <strong>Pending</strong>.</p>
                <div class="flex justify-end gap-2">
                    <button type="button" @click="unverifyOpen = false" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 transition">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700 transition shadow-sm">Batalkan Verifikasi</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
