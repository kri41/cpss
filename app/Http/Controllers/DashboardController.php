<?php

namespace App\Http\Controllers;

use App\Models\Prasarana;
use App\Models\Partisipasi;
use App\Models\Event;
use App\Models\Talenta;
use App\Models\TenagaAhli;
use App\Models\AuditLog;
use App\Models\Club;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

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

    public function laporanPdf(): Response
    {
        $user      = auth()->user();
        $isRelawan = $user->isRelawan();

        $baseScope = fn($query) => $isRelawan ? $query->where('user_id', $user->id) : $query;

        $prasarana   = $baseScope(Prasarana::with(['user', 'jenisOlahraga'])->latest())->get();
        $events      = $baseScope(Event::with('user')->latest())->get();
        $clubs       = $baseScope(Club::with('user')->latest())->get();
        $partisipasi = $baseScope(Partisipasi::with('user')->latest())->get();

        $stats = [
            'prasarana'            => $prasarana->count(),
            'prasarana_validated'  => $prasarana->where('status_validasi', 'validated')->count(),
            'events'               => $events->count(),
            'events_validated'     => $events->where('status_validasi', 'validated')->count(),
            'clubs'                => $clubs->count(),
            'clubs_validated'      => $clubs->where('status_validasi', 'validated')->count(),
            'partisipasi'          => $partisipasi->count(),
            'partisipasi_validated'=> $partisipasi->where('status_validasi', 'validated')->count(),
        ];

        $pdf = Pdf::loadView('dashboard.laporan-pdf', compact('user', 'isRelawan', 'stats', 'prasarana', 'events', 'clubs', 'partisipasi'))
            ->setPaper('a4', 'portrait');

        $label    = $isRelawan ? str_replace(' ', '_', $user->name) : 'semua_data';
        $filename = 'laporan_dataraga_' . $label . '_' . now()->format('Ymd') . '.pdf';

        return $pdf->download($filename);
    }

    public function laporanCsv(): StreamedResponse
    {
        $user      = auth()->user();
        $isRelawan = $user->isRelawan();

        $baseScope = fn($query) => $isRelawan ? $query->where('user_id', $user->id) : $query;

        $prasarana   = $baseScope(Prasarana::with(['user', 'jenisOlahraga'])->latest())->get();
        $events      = $baseScope(Event::with('user')->latest())->get();
        $clubs       = $baseScope(Club::with(['user', 'jenisOlahraga'])->latest())->get();
        $partisipasi = $baseScope(Partisipasi::with('user')->latest())->get();

        $label    = $isRelawan ? str_replace(' ', '_', $user->name) : 'semua_data';
        $filename = 'laporan_dataraga_' . $label . '_' . now()->format('Ymd') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control'       => 'no-store, no-cache',
            'Pragma'              => 'no-cache',
        ];

        return response()->stream(function () use ($prasarana, $events, $clubs, $partisipasi, $isRelawan) {
            $out = fopen('php://output', 'w');
            fputs($out, "\xEF\xBB\xBF");

            $columns = ['No', 'Jenis', 'Nama', 'Detail', 'Lokasi'];
            if (!$isRelawan) $columns[] = 'Relawan';
            $columns[] = 'Status Validasi';
            $columns[] = 'Tanggal Input';
            fputcsv($out, $columns);

            $no = 1;
            $lokasi = fn($item) => collect([$item->desa, $item->kecamatan, $item->kabupaten])->filter()->implode(', ') ?: '-';
            $writeRow = function ($jenis, $nama, $detail, $item) use ($out, &$no, $isRelawan, $lokasi) {
                $row = [$no++, $jenis, $nama, $detail, $lokasi($item)];
                if (!$isRelawan) $row[] = $item->user?->name ?? '-';
                $row[] = ucfirst($item->status_validasi);
                $row[] = $item->created_at->format('d/m/Y');
                fputcsv($out, $row);
            };

            foreach ($prasarana as $p) {
                $writeRow('Prasarana', $p->nama_fasilitas, $p->kategori_olahraga_label, $p);
            }
            foreach ($events as $e) {
                $detail = $e->tanggal_mulai ? \Carbon\Carbon::parse($e->tanggal_mulai)->format('d/m/Y') : '-';
                $writeRow('Event', $e->nama_event, $detail, $e);
            }
            foreach ($clubs as $c) {
                $writeRow('Klub/Komunitas', $c->nama_club, $c->jenisOlahraga?->nama ?? '-', $c);
            }
            foreach ($partisipasi as $p) {
                $detail = 'Est. ' . number_format($p->estimasi_jumlah_orang ?? 0) . ' orang';
                $writeRow('Partisipasi', $p->nama_kegiatan ?? $p->lokasi_observasi ?? '-', $detail, $p);
            }

            fclose($out);
        }, 200, $headers);
    }
}
