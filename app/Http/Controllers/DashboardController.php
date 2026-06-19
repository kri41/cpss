<?php

namespace App\Http\Controllers;

use App\Models\Prasarana;
use App\Models\Partisipasi;
use App\Models\Event;
use App\Models\Talenta;
use App\Models\TenagaAhli;
use App\Models\AuditLog;
use App\Models\Club;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Show the dashboard.
     */
    public function index(): View
    {
        // Statistik total
        $stats = [
            'total_prasarana' => Prasarana::count(),
            'total_clubs' => Club::count(),
            'total_partisipasi' => Partisipasi::totalPartisipasi(),
            'total_events' => Event::count(),
            'total_talenta' => Talenta::count(),
            'total_tenaga_ahli' => TenagaAhli::count(),
        ];

        // Data partisipasi per bulan (6 bulan terakhir) - MySQL compatible
        $partisipasiPerBulan = Partisipasi::select(
                DB::raw("DATE_FORMAT(tanggal_observasi, '%Y-%m') as bulan"),
                DB::raw('SUM(estimasi_jumlah_orang) as total')
            )
            ->where('tanggal_observasi', '>=', now()->subMonths(6))
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get();

        // Event yang akan datang
        $upcomingEvents = Event::akanDatang()
            ->limit(5)
            ->get();

        // Data prasarana berdasarkan kondisi
        $prasaranaByKondisi = Prasarana::selectRaw('kondisi_lantai, COUNT(*) as total')
            ->whereNotNull('kondisi_lantai')
            ->groupBy('kondisi_lantai')
            ->get();

        // Data partisipasi berdasarkan kelompok usia
        $partisipasiByUsia = Partisipasi::selectRaw('mayoritas_usia, SUM(estimasi_jumlah_orang) as total')
            ->groupBy('mayoritas_usia')
            ->get();

        // Audit logs terbaru (untuk admin)
        $recentAuditLogs = collect();
        if (auth()->user()->isAdmin()) {
            $recentAuditLogs = AuditLog::with('user')
                ->latest()
                ->limit(10)
                ->get();
        }

        return view('dashboard', compact(
            'stats',
            'partisipasiPerBulan',
            'upcomingEvents',
            'prasaranaByKondisi',
            'partisipasiByUsia',
            'recentAuditLogs'
        ));
    }
}
