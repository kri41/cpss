@extends('layouts.public')

@section('title', 'Kalender Kegiatan - CPSS')

@section('content')
<!-- Header Kalender -->
<div class="bg-gray-50 border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="text-xl font-bold text-gray-900">Kalender Kegiatan</h2>
                <p class="text-sm text-gray-500 mt-0.5">Jadwal event dan latihan komunitas</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('kalender.index', ['bulan' => $prevMonth]) }}" class="p-2 rounded-lg bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 transition shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                </a>
                <span class="px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm font-semibold text-gray-700 min-w-[160px] text-center shadow-sm">
                    {{ $currentDate->translatedFormat('F Y') }}
                </span>
                <a href="{{ route('kalender.index', ['bulan' => $nextMonth]) }}" class="p-2 rounded-lg bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 transition shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                </a>
            </div>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <!-- Legend -->
    <div class="flex flex-wrap items-center gap-4 mb-6">
        <div class="flex items-center gap-2">
            <span class="w-3 h-3 rounded-full bg-sky-400"></span>
            <span class="text-sm text-gray-600">Event</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="w-3 h-3 rounded-full bg-emerald-400"></span>
            <span class="text-sm text-gray-600">Jadwal Latihan Club</span>
        </div>
    </div>

    <!-- Calendar Grid -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <!-- Header Hari -->
        <div class="grid grid-cols-7 bg-gray-50 border-b border-gray-200">
            @foreach(['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'] as $hari)
                <div class="py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ $hari }}</div>
            @endforeach
        </div>

        <!-- Grid Tanggal -->
        <div class="grid grid-cols-7">
            {{-- Empty cells sebelum tanggal 1 --}}
            @for($i = 1; $i < $firstDayOfWeek; $i++)
                <div class="min-h-[120px] border-b border-r border-gray-100 bg-gray-50/30"></div>
            @endfor

            {{-- Tanggal --}}
            @for($day = 1; $day <= $daysInMonth; $day++)
                @php
                    $dateKey = $currentDate->copy()->setDay($day)->format('Y-m-d');
                    $items = $calendarData[$dateKey] ?? [];
                    $isToday = $dateKey === now()->format('Y-m-d');
                @endphp
                <div class="min-h-[120px] border-b border-r border-gray-100 p-2 hover:bg-gray-50/50 transition {{ $isToday ? 'bg-blue-50/40' : '' }}">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-sm font-medium {{ $isToday ? 'text-blue-600 bg-blue-100 w-7 h-7 flex items-center justify-center rounded-full' : 'text-gray-700' }}">{{ $day }}</span>
                    </div>
                    <div class="space-y-1">
                        @foreach($items as $item)
                            <a href="{{ $item['url'] ?? '#' }}" class="block px-2 py-1 rounded-md text-[11px] font-medium border truncate {{ $item['color'] }} hover:opacity-80 transition" title="{{ $item['title'] }}{{ isset($item['time']) ? ' (' . $item['time'] . ')' : '' }}">
                                {{ $item['title'] }}
                                @if(isset($item['time']))
                                    <span class="opacity-75">{{ $item['time'] }}</span>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>
            @endfor

            {{-- Empty cells setelah tanggal terakhir --}}
            @php
                $totalCells = $firstDayOfWeek - 1 + $daysInMonth;
                $remaining = (7 - ($totalCells % 7)) % 7;
            @endphp
            @for($i = 0; $i < $remaining; $i++)
                <div class="min-h-[120px] border-b border-r border-gray-100 bg-gray-50/30"></div>
            @endfor
        </div>
    </div>
</div>
@endsection
