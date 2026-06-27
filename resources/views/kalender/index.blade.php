@extends('layouts.public')

@section('title', 'Kalender Kegiatan - CPSS')

@section('content')
<div class="h-[calc(100vh-3.5rem)] flex flex-col overflow-hidden px-4 sm:px-6 lg:px-8 pb-4">
    <!-- Header Kalender -->
    <div class="shrink-0 bg-gray-50 border-b border-gray-200 py-3">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <div class="flex items-center gap-4">
                <h2 class="text-lg font-bold text-gray-900">Kalender Kegiatan</h2>
                <div class="flex items-center gap-3 text-xs text-gray-500">
                    <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-sky-400"></span> Event</span>
                    <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-emerald-400"></span> Latihan</span>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('kalender.index', ['bulan' => $prevMonth]) }}" class="p-1.5 rounded-lg bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 transition shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                </a>
                <span class="px-3 py-1.5 bg-white border border-gray-200 rounded-lg text-sm font-semibold text-gray-700 min-w-[140px] text-center shadow-sm">
                    {{ $currentDate->translatedFormat('F Y') }}
                </span>
                <a href="{{ route('kalender.index', ['bulan' => $nextMonth]) }}" class="p-1.5 rounded-lg bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 transition shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                </a>
            </div>
        </div>
    </div>

    <!-- Calendar Grid -->
    <div class="flex-1 bg-white border-b border-gray-200 overflow-hidden">
        <!-- Header Hari -->
        <div class="grid grid-cols-7 bg-gray-50 border-b border-gray-200">
            @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'] as $hari)
                <div class="py-2 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide border-r border-gray-100 last:border-r-0">{{ $hari }}</div>
            @endforeach
        </div>

        <!-- Grid Tanggal -->
        <div class="grid grid-cols-7 h-[calc(100%-2.25rem)]">
            @php
                $totalCells = $firstDayOfWeek - 1 + $daysInMonth;
                $remaining = (7 - ($totalCells % 7)) % 7;
                $rows = ($totalCells + $remaining) / 7;
                $cellHeight = $rows > 0 ? 'calc(100% / ' . $rows . ')' : 'auto';
            @endphp

            {{-- Empty cells sebelum tanggal 1 --}}
            @for($i = 1; $i < $firstDayOfWeek; $i++)
                <div class="border-b border-r border-gray-100 bg-gray-50/30" style="height: {{ $cellHeight }}"></div>
            @endfor

            {{-- Tanggal --}}
            @for($day = 1; $day <= $daysInMonth; $day++)
                @php
                    $dateKey = $currentDate->copy()->setDay($day)->format('Y-m-d');
                    $items = $calendarData[$dateKey] ?? [];
                    $isToday = $dateKey === now()->format('Y-m-d');
                @endphp
                <div class="border-b border-r border-gray-100 p-1.5 hover:bg-gray-50/50 transition overflow-hidden {{ $isToday ? 'bg-blue-50/40' : '' }}" style="height: {{ $cellHeight }}">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-xs font-medium {{ $isToday ? 'text-blue-600 bg-blue-100 w-6 h-6 flex items-center justify-center rounded-full' : 'text-gray-700' }}">{{ $day }}</span>
                    </div>
                    <div class="space-y-0.5">
                        @foreach($items as $item)
                            <a href="{{ $item['url'] ?? '#' }}" class="block px-1.5 py-0.5 rounded text-[10px] font-medium border truncate {{ $item['color'] }} hover:opacity-80 transition leading-tight" title="{{ $item['title'] }}{{ isset($item['time']) ? ' (' . $item['time'] . ')' : '' }}">
                                {{ $item['title'] }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endfor

            {{-- Empty cells setelah tanggal terakhir --}}
            @for($i = 0; $i < $remaining; $i++)
                <div class="border-b border-r border-gray-100 bg-gray-50/30" style="height: {{ $cellHeight }}"></div>
            @endfor
        </div>
    </div>
</div>
@endsection
