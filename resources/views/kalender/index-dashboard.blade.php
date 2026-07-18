@extends('layouts.app')

@section('title', 'Kalender Kegiatan - Dataraga')

@section('content')
@php
    $totalCells   = $firstDayOfWeek - 1 + $daysInMonth;
    $remaining    = (7 - ($totalCells % 7)) % 7;
    $rows         = ($totalCells + $remaining) / 7;

    // Kumpulkan semua event bulan ini untuk panel samping
    $allItems = collect($calendarData)->flatten(1)->sortBy('date');
    $today    = now()->format('Y-m-d');

    $hariSingkat = ['Sen','Sel','Rab','Kam','Jum','Sab','Min'];
@endphp

<div class="flex flex-col lg:flex-row gap-0 h-full min-h-[calc(100vh-4rem)]">

    {{-- ===== PANEL KIRI: KALENDER ===== --}}
    <div class="flex-1 flex flex-col min-w-0">

        {{-- Header --}}
        <div class="bg-gradient-to-r from-blue-700 via-blue-600 to-sky-500 px-5 py-4 flex items-center justify-between shadow-sm">
            <div>
                <p class="text-blue-200 text-xs font-medium uppercase tracking-widest">Kalender Kegiatan</p>
                <h2 class="text-white text-2xl font-extrabold leading-tight">
                    {{ $currentDate->translatedFormat('F') }}
                    <span class="text-blue-200 font-normal">{{ $currentDate->format('Y') }}</span>
                </h2>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('kalender.index', ['bulan' => $prevMonth]) }}"
                   class="p-2 rounded-xl bg-white/15 hover:bg-white/25 text-white transition">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                </a>
                <a href="{{ route('kalender.index', ['bulan' => now()->format('Y-m')]) }}"
                   class="px-3 py-1.5 rounded-xl bg-white/15 hover:bg-white/25 text-white text-xs font-semibold transition">
                    Hari Ini
                </a>
                <a href="{{ route('kalender.index', ['bulan' => $nextMonth]) }}"
                   class="p-2 rounded-xl bg-white/15 hover:bg-white/25 text-white transition">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
        </div>

        {{-- Header Hari --}}
        <div class="grid grid-cols-7 bg-blue-50 border-b border-blue-100">
            @foreach($hariSingkat as $idx => $hari)
            <div class="py-2.5 text-center text-[11px] font-bold uppercase tracking-wider
                {{ $idx >= 5 ? 'text-red-400' : 'text-blue-600' }}">
                {{ $hari }}
            </div>
            @endforeach
        </div>

        {{-- Grid Tanggal --}}
        <div class="flex-1 grid grid-cols-7 bg-white" style="grid-template-rows: repeat({{ $rows }}, minmax(80px, 1fr));">

            @for($i = 1; $i < $firstDayOfWeek; $i++)
                <div class="border-b border-r border-gray-100 bg-gray-50/50 {{ $i % 7 === 6 || $i % 7 === 0 ? 'bg-red-50/30' : '' }}"></div>
            @endfor

            @for($day = 1; $day <= $daysInMonth; $day++)
                @php
                    $dateKey     = $currentDate->copy()->setDay($day)->format('Y-m-d');
                    $items       = $calendarData[$dateKey] ?? [];
                    $isToday     = $dateKey === $today;
                    $dayOfWeek   = $currentDate->copy()->setDay($day)->dayOfWeekIso; // 1=Mon..7=Sun
                    $isWeekend   = $dayOfWeek >= 6;
                    $visibleItems = array_slice($items, 0, 2);
                    $hiddenCount = count($items) - 2;
                @endphp
                <div class="border-b border-r border-gray-100 p-1.5 transition group relative
                    {{ $isWeekend ? 'bg-red-50/20' : '' }}
                    {{ $isToday ? 'ring-2 ring-inset ring-blue-400 bg-blue-50/40' : 'hover:bg-gray-50' }}">
                    {{-- Nomor Tanggal --}}
                    <div class="mb-1">
                        @if($isToday)
                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-blue-600 text-white text-xs font-bold shadow">{{ $day }}</span>
                        @else
                            <span class="text-sm font-medium {{ $isWeekend ? 'text-red-400' : 'text-gray-600' }}">{{ $day }}</span>
                        @endif
                    </div>
                    {{-- Event Pills --}}
                    <div class="space-y-0.5">
                        @foreach($visibleItems as $item)
                        <a href="{{ $item['url'] ?? '#' }}"
                           class="flex items-center gap-1 px-1.5 py-0.5 rounded-md text-[10px] font-semibold truncate leading-tight transition hover:opacity-80
                               {{ $item['type'] === 'event' ? 'bg-sky-100 text-sky-700 border border-sky-200' : 'bg-emerald-100 text-emerald-700 border border-emerald-200' }}"
                           title="{{ $item['title'] }}">
                            <span class="shrink-0 w-1.5 h-1.5 rounded-full {{ $item['type'] === 'event' ? 'bg-sky-500' : 'bg-emerald-500' }}"></span>
                            <span class="truncate">{{ $item['title'] }}</span>
                        </a>
                        @endforeach
                        @if($hiddenCount > 0)
                        <span class="block text-[10px] text-gray-400 pl-1">+{{ $hiddenCount }} lagi</span>
                        @endif
                    </div>
                </div>
            @endfor

            @for($i = 0; $i < $remaining; $i++)
                <div class="border-b border-r border-gray-100 bg-gray-50/50"></div>
            @endfor
        </div>
    </div>

    {{-- ===== PANEL KANAN: EVENT LIST ===== --}}
    <div class="lg:w-72 xl:w-80 border-l border-gray-100 bg-gray-50/60 flex flex-col">
        {{-- Header Panel --}}
        <div class="px-4 py-4 border-b border-gray-100 bg-white">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Agenda Bulan Ini</p>
            <div class="flex items-center gap-2 flex-wrap">
                <span class="flex items-center gap-1 text-xs text-sky-600 font-medium">
                    <span class="w-2 h-2 rounded-full bg-sky-400"></span> Event
                </span>
                <span class="flex items-center gap-1 text-xs text-emerald-600 font-medium">
                    <span class="w-2 h-2 rounded-full bg-emerald-400"></span> Latihan/Partisipasi
                </span>
            </div>
        </div>

        {{-- List --}}
        <div class="flex-1 overflow-y-auto px-3 py-3 space-y-2">
            @forelse($allItems as $item)
            @php
                $isPast = isset($item['date']) && $item['date'] < $today;
                $isItemToday = isset($item['date']) && $item['date'] === $today;
            @endphp
            <a href="{{ $item['url'] ?? '#' }}"
               class="flex items-start gap-3 p-3 rounded-xl border transition group
                   {{ $isItemToday ? 'bg-blue-50 border-blue-200 shadow-sm' : ($isPast ? 'bg-white border-gray-100 opacity-60' : 'bg-white border-gray-100 hover:border-blue-200 hover:shadow-sm') }}">
                {{-- Tanggal Badge --}}
                <div class="shrink-0 w-10 text-center">
                    <div class="text-lg font-extrabold leading-none {{ $isItemToday ? 'text-blue-600' : 'text-gray-700' }}">
                        {{ isset($item['date']) ? \Carbon\Carbon::parse($item['date'])->format('d') : '' }}
                    </div>
                    <div class="text-[10px] font-medium text-gray-400 uppercase">
                        {{ isset($item['date']) ? \Carbon\Carbon::parse($item['date'])->isoFormat('MMM') : '' }}
                    </div>
                </div>
                {{-- Garis pemisah vertikal --}}
                <div class="w-0.5 self-stretch rounded-full {{ $item['type'] === 'event' ? 'bg-sky-400' : 'bg-emerald-400' }}"></div>
                {{-- Info --}}
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-semibold text-gray-800 truncate group-hover:text-blue-700 transition">{{ $item['title'] }}</p>
                    @if(isset($item['time']))
                    <p class="text-[10px] text-gray-400 mt-0.5">{{ $item['time'] }}</p>
                    @endif
                    <span class="inline-block mt-1 px-1.5 py-0.5 rounded text-[10px] font-medium
                        {{ $item['type'] === 'event' ? 'bg-sky-100 text-sky-600' : 'bg-emerald-100 text-emerald-600' }}">
                        {{ $item['type'] === 'event' ? 'Event' : 'Latihan' }}
                    </span>
                </div>
            </a>
            @empty
            <div class="text-center py-12">
                <div class="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-3">
                    <svg class="h-6 w-6 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <p class="text-sm text-gray-400 font-medium">Tidak ada kegiatan</p>
                <p class="text-xs text-gray-300 mt-1">bulan ini</p>
            </div>
            @endforelse
        </div>
    </div>

</div>
@endsection
