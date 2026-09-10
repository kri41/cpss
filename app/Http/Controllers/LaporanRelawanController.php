<?php

namespace App\Http\Controllers;

use App\Models\DurasiEntri;
use App\Models\PembuatanLaporan;
use App\Models\PointTransaction;
use App\Models\User;
use App\Services\RelawanReportService;
use App\Support\RisetLogger;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

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

        // Pastikan tiap relawan di halaman ini punya token link Live Report
        $relawan->getCollection()->each->ensurePublicReportToken();

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
        $data['riset'] = $this->report->durasi($relawan);

        return view('laporan-relawan.show', $data);
    }

    /* ================================================================
       PDF PER RELAWAN
       ================================================================ */
    public function pdf(User $relawan): Response
    {
        abort_unless($relawan->isRelawan(), 404);

        $data = $this->report->build($relawan);
        $data['riset'] = $this->report->durasi($relawan);

        $filename = 'laporan-relawan_'.str($relawan->name)->slug().'_'.now()->format('Ymd').'.pdf';

        return $this->unduhPdf('laporan-relawan.pdf', $data, $filename, 'laporan-relawan', $relawan->id);
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

        return $this->unduhPdf('laporan-relawan.rekap-pdf', compact('relawan', 'agg'), 'rekap-relawan_'.now()->format('Ymd').'.pdf', 'rekap-relawan');
    }

    /* ================================================================
       EKSPOR DATA RISET (pengukuran waktu — untuk analisis disertasi)
       ================================================================ */

    /** Satu baris per entri data: lama pengisian formulir. */
    public function risetEntriCsv(): StreamedResponse
    {
        $rows = DurasiEntri::with('user')
            ->whereHas('user', fn ($q) => $q->where('role', 'relawan'))
            ->orderBy('user_id')->orderBy('selesai_input_at')
            ->get();

        // Resolusi nama entitas per tipe
        $nama = [];
        foreach ($rows->groupBy('entri_type') as $type => $g) {
            $model = DurasiEntri::MODEL[$type] ?? null;
            $kolom = DurasiEntri::KOLOM_NAMA[$type] ?? null;
            if (! $model) {
                continue;
            }
            $items = $model::whereIn('id', $g->pluck('entri_id')->unique())->get()->keyBy('id');
            foreach ($g as $r) {
                $nama[$r->id] = $items->get($r->entri_id)?->{$kolom} ?? '(data dihapus)';
            }
        }

        return $this->streamCsv('riset_durasi-entri_'.now()->format('Ymd-Hi').'.csv',
            ['relawan', 'email', 'jenis', 'entri_id', 'nama_entri', 'mulai_input', 'selesai_input', 'durasi_detik', 'durasi_menit', 'sumber'],
            $rows->map(fn ($r) => [
                $r->user?->name,
                $r->user?->email,
                DurasiEntri::LABEL[$r->entri_type] ?? $r->entri_type,
                $r->entri_id,
                $nama[$r->id] ?? '',
                $r->mulai_input_at?->format('Y-m-d H:i:s'),
                $r->selesai_input_at?->format('Y-m-d H:i:s'),
                $r->durasi_detik,
                number_format($r->durasi_detik / 60, 2, '.', ''),
                $r->sumber,
            ])
        );
    }

    /** Satu baris per laporan yang dihasilkan. */
    public function risetLaporanCsv(): StreamedResponse
    {
        $rows = PembuatanLaporan::with(['pembuat', 'subjek'])->orderBy('dibuat_at')->get();

        return $this->streamCsv('riset_pembuatan-laporan_'.now()->format('Ymd-Hi').'.csv',
            ['pembuat', 'jenis', 'relawan_subjek', 'format', 'dibuat_pada', 'render_ms'],
            $rows->map(fn ($r) => [
                $r->pembuat?->name ?? '(tautan publik)',
                $r->jenis,
                $r->subjek?->name ?? '',
                $r->format,
                $r->dibuat_at?->format('Y-m-d H:i:s'),
                $r->render_ms,
            ])
        );
    }

    /** Rekap per relawan: total durasi input + total waktu alur kerja. */
    public function risetRekapCsv(): StreamedResponse
    {
        $relawan = User::where('role', 'relawan')->orderBy('name')->get();

        $menit = fn ($d) => $d !== null && $d !== '' ? number_format($d / 60, 2, '.', '') : '';

        $baris = $relawan->map(function (User $u) use ($menit) {
            $d = $this->report->durasi($u);

            return [
                $u->name,
                $u->email,
                $d['jumlah_entri'],
                $d['jumlah_terukur'],
                $d['jumlah_estimasi'],
                $d['total_detik'],
                $menit($d['total_detik']),
                $d['rata_detik'],
                $d['input_span_detik'],
                $menit($d['input_span_detik']),
                $d['entri_pertama_at']?->format('Y-m-d H:i:s'),
                $d['entri_terakhir_at']?->format('Y-m-d H:i:s'),
                $d['laporan_terakhir_at']?->format('Y-m-d H:i:s'),
                $d['workflow_detik'] ?? '',
                $menit($d['workflow_detik']),
            ];
        });

        return $this->streamCsv('riset_rekap-per-relawan_'.now()->format('Ymd-Hi').'.csv',
            ['relawan', 'email', 'jumlah_entri', 'entri_terukur', 'entri_estimasi',
                'total_durasi_input_detik', 'total_durasi_input_menit', 'rata2_per_entri_detik',
                'rentang_input_detik', 'rentang_input_menit',
                'entri_pertama', 'entri_terakhir', 'laporan_terakhir_sesi',
                'total_alur_kerja_detik', 'total_alur_kerja_menit'],
            $baris
        );
    }

    /* ================================================================
       HELPER
       ================================================================ */

    private function unduhPdf(string $view, array $data, string $filename, string $jenisLog, ?int $subjekId = null): Response
    {
        $t0 = hrtime(true);
        $content = Pdf::loadView($view, $data)
            ->setPaper('a4', 'portrait')
            ->setOption('isPhpEnabled', true)
            ->output();
        RisetLogger::catatLaporan($jenisLog, 'pdf', $subjekId, (hrtime(true) - $t0) / 1e6);

        return response($content, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    private function streamCsv(string $filename, array $header, iterable $rows): StreamedResponse
    {
        return response()->streamDownload(function () use ($header, $rows) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // BOM agar Excel membaca UTF-8
            fputcsv($out, $header, ',', '"', '');
            foreach ($rows as $row) {
                fputcsv($out, $row, ',', '"', '');
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
