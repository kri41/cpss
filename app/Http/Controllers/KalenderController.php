<?php

namespace App\Http\Controllers;

use App\Models\Club;
use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KalenderController extends Controller
{
    /**
     * Display calendar with events and club schedules.
     */
    public function index(Request $request): View
    {
        $bulan = $request->input('bulan', now()->format('Y-m'));
        $currentDate = Carbon::createFromFormat('Y-m', $bulan)->startOfMonth();
        $prevMonth = $currentDate->copy()->subMonth()->format('Y-m');
        $nextMonth = $currentDate->copy()->addMonth()->format('Y-m');

        $startOfMonth = $currentDate->copy()->startOfMonth();
        $endOfMonth = $currentDate->copy()->endOfMonth();

        // Ambil event di bulan ini (hanya validated untuk publik)
        $eventQuery = Event::whereBetween('tanggal_mulai', [$startOfMonth, $endOfMonth])
            ->orWhereBetween('tanggal_selesai', [$startOfMonth, $endOfMonth])
            ->orWhere(function ($q) use ($startOfMonth, $endOfMonth) {
                $q->where('tanggal_mulai', '<=', $startOfMonth)
                  ->where('tanggal_selesai', '>=', $endOfMonth);
            });

        if (!auth()->check()) {
            $eventQuery->validated();
        }

        $events = $eventQuery->get();

        // Ambil jadwal latihan clubs (berulang per hari)
        $clubQuery = Club::with('jadwalLatihan')->where('aktif', true);
        if (!auth()->check()) {
            $clubQuery->validated();
        }
        $clubs = $clubQuery->get();

        // Mapping hari Indonesia ke Carbon
        $hariMap = [
            'Senin' => 1, 'Selasa' => 2, 'Rabu' => 3,
            'Kamis' => 4, 'Jumat' => 5, 'Sabtu' => 6, 'Minggu' => 0,
        ];

        // Buat events per tanggal
        $eventsByDate = [];
        foreach ($events as $event) {
            $start = Carbon::parse($event->tanggal_mulai);
            $end = $event->tanggal_selesai ? Carbon::parse($event->tanggal_selesai) : $start->copy();

            for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
                $key = $date->format('Y-m-d');
                if (!isset($eventsByDate[$key])) $eventsByDate[$key] = [];
                $eventsByDate[$key][] = [
                    'type' => 'event',
                    'title' => $event->nama_event,
                    'color' => 'bg-sky-100 text-sky-700 border-sky-200',
                    'url' => route('events.show', $event),
                ];
            }
        }

        // Buat jadwal latihan per tanggal di bulan ini
        $schedulesByDate = [];
        foreach ($clubs as $club) {
            foreach ($club->jadwalLatihan as $jadwal) {
                $targetDay = $hariMap[$jadwal->hari] ?? null;
                if ($targetDay === null) continue;

                // Cari semua tanggal di bulan ini yang sesuai hari
                for ($date = $startOfMonth->copy(); $date->lte($endOfMonth); $date->addDay()) {
                    if ($date->dayOfWeek === $targetDay) {
                        $key = $date->format('Y-m-d');
                        if (!isset($schedulesByDate[$key])) $schedulesByDate[$key] = [];
                        $schedulesByDate[$key][] = [
                            'type' => 'schedule',
                            'title' => $club->nama_club,
                            'time' => $jadwal->jam_mulai?->format('H:i') . ' - ' . $jadwal->jam_selesai?->format('H:i'),
                            'color' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                            'url' => route('clubs.show', $club),
                        ];
                    }
                }
            }
        }

        // Merge semua ke kalender
        $calendarData = [];
        for ($date = $startOfMonth->copy(); $date->lte($endOfMonth); $date->addDay()) {
            $key = $date->format('Y-m-d');
            $calendarData[$key] = array_merge(
                $eventsByDate[$key] ?? [],
                $schedulesByDate[$key] ?? []
            );
        }

        // Days grid (mulai dari hari Senin)
        $firstDayOfWeek = $startOfMonth->dayOfWeek;
        $firstDayOfWeek = $firstDayOfWeek === 0 ? 7 : $firstDayOfWeek; // Minggu = 7
        $daysInMonth = $startOfMonth->daysInMonth;

        return view('kalender.index', compact(
            'currentDate', 'prevMonth', 'nextMonth', 'calendarData',
            'firstDayOfWeek', 'daysInMonth', 'bulan'
        ));
    }
}