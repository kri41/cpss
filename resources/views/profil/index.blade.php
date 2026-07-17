@extends('layouts.app')

@section('title', 'Profil Saya - Dataraga')

@section('content')
<div class="py-6">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        {{-- ===== HEADER PROFIL ===== --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="h-20 bg-gradient-to-r from-blue-600 to-sky-500"></div>
            <div class="px-6 pb-6">
                <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 -mt-8">
                    <div class="flex items-end gap-4">
                        <div class="w-20 h-20 rounded-2xl bg-white border-4 border-white shadow-md bg-gradient-to-br from-blue-500 to-sky-400 flex items-center justify-center text-white font-bold text-3xl shrink-0">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <div class="pb-1">
                            <h1 class="text-xl font-bold text-gray-900">{{ $user->name }}</h1>
                            <div class="flex flex-wrap items-center gap-2 mt-1">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold
                                    {{ $user->role === 'super_admin' ? 'bg-purple-100 text-purple-700' : ($user->role === 'admin' ? 'bg-blue-100 text-blue-700' : 'bg-emerald-100 text-emerald-700') }}">
                                    {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                                </span>
                                @if($user->kabupaten)
                                    <span class="text-xs text-gray-500 flex items-center gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        {{ $user->desa ? $user->desa . ', ' : '' }}{{ $user->kecamatan ? $user->kecamatan . ', ' : '' }}{{ $user->kabupaten }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="pb-1 flex gap-2">
                        <a href="{{ route('profile.edit') }}" class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl transition">Edit Profil</a>
                        <a href="{{ route('leaderboard.my-points') }}" class="px-4 py-2 text-sm font-medium text-blue-600 bg-blue-50 hover:bg-blue-100 rounded-xl transition">Riwayat Poin</a>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== POIN & PERINGKAT ===== --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-gradient-to-br from-blue-600 to-sky-500 rounded-2xl shadow-sm p-5 text-white">
                <p class="text-sm font-medium text-blue-100">Total Poin</p>
                <p class="text-4xl font-extrabold mt-1">{{ number_format($user->total_poin ?? 0) }}</p>
                <p class="text-xs text-blue-200 mt-1">poin terkumpul</p>
            </div>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <p class="text-sm font-medium text-gray-500">Peringkat Saya</p>
                <p class="text-4xl font-extrabold text-gray-900 mt-1">#{{ $rank }}</p>
                <p class="text-xs text-gray-400 mt-1">dari {{ $totalActiveUsers }} relawan aktif</p>
            </div>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <p class="text-sm font-medium text-gray-500">Lencana Diraih</p>
                <p class="text-4xl font-extrabold text-gray-900 mt-1">{{ $badges->where('earned', true)->count() }}<span class="text-xl text-gray-300 font-normal">/{{ $badges->count() }}</span></p>
                <p class="text-xs text-gray-400 mt-1">pencapaian terbuka</p>
            </div>
        </div>

        {{-- ===== STATISTIK KONTRIBUSI ===== --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-base font-bold text-gray-900 mb-4">Statistik Kontribusi</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @php
                $kontribusiItems = [
                    ['label' => 'Prasarana',   'total' => $stats['prasarana'],   'validated' => $stats['prasarana_validated'],   'color' => 'blue',    'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'],
                    ['label' => 'Klub',        'total' => $stats['clubs'],       'validated' => $stats['clubs_validated'],       'color' => 'indigo',  'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
                    ['label' => 'Event',       'total' => $stats['events'],      'validated' => $stats['events_validated'],      'color' => 'sky',     'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                    ['label' => 'Partisipasi', 'total' => $stats['partisipasi'], 'validated' => $stats['partisipasi_validated'], 'color' => 'emerald', 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z'],
                ];
                @endphp
                @foreach($kontribusiItems as $item)
                <div class="bg-{{ $item['color'] }}-50 rounded-xl p-4 border border-{{ $item['color'] }}-100">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="p-1.5 bg-{{ $item['color'] }}-100 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-{{ $item['color'] }}-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"/></svg>
                        </div>
                        <span class="text-xs font-semibold text-{{ $item['color'] }}-700">{{ $item['label'] }}</span>
                    </div>
                    <p class="text-2xl font-extrabold text-gray-900">{{ $item['total'] }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">{{ $item['validated'] }} tervalidasi</p>
                    @if($item['total'] > 0)
                        <div class="mt-2 h-1 bg-{{ $item['color'] }}-100 rounded-full overflow-hidden">
                            <div class="h-full bg-{{ $item['color'] }}-500 rounded-full" style="width: {{ min(100, round(($item['validated'] / $item['total']) * 100)) }}%"></div>
                        </div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>

        {{-- ===== LENCANA ===== --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-base font-bold text-gray-900 mb-4">Lencana Pencapaian</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                @foreach($badges as $badge)
                <div class="flex items-start gap-4 p-4 rounded-xl border transition
                    {{ $badge->earned ? 'border-amber-200 bg-amber-50' : 'border-gray-100 bg-gray-50 opacity-60' }}">
                    <div class="shrink-0 w-12 h-12 rounded-xl flex items-center justify-center text-2xl shadow-sm
                        {{ $badge->earned ? 'bg-amber-100' : 'bg-gray-100 grayscale' }}">
                        {{ $badge->icon ?? '🏅' }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <p class="text-sm font-bold {{ $badge->earned ? 'text-amber-800' : 'text-gray-400' }}">{{ $badge->nama }}</p>
                            @if($badge->earned)
                                <svg class="h-4 w-4 text-amber-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            @endif
                        </div>
                        <p class="text-xs {{ $badge->earned ? 'text-amber-700' : 'text-gray-400' }} mt-0.5">{{ $badge->deskripsi }}</p>
                        @if($badge->earned && $badge->earned_at)
                            <p class="text-[10px] text-amber-500 mt-1">Diraih {{ \Carbon\Carbon::parse($badge->earned_at)->isoFormat('D MMM YYYY') }}</p>
                        @elseif(!$badge->earned)
                            <p class="text-[10px] text-gray-400 mt-1">Belum diraih</p>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- ===== AKTIVITAS TERAKHIR ===== --}}
        @if($recentTransactions->count() > 0)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-base font-bold text-gray-900">Aktivitas Terakhir</h2>
                <a href="{{ route('leaderboard.my-points') }}" class="text-xs text-blue-600 hover:text-blue-800 font-medium">Lihat semua →</a>
            </div>
            <div class="space-y-2">
                @foreach($recentTransactions as $tx)
                <div class="flex items-center gap-3 py-2.5 border-b border-gray-50 last:border-0">
                    <div class="shrink-0 w-9 h-9 rounded-xl flex items-center justify-center text-sm font-bold
                        {{ $tx->status === 'valid' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-500' }}">
                        {{ $tx->status === 'valid' ? '+' : '-' }}{{ $tx->poin }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-800 capitalize">
                            {{ str_replace(['_baru', '_update', '_valid'], [' baru', ' diperbarui', ' tervalidasi'], $tx->jenis_aksi ?? $tx->related_type) }}
                            <span class="font-normal text-gray-400 capitalize">({{ $tx->related_type }})</span>
                        </p>
                        <p class="text-xs text-gray-400">{{ $tx->created_at->isoFormat('D MMM YYYY, HH:mm') }}</p>
                    </div>
                    @if($tx->status === 'dibatalkan')
                        <span class="shrink-0 text-xs text-red-500 font-medium">Dibatalkan</span>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif

    </div>
</div>
@endsection
