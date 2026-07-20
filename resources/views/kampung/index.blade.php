@extends('layouts.app')

@section('title', 'Kampung Olahraga - Dataraga')

@section('content')
@php
    $items = $kampungList->getCollection();
    $totalPendingUser = $items->where('status_validasi', 'pending')->count();
    $totalValidated   = $items->where('status_validasi', 'validated')->count();
@endphp

<div x-data="{ rejectOpen: false, selected: null }">

{{-- HEADER --}}
<div class="sticky top-14 lg:top-0 z-30 bg-white/90 backdrop-blur border-b border-gray-200/80">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 sm:py-4">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-2 sm:gap-3 mb-3">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-2 sm:p-3 flex items-center gap-2 sm:gap-3">
                <div class="p-2 bg-green-100 rounded-lg text-green-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Total</p>
                    <p class="text-lg font-bold text-gray-900">{{ $kampungList->total() }}</p>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-2 sm:p-3 flex items-center gap-2 sm:gap-3">
                <div class="p-2 bg-emerald-100 rounded-lg text-emerald-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Terverifikasi</p>
                    <p class="text-lg font-bold text-gray-900">{{ $totalValidated }}</p>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-2 sm:p-3 flex items-center gap-2 sm:gap-3">
                <div class="p-2 bg-amber-100 rounded-lg text-amber-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Menunggu</p>
                    <p class="text-lg font-bold text-gray-900">{{ $totalPending }}</p>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-2 sm:p-3 flex items-center gap-2 sm:gap-3">
                <div class="p-2 bg-blue-100 rounded-lg text-blue-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 3.5c0 1.5-1.5 3-3 3s-3-1.5-3-3 1.5-3 3-3 3 1.5 3 3z"/></svg>
                </div>
                <div>
                    <p class="text-xs text-gray-500">QR Aktif</p>
                    <p class="text-lg font-bold text-gray-900">{{ $items->whereNotNull('qr_token')->count() }}</p>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-between gap-3">
            <h1 class="text-base font-bold text-gray-800">Kampung Olahraga</h1>
            @if(auth()->user()->isAdmin() || auth()->user()->isRelawan())
            <div class="flex gap-2">
                @if(auth()->user()->isAdmin())
                <a href="{{ route('komponen-syarat.index') }}" class="flex items-center gap-1.5 px-3 py-2 text-xs bg-white border border-gray-200 text-gray-600 rounded-lg hover:bg-gray-50 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Komponen Syarat
                </a>
                @endif
                <a href="{{ route('kampung.create') }}" class="flex items-center gap-1.5 px-3 py-2 text-xs bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Tambah Kampung
                </a>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- CONTENT --}}
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6">

    @if(session('success'))
    <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-xl text-sm text-green-700">{{ session('success') }}</div>
    @endif

    @if($kampungList->isEmpty())
    <div class="text-center py-16">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 rounded-2xl mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
        </div>
        <h3 class="text-base font-semibold text-gray-700 mb-1">Belum ada Kampung Olahraga</h3>
        <p class="text-sm text-gray-500 mb-4">Daftarkan kampung olahraga untuk mendapatkan QR check-in.</p>
        @if(auth()->user()->isRelawan())
        <a href="{{ route('kampung.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition">Daftarkan Sekarang</a>
        @endif
    </div>
    @else
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        @foreach($kampungList as $kampung)
        @php
            $statusColor = match($kampung->status_validasi) {
                'validated' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                'rejected'  => 'bg-red-100 text-red-700 border-red-200',
                default     => 'bg-amber-100 text-amber-700 border-amber-200',
            };
            $statusLabel = match($kampung->status_validasi) {
                'validated' => 'Terverifikasi',
                'rejected'  => 'Ditolak',
                default     => 'Pending',
            };
            $totalCi = $kampung->checkins()->count();
        @endphp
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition overflow-hidden">
            <div class="p-4 sm:p-5">
                <div class="flex items-start justify-between mb-3">
                    <div class="flex-1 min-w-0 mr-2">
                        <h3 class="font-bold text-gray-900 text-base truncate">{{ $kampung->nama_kampung }}</h3>
                        <p class="text-xs text-gray-500 mt-0.5">
                            {{ collect([$kampung->desa, $kampung->kecamatan, $kampung->kabupaten])->filter()->implode(', ') ?: 'Lokasi belum diisi' }}
                        </p>
                    </div>
                    <span class="shrink-0 inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $statusColor }}">{{ $statusLabel }}</span>
                </div>

                @if(auth()->user()->isAdmin())
                <p class="text-xs text-gray-400 mb-2">Relawan: {{ $kampung->user?->name ?? '-' }}</p>
                @endif

                <div class="flex items-center gap-4 text-xs text-gray-500 mb-3">
                    <span class="flex items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                        <strong class="text-gray-800">{{ $totalCi }}</strong> check-in
                    </span>
                    @if($kampung->qr_token)
                    <span class="flex items-center gap-1 text-emerald-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 3.5c0 1.5-1.5 3-3 3s-3-1.5-3-3 1.5-3 3-3 3 1.5 3 3z"/></svg>
                        QR Aktif
                    </span>
                    @endif
                </div>

                <div class="flex gap-2">
                    <a href="{{ route('kampung.show', $kampung) }}" class="flex-1 text-center px-3 py-1.5 text-xs font-semibold bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-lg transition">Lihat Detail</a>
                    @if(auth()->user()->isAdmin())
                        @if($kampung->status_validasi === 'pending')
                        <form method="POST" action="{{ route('kampung.validate', $kampung) }}">
                            @csrf @method('PATCH')
                            <button type="submit" class="px-3 py-1.5 text-xs font-semibold bg-emerald-50 hover:bg-emerald-100 text-emerald-700 rounded-lg transition">Verifikasi</button>
                        </form>
                        @elseif($kampung->status_validasi === 'validated')
                        <form method="POST" action="{{ route('kampung.cancel-validate', $kampung) }}">
                            @csrf @method('PATCH')
                            <button type="submit" class="px-3 py-1.5 text-xs font-semibold bg-amber-50 hover:bg-amber-100 text-amber-700 rounded-lg transition">Batalkan</button>
                        </form>
                        @endif
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-6">{{ $kampungList->links() }}</div>
    @endif
</div>

</div>
@endsection
