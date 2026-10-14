<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\CheckinKampung;
use App\Models\Club;
use App\Models\Event;
use App\Models\Partisipasi;
use App\Models\Pengumuman;
use App\Models\Prasarana;
use App\Models\Talenta;
use App\Models\TenagaAhli;
use App\Support\RisetLogger;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
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

        // Tren partisipasi 6 bulan terakhir — digabung dari DUA sumber supaya
        // tidak "0 terus" padahal sebenarnya ada aktivitas: (1) observasi manual
        // (Partisipasi::estimasi_jumlah_orang, bisa kosong lama tidak diisi), dan
        // (2) check-in QR nyata di Kampung Olahraga (CheckinKampung, per orang).
        // Keduanya ditampilkan sebagai 2 garis terpisah (bukan dijumlah) karena
        // satuannya beda: estimasi kerumunan vs hitungan individu check-in riil.
        $partisipasiPerBulan = Partisipasi::select(
            DB::raw("DATE_FORMAT(tanggal_observasi, '%Y-%m') as bulan"),
            DB::raw('SUM(estimasi_jumlah_orang) as total')
        )
            ->where('tanggal_observasi', '>=', now()->subMonths(6))
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get();

        $checkinPerBulan = CheckinKampung::select(
            DB::raw("DATE_FORMAT(created_at, '%Y-%m') as bulan"),
            DB::raw('COUNT(*) as total')
        )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get();

        $bulanRange = collect(range(5, 0))->map(fn ($i) => now()->subMonths($i)->format('Y-m'));
        $partisipasiMap = $partisipasiPerBulan->pluck('total', 'bulan');
        $checkinMap = $checkinPerBulan->pluck('total', 'bulan');
        $trenPartisipasi = $bulanRange->map(fn ($b) => [
            'bulan' => $b,
            'label' => Carbon::createFromFormat('Y-m', $b)->locale('id')->isoFormat('MMM YYYY'),
            'observasi' => (int) ($partisipasiMap[$b] ?? 0),
            'checkin' => (int) ($checkinMap[$b] ?? 0),
        ])->values();

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

        $pengumumanAktif = Pengumuman::aktif()->latest()->get();

        return view('dashboard', compact(
            'stats',
            'partisipasiPerBulan',
            'trenPartisipasi',
            'upcomingEvents',
            'prasaranaByKondisi',
            'partisipasiByUsia',
            'recentAuditLogs',
            'pengumumanAktif'
        ));
    }

    public function laporanPdf(): Response
    {
        $user = auth()->user();
        $isRelawan = $user->isRelawan();

        $baseScope = fn ($query) => $isRelawan ? $query->where('user_id', $user->id) : $query;

        $prasarana = $baseScope(Prasarana::with(['user', 'jenisOlahraga'])->latest())->get();
        $events = $baseScope(Event::with('user')->latest())->get();
        $clubs = $baseScope(Club::with('user')->latest())->get();
        $partisipasi = $baseScope(Partisipasi::with('user')->latest())->get();

        $stats = [
            'prasarana' => $prasarana->count(),
            'prasarana_validated' => $prasarana->where('status_validasi', 'validated')->count(),
            'events' => $events->count(),
            'events_validated' => $events->where('status_validasi', 'validated')->count(),
            'clubs' => $clubs->count(),
            'clubs_validated' => $clubs->where('status_validasi', 'validated')->count(),
            'partisipasi' => $partisipasi->count(),
            'partisipasi_validated' => $partisipasi->where('status_validasi', 'validated')->count(),
        ];

        $t0 = hrtime(true);
        $content = Pdf::loadView('dashboard.laporan-pdf', compact('user', 'isRelawan', 'stats', 'prasarana', 'events', 'clubs', 'partisipasi'))
            ->setPaper('a4', 'portrait')
            ->setOption('isPhpEnabled', true)
            ->output();
        RisetLogger::catatLaporan('dashboard', 'pdf', $isRelawan ? $user->id : null, (hrtime(true) - $t0) / 1e6);

        $label = $isRelawan ? str_replace(' ', '_', $user->name) : 'semua_data';
        $filename = 'laporan_dataraga_'.$label.'_'.now()->format('Ymd').'.pdf';

        return response($content, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    public function laporanCsv(): StreamedResponse
    {
        $user = auth()->user();
        $isRelawan = $user->isRelawan();

        $baseScope = fn ($query) => $isRelawan ? $query->where('user_id', $user->id) : $query;

        $prasarana = $baseScope(Prasarana::with(['user', 'jenisOlahraga'])->latest())->get();
        $events = $baseScope(Event::with('user')->latest())->get();
        $clubs = $baseScope(Club::with(['user', 'jenisOlahraga'])->latest())->get();
        $partisipasi = $baseScope(Partisipasi::with('user')->latest())->get();

        RisetLogger::catatLaporan('dashboard', 'csv', $isRelawan ? $user->id : null);

        $label = $isRelawan ? str_replace(' ', '_', $user->name) : 'semua_data';
        $filename = 'laporan_dataraga_'.$label.'_'.now()->format('Ymd').'.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            'Cache-Control' => 'no-store, no-cache',
            'Pragma' => 'no-cache',
        ];

        return response()->stream(function () use ($prasarana, $events, $clubs, $partisipasi, $isRelawan) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");

            $columns = ['No', 'Jenis', 'Nama', 'Detail', 'Lokasi'];
            if (! $isRelawan) {
                $columns[] = 'Relawan';
            }
            $columns[] = 'Status Validasi';
            $columns[] = 'Tanggal Input';
            fputcsv($out, $columns);

            $no = 1;
            $lokasi = fn ($item) => collect([$item->desa, $item->kecamatan, $item->kabupaten])->filter()->implode(', ') ?: '-';
            $writeRow = function ($jenis, $nama, $detail, $item) use ($out, &$no, $isRelawan, $lokasi) {
                $row = [$no++, $jenis, $nama, $detail, $lokasi($item)];
                if (! $isRelawan) {
                    $row[] = $item->user?->name ?? '-';
                }
                $row[] = ucfirst($item->status_validasi);
                $row[] = $item->created_at->format('d/m/Y');
                fputcsv($out, $row);
            };

            foreach ($prasarana as $p) {
                $writeRow('Prasarana', $p->nama_fasilitas, $p->kategori_olahraga_label, $p);
            }
            foreach ($events as $e) {
                $detail = $e->tanggal_mulai ? Carbon::parse($e->tanggal_mulai)->format('d/m/Y') : '-';
                $writeRow('Event', $e->nama_event, $detail, $e);
            }
            foreach ($clubs as $c) {
                $writeRow('Klub/Komunitas', $c->nama_club, $c->jenisOlahraga?->nama ?? '-', $c);
            }
            foreach ($partisipasi as $p) {
                $detail = 'Est. '.number_format($p->estimasi_jumlah_orang ?? 0).' orang';
                $writeRow('Partisipasi', $p->nama_kegiatan ?? $p->lokasi_observasi ?? '-', $detail, $p);
            }

            fclose($out);
        }, 200, $headers);
    }
}
