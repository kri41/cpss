<?php

namespace App\Http\Controllers;

use App\Models\PointTransaction;
use App\Models\User;
use App\Services\RelawanReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class LaporanRelawanController extends Controller
{
    public function __construct(private readonly RelawanReportService $report) {}

    /* ================================================================
       DAFTAR RELAWAN + RINGKASAN
       ================================================================ */
    public function index(Request $request): View
    {
        $query = User::where('role', 'relawan')
            ->withCount([
                'prasarana',
                'prasarana as prasarana_valid_count' => fn ($q) => $q->where('status_validasi', 'validated'),
                'clubs',
                'clubs as clubs_valid_count' => fn ($q) => $q->where('status_validasi', 'validated'),
                'events',
                'events as events_valid_count' => fn ($q) => $q->where('status_validasi', 'validated'),
                'partisipasi',
                'partisipasi as partisipasi_valid_count' => fn ($q) => $q->where('status_validasi', 'validated'),
                'kampungOlahraga',
                'kampungOlahraga as kampung_valid_count' => fn ($q) => $q->where('status_validasi', 'validated'),
                'badges',
            ]);

        if ($request->filled('search')) {
            $s = $request->string('search');
            $query->where(fn ($q) => $q->where('name', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%"));
        }
        if ($request->filled('kabupaten')) {
            $query->where('kabupaten', $request->kabupaten);
        }
        if ($request->filled('kecamatan')) {
            $query->where('kecamatan', $request->kecamatan);
        }

        $sort = $request->input('sort', 'poin');
        match ($sort) {
            'nama' => $query->orderBy('name'),
            'kontribusi' => $query->orderByRaw('(prasarana_count + clubs_count + events_count + partisipasi_count + kampung_olahraga_count) desc'),
            'terbaru' => $query->latest(),
            default => $query->orderByDesc('total_poin')->orderBy('name'),
        };

        $relawan = $query->paginate(15)->withQueryString();

        // Ringkasan agregat semua relawan (bukan hanya halaman ini)
        $agg = [
            'total_relawan' => User::where('role', 'relawan')->count(),
            'total_poin' => (int) User::where('role', 'relawan')->sum('total_poin'),
            'aktif' => User::where('role', 'relawan')->where('total_poin', '>', 0)->count(),
            'kontribusi' => PointTransaction::where('status', 'valid')
                ->whereIn('user_id', User::where('role', 'relawan')->select('id'))
                ->count(),
        ];

        $filterKabupaten = User::where('role', 'relawan')->whereNotNull('kabupaten')->distinct()->orderBy('kabupaten')->pluck('kabupaten');
        $filterKecamatan = User::where('role', 'relawan')->whereNotNull('kecamatan')->distinct()->orderBy('kecamatan')->pluck('kecamatan');

        return view('laporan-relawan.index', compact('relawan', 'agg', 'filterKabupaten', 'filterKecamatan', 'sort'));
    }

    /* ================================================================
       DETAIL PER RELAWAN (HTML)
       ================================================================ */
    public function show(User $relawan): View
    {
        abort_unless($relawan->isRelawan(), 404);

        $data = $this->report->build($relawan);
        $data['liveUrl'] = $relawan->ensurePublicReportToken()
            ? $relawan->publicReportUrl()
            : null;

        return view('laporan-relawan.show', $data);
    }

    /* ================================================================
       PDF PER RELAWAN
       ================================================================ */
    public function pdf(User $relawan): Response
    {
        abort_unless($relawan->isRelawan(), 404);

        $pdf = Pdf::loadView('laporan-relawan.pdf', $this->report->build($relawan))
            ->setPaper('a4', 'portrait')
            ->setOption('isPhpEnabled', true);

        $filename = 'laporan-relawan_'.str($relawan->name)->slug().'_'.now()->format('Ymd').'.pdf';

        return $pdf->download($filename);
    }

    /* ================================================================
       GANTI TOKEN LINK LIVE REPORT
       ================================================================ */
    public function regenerateToken(User $relawan): RedirectResponse
    {
        abort_unless($relawan->isRelawan(), 404);

        $relawan->regeneratePublicReportToken();

        return back()->with('success', 'Link laporan publik berhasil diganti. Link lama sudah tidak berlaku.');
    }

    /* ================================================================
       PDF REKAP SEMUA RELAWAN (peringkat)
       ================================================================ */
    public function rekapPdf(): Response
    {
        $relawan = User::where('role', 'relawan')
            ->withCount([
                'prasarana', 'clubs', 'events', 'partisipasi', 'kampungOlahraga', 'badges',
            ])
            ->orderByDesc('total_poin')
            ->orderBy('name')
            ->get();

        $agg = [
            'total_relawan' => $relawan->count(),
            'total_poin' => (int) $relawan->sum('total_poin'),
            'aktif' => $relawan->where('total_poin', '>', 0)->count(),
            'kontribusi' => $relawan->sum(fn ($r) => $r->prasarana_count + $r->clubs_count + $r->events_count + $r->partisipasi_count + $r->kampung_olahraga_count),
        ];

        $pdf = Pdf::loadView('laporan-relawan.rekap-pdf', compact('relawan', 'agg'))
            ->setPaper('a4', 'portrait')
            ->setOption('isPhpEnabled', true);

        return $pdf->download('rekap-relawan_'.now()->format('Ymd').'.pdf');
    }
}
