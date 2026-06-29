@extends('layouts.app')

@section('title', 'Event Olahraga - CPSS')

@section('content')
@php
    $now = now();
    $totalEvents = $events->count();
    $upcomingEvents = $events->filter(fn($e) => $e->tanggal_mulai > $now)->count();
    $ongoingEvents = $events->filter(fn($e) => $e->tanggal_mulai <= $now && (optional($e->tanggal_selesai)->gte($now) ?? true))->count();
    $validatedEvents = $events->where('status_validasi', 'validated')->count();
@endphp

<!-- Sticky Stats & Filter Bar -->
<div class="sticky top-0 z-30 bg-gray-50/95 backdrop-blur border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <!-- Stats Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-3 flex items-center gap-3">
                <div class="p-2 bg-blue-100 rounded-lg text-blue-600"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg></div>
                <div>
                    <p class="text-xs text-gray-500">Total Event</p>
                    <p class="text-lg font-bold text-gray-900">{{ $totalEvents }}</p>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-3 flex items-center gap-3">
                <div class="p-2 bg-amber-100 rounded-lg text-amber-600"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg></div>
                <div>
                    <p class="text-xs text-gray-500">Akan Datang</p>
                    <p class="text-lg font-bold text-gray-900">{{ $upcomingEvents }}</p>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-3 flex items-center gap-3">
                <div class="p-2 bg-green-100 rounded-lg text-green-600"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg></div>
                <div>
                    <p class="text-xs text-gray-500">Berlangsung</p>
                    <p class="text-lg font-bold text-gray-900">{{ $ongoingEvents }}</p>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-3 flex items-center gap-3">
                <div class="p-2 bg-emerald-100 rounded-lg text-emerald-600"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg></div>
                <div>
                    <p class="text-xs text-gray-500">Tervalidasi</p>
                    <p class="text-lg font-bold text-gray-900">{{ $validatedEvents }}</p>
                </div>
            </div>
        </div>

        <!-- Filter Row -->
        <form method="GET" action="{{ route('events.index') }}" class="flex flex-wrap items-end gap-3">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-medium text-gray-500 mb-1">Cari Event</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama event..." class="w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500 shadow-sm">
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
            <div class="w-40">
                <label class="block text-xs font-medium text-gray-500 mb-1">Tingkat</label>
                <select name="tingkat" class="w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500 shadow-sm" onchange="this.form.submit()">
                    <option value="">Semua</option>
                    @foreach($tingkatList as $t)
                        <option value="{{ $t }}" {{ request('tingkat') == $t ? 'selected' : '' }}>{{ $t }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition shadow-sm">Filter</button>
                <a href="{{ route('events.index') }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-50 transition shadow-sm">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- List Events -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="grid grid-cols-1 gap-4">
        @forelse($events as $event)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                    <div class="flex items-start gap-4 flex-1 min-w-0">
                        <div class="shrink-0 flex flex-col items-center justify-center w-14 h-14 rounded-xl bg-blue-50 border border-blue-100 text-blue-700">
                            <span class="text-lg font-bold leading-none">{{ $event->tanggal_mulai->format('d') }}</span>
                            <span class="text-[10px] font-medium uppercase tracking-wide">{{ $event->tanggal_mulai->format('M') }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <h3 class="text-base font-semibold text-gray-900 truncate">{{ $event->nama_event }}</h3>
                                @if($event->status_validasi === 'validated')
                                    <svg class="h-5 w-5 text-emerald-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                @endif
                            </div>
                            <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-2 text-sm text-gray-500">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-xs font-medium {{ $event->tingkat === 'Desa/Kelurahan' ? 'bg-green-50 text-green-700' : ($event->tingkat === 'Kecamatan' ? 'bg-blue-50 text-blue-700' : 'bg-sky-50 text-sky-700') }}">{{ $event->tingkat }}</span>
                                <span class="inline-flex items-center gap-1"><svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>{{ $event->desa }}, {{ $event->kecamatan }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 self-end sm:self-start shrink-0">
                        <a href="{{ route('events.show', $event) }}" class="p-2 rounded-lg text-blue-600 hover:bg-blue-50 transition-colors" title="Detail"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg></a>
                        @auth
                            @if(auth()->user()->canEdit($event))
                                <a href="{{ route('events.edit', $event) }}" class="p-2 rounded-lg text-amber-600 hover:bg-amber-50 transition-colors" title="Edit"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg></a>
                            @endif
                        @endauth
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
                <div class="flex flex-col items-center">
                    <div class="p-4 bg-gray-100 rounded-full mb-4"><svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg></div>
                    <p class="text-gray-500 text-sm">Tidak ada data event.</p>
                </div>
            </div>
        @endforelse
    </div>

    @if($events->hasPages())
        <div class="mt-6">{{ $events->links() }}</div>
    @endif
</div>
@endsection
