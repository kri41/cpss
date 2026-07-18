@extends('layouts.public')

@section('title', 'Kalender Kegiatan - Dataraga')

@section('content')
@php
    $totalCells  = $firstDayOfWeek - 1 + $daysInMonth;
    $remaining   = (7 - ($totalCells % 7)) % 7;
    $rows        = ($totalCells + $remaining) / 7;
    $allItems    = collect($calendarData)->flatten(1)->sortBy('date');
    $today       = now()->format('Y-m-d');
    $hariSingkat = ['Sen','Sel','Rab','Kam','Jum','Sab','Min'];
@endphp

<div class="flex flex-col lg:flex-row min-h-[calc(100vh-3.5rem)]">

    {{-- ===== PANEL KIRI: KALENDER ===== --}}
    <div class="flex-1 flex flex-col min-w-0">

        {{-- Header --}}
        <div class="bg-gradient-to-r from-blue-700 via-blue-600 to-sky-500 px-5 sm:px-8 py-5 flex items-center justify-between">
            <div>
                <p class="text-blue-200 text-xs font-semibold uppercase tracking-widest">Kalender Kegiatan</p>
                <h2 class="text-white text-3xl font-extrabold leading-tight mt-0.5">
                    {{ $currentDate->translatedFormat('F') }}
                    <span class="text-blue-300 font-light">{{ $currentDate->format('Y') }}</span>
                </h2>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('kalender.index', ['bulan' => $prevMonth]) }}"
                   class="p-2.5 rounded-xl bg-white/15 hover:bg-white/30 text-white transition">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                </a>
                <a href="{{ route('kalender.index', ['bulan' => now()->format('Y-m')]) }}"
                   class="px-3 py-2 rounded-xl bg-white/15 hover:bg-white/30 text-white text-xs font-bold transition hidden sm:block">
                    Bulan Ini
                </a>
                <a href="{{ route('kalender.index', ['bulan' => $nextMonth]) }}"
                   class="p-2.5 rounded-xl bg-white/15 hover:bg-white/30 text-white transition">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
        </div>

        {{-- Header Hari --}}
        <div class="grid grid-cols-7 bg-blue-50/80 border-b border-blue-100">
            @foreach($hariSingkat as $idx => $hari)
            <div class="py-3 text-center text-[11px] font-bold uppercase tracking-widest
                {{ $idx >= 5 ? 'text-red-400' : 'text-blue-600' }}">
                {{ $hari }}
            </div>
            @endforeach
        </div>

        {{-- Grid Tanggal --}}
        <div class="flex-1 grid grid-cols-7 bg-white" style="grid-template-rows: repeat({{ $rows }}, minmax(90px, 1fr));">

            @for($i = 1; $i < $firstDayOfWeek; $i++)
                <div class="border-b border-r border-gray-100 bg-gray-50/60"></div>
            @endfor

            @for($day = 1; $day <= $daysInMonth; $day++)
                @php
                    $dateKey     = $currentDate->copy()->setDay($day)->format('Y-m-d');
                    $items       = $calendarData[$dateKey] ?? [];
                    $isToday     = $dateKey === $today;
                    $dayOfWeek   = $currentDate->copy()->setDay($day)->dayOfWeekIso;
                    $isWeekend   = $dayOfWeek >= 6;
                    $visibleItems = array_slice($items, 0, 3);
                    $hiddenCount = count($items) - 3;
                @endphp
                <div class="border-b border-r border-gray-100 p-2 transition
                    {{ $isWeekend ? 'bg-red-50/20' : '' }}
                    {{ $isToday ? 'ring-2 ring-inset ring-blue-500 bg-blue-50/50' : 'hover:bg-sky-50/40' }}">
                    <div class="mb-1.5">
                        @if($isToday)
                            <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-blue-600 text-white text-sm font-bold shadow-md">{{ $day }}</span>
                        @else
                            <span class="text-sm font-semibold {{ $isWeekend ? 'text-red-400' : 'text-gray-500' }}">{{ $day }}</span>
                        @endif
                    </div>
                    <div class="space-y-0.5">
                        @foreach($visibleItems as $item)
                        <a href="{{ $item['url'] ?? '#' }}"
                           class="flex items-center gap-1 px-1.5 py-0.5 rounded-md text-[10px] font-semibold truncate leading-tight transition hover:opacity-75
                               {{ $item['type'] === 'event' ? 'bg-sky-100 text-sky-700 border border-sky-200' : 'bg-emerald-100 text-emerald-700 border border-emerald-200' }}"
                           title="{{ $item['title'] }}">
                            <span class="shrink-0 w-1.5 h-1.5 rounded-full {{ $item['type'] === 'event' ? 'bg-sky-500' : 'bg-emerald-500' }}"></span>
                            <span class="truncate">{{ $item['title'] }}</span>
                        </a>
                        @endforeach
                        @if($hiddenCount > 0)
                        <span class="block text-[10px] text-gray-400 pl-1 font-medium">+{{ $hiddenCount }} lagi</span>
                        @endif
                    </div>
                </div>
            @endfor

            @for($i = 0; $i < $remaining; $i++)
                <div class="border-b border-r border-gray-100 bg-gray-50/60"></div>
            @endfor
        </div>
    </div>

    {{-- ===== PANEL KANAN: AGENDA ===== --}}
    <div class="lg:w-72 xl:w-80 border-l border-gray-200/80 bg-white flex flex-col">
        {{-- Header --}}
        <div class="px-5 py-4 border-b border-gray-100">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Agenda</p>
            <p class="text-sm font-semibold text-gray-700 mt-0.5">{{ $currentDate->translatedFormat('F Y') }}</p>
            <div class="flex items-center gap-3 mt-2">
                <span class="flex items-center gap-1.5 text-[11px] text-sky-600 font-medium">
                    <span class="w-2 h-2 rounded-full bg-sky-400"></span> Event
                </span>
                <span class="flex items-center gap-1.5 text-[11px] text-emerald-600 font-medium">
                    <span class="w-2 h-2 rounded-full bg-emerald-400"></span> Latihan
                </span>
            </div>
        </div>

        {{-- List Agenda --}}
        <div class="flex-1 overflow-y-auto divide-y divide-gray-50">
            @forelse($allItems as $item)
            @php
                $isPast     = isset($item['date']) && $item['date'] < $today;
                $isNow      = isset($item['date']) && $item['date'] === $today;
                $carbonDate = isset($item['date']) ? \Carbon\Carbon::parse($item['date']) : null;
            @endphp
            <a href="{{ $item['url'] ?? '#' }}"
               class="flex items-start gap-3 px-4 py-3 transition group
                   {{ $isNow ? 'bg-blue-50' : ($isPast ? 'opacity-50' : 'hover:bg-gray-50') }}">
                {{-- Tanggal --}}
                <div class="shrink-0 w-10 text-center pt-0.5">
                    <div class="text-xl font-black leading-none {{ $isNow ? 'text-blue-600' : 'text-gray-700' }}">
                        {{ $carbonDate?->format('d') }}
                    </div>
                    <div class="text-[10px] text-gray-400 uppercase font-semibold">
                        {{ $carbonDate?->isoFormat('ddd') }}
                    </div>
                </div>
                {{-- Strip warna --}}
                <div class="w-1 self-stretch rounded-full shrink-0
                    {{ $item['type'] === 'event' ? 'bg-sky-400' : 'bg-emerald-400' }}"></div>
                {{-- Konten --}}
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-bold text-gray-800 group-hover:text-blue-700 transition line-clamp-2">{{ $item['title'] }}</p>
                    @if(isset($item['time']))
                        <p class="text-[10px] text-gray-400 mt-0.5 flex items-center gap-1">
                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            {{ $item['time'] }}
                        </p>
                    @endif
                    @if($isNow)
                        <span class="inline-block mt-1 px-1.5 py-0.5 bg-blue-600 text-white text-[9px] font-bold rounded-full uppercase tracking-wide">Hari Ini</span>
                    @endif
                </div>
            </a>
            @empty
            <div class="flex flex-col items-center justify-center py-16 px-4 text-center">
                <div class="w-14 h-14 rounded-2xl bg-gray-100 flex items-center justify-center mb-3">
                    <svg class="h-7 w-7 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <p class="text-sm font-semibold text-gray-500">Tidak ada agenda</p>
                <p class="text-xs text-gray-400 mt-1">Bulan {{ $currentDate->translatedFormat('F Y') }} kosong</p>
            </div>
            @endforelse
        </div>
    </div>

</div>
@endsection

@push('mascot')
<div class="hidden lg:block fixed bottom-0 left-4 z-10 pointer-events-none select-none">
    <img src="/storage/karakter/2.png" alt="" class="h-48 xl:h-56 w-auto object-contain object-bottom drop-shadow-xl">
</div>
@endpush
