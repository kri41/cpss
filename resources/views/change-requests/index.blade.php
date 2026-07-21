@extends('layouts.app')

@section('title', 'Usulan Perubahan - Dataraga')

@section('content')
<div x-data="{ rejectOpen: false, rejectAction: '' }" class="max-w-4xl mx-auto px-4 py-4 sm:px-6">

    <div class="mb-4 flex items-center gap-3">
        <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800 font-medium">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali
        </a>
        <h1 class="text-base font-bold text-gray-800">Usulan Perubahan</h1>
        <span class="text-xs font-bold text-blue-700 bg-blue-50 px-2.5 py-1 rounded-full">{{ $changeRequests->total() }} menunggu</span>
    </div>

    <p class="text-xs text-gray-400 mb-4">Menyetujui permintaan akan membuka kembali akses edit untuk <strong>pemilik asli</strong> data (status kembali "pending", perlu divalidasi ulang setelah pemilik selesai memperbaiki) — bukan mengubah data secara langsung.</p>

    @if(session('success'))
    <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-xl text-sm text-green-700">{{ session('success') }}</div>
    @endif

    @if($changeRequests->isEmpty())
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-10 text-center text-gray-400 text-sm">
        Tidak ada permintaan yang menunggu tinjauan.
    </div>
    @else
    <div class="space-y-4">
        @foreach($changeRequests as $cr)
        @php
            $model = $cr->changeable;
            $itemName = $model?->nama_fasilitas ?? $model?->nama_club ?? $model?->nama_event ?? '(data tidak ditemukan)';
            $ownerName = $model?->user?->name ?? '-';
            $isOwnerRequest = $model && $cr->user_id === $model->user_id;
            $showRoute = match($cr->changeable_type) {
                'prasarana' => $model ? route('prasarana.show', $model) : null,
                'club' => $model ? route('clubs.show', $model) : null,
                'event' => $model ? route('events.show', $model) : null,
                default => null,
            };
        @endphp
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-50">
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="text-[10px] font-bold uppercase tracking-wide text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full">{{ $cr->typeLabel() }}</span>
                    @if($showRoute)
                    <a href="{{ $showRoute }}" target="_blank" class="text-sm font-bold text-gray-900 hover:text-blue-600 truncate">{{ $itemName }}</a>
                    @else
                    <span class="text-sm font-bold text-gray-400">{{ $itemName }}</span>
                    @endif
                </div>
                <p class="text-xs text-gray-400 mt-1">
                    Pemilik data: <strong class="text-gray-600">{{ $ownerName }}</strong>
                    &middot;
                    @if($isOwnerRequest)
                        Diajukan oleh pemilik sendiri
                    @else
                        Dilaporkan oleh <strong class="text-gray-600">{{ $cr->user?->name ?? '-' }}</strong>
                    @endif
                    &middot; {{ $cr->created_at->diffForHumans() }}
                </p>
            </div>

            <div class="px-5 py-3 bg-amber-50/60">
                <p class="text-xs font-semibold text-amber-800 mb-0.5">Alasan:</p>
                <p class="text-sm text-amber-900">{{ $cr->alasan }}</p>
            </div>

            <div class="px-5 py-4 flex gap-2">
                <form method="POST" action="{{ route('change-requests.approve', $cr) }}" onsubmit="return confirm('Setujui permintaan ini? Akses edit pemilik data akan dibuka kembali (status kembali pending).')">
                    @csrf @method('PATCH')
                    <button type="submit" class="px-4 py-2 text-sm font-semibold bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl transition">Setujui</button>
                </form>
                <button @click="rejectOpen = true; rejectAction = '{{ route('change-requests.reject', $cr) }}'" class="px-4 py-2 text-sm font-semibold bg-red-50 hover:bg-red-100 text-red-700 rounded-xl transition">Tolak</button>
            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-6">{{ $changeRequests->links() }}</div>
    @endif
</div>

{{-- Reject Modal --}}
<div x-show="rejectOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50">
    <div @click.away="rejectOpen = false" class="bg-white rounded-2xl shadow-2xl max-w-sm w-full p-6">
        <h3 class="text-base font-bold text-gray-900 mb-1">Tolak Permintaan</h3>
        <p class="text-sm text-gray-500 mb-4">Berikan alasan penolakan agar pengaju mengerti.</p>
        <form method="POST" :action="rejectAction">
            @csrf @method('PATCH')
            <textarea name="catatan_admin" rows="3" placeholder="Tuliskan alasan penolakan..." required class="w-full rounded-xl border-gray-300 focus:border-red-500 focus:ring-red-500 text-sm mb-4 resize-none"></textarea>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-xl transition">Tolak</button>
                <button type="button" @click="rejectOpen = false" class="flex-1 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-xl transition">Batal</button>
            </div>
        </form>
    </div>
</div>
@endsection
