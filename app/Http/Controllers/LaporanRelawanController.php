<?php

namespace App\Http\Controllers;

use App\Models\Club;
use App\Models\Event;
use App\Models\KampungOlahraga;
use App\Models\Partisipasi;
use App\Models\PointTransaction;
use App\Models\Prasarana;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class LaporanRelawanController extends Controller
{
    /**
     * Peta jenis kontribusi: related_type transaksi poin => model + kolom nama + label.
     */
    private const KONTRIBUSI = [
        'prasarana' => ['model' => Prasarana::class,       'nama' => 'nama_fasilitas', 'label' => 'Prasarana'],
        'club' => ['model' => Club::class,            'nama' => 'nama_club',      'label' => 'Klub/Komunitas'],
        'event' => ['model' => Event::class,           'nama' => 'nama_event',     'label' => 'Event'],
        'partisipasi' => ['model' => Partisipasi::class,     'nama' => 'lokasi_observasi', 'label' => 'Partisipasi'],
        'kampung_olahraga' => ['model' => KampungOlahraga::class, 'nama' => 'nama_kampung',   'label' => 'Kampung Olahraga'],
    ];

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

        return view('laporan-relawan.show', $this->buildReport($relawan));
    }

    /* ================================================================
       PDF PER RELAWAN
       ================================================================ */
    public function pdf(User $relawan): Response
    {
        abort_unless($relawan->isRelawan(), 404);

        $pdf = Pdf::loadView('laporan-relawan.pdf', $this->buildReport($relawan))
            ->setPaper('a4', 'portrait')
            ->setOption('isPhpEnabled', true);

        $filename = 'laporan-relawan_'.str($relawan->name)->slug().'_'.now()->format('Ymd').'.pdf';

        return $pdf->download($filename);
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

    /* ================================================================
       HELPER — susun data lengkap satu relawan
       ================================================================ */
    private function buildReport(User $relawan): array
    {
        $prasarana = Prasarana::with('jenisOlahraga')->where('user_id', $relawan->id)->latest()->get();
        $clubs = Club::with('jenisOlahraga')->where('user_id', $relawan->id)->latest()->get();
        $events = Event::where('user_id', $relawan->id)->latest()->get();
        $partisipasi = Partisipasi::withCount('kehadiran')->where('user_id', $relawan->id)->latest()->get();
        $kampung = KampungOlahraga::withCount('checkins')->where('user_id', $relawan->id)->latest()->get();

        $transaksi = PointTransaction::where('user_id', $relawan->id)
            ->with('dibatalkanOleh')
            ->latest()
            ->get();

        // Resolusi nama entitas terkait tiap transaksi poin
        $targetNama = [];
        foreach ($transaksi->groupBy('related_type') as $type => $rows) {
            $cfg = self::KONTRIBUSI[$type] ?? null;
            if (! $cfg) {
                continue;
            }
            $models = $cfg['model']::whereIn('id', $rows->pluck('related_id')->unique())->get()->keyBy('id');
            foreach ($rows as $row) {
                $m = $models->get($row->related_id);
                $targetNama[$row->id] = [
                    'label' => $cfg['label'],
                    'nama' => $m?->{$cfg['nama']} ?? '(data dihapus)',
                ];
            }
        }

        $poinPerKategori = $transaksi->where('status', 'valid')
            ->groupBy('related_type')
            ->map(fn ($g) => (int) $g->sum('poin'));

        $stats = [
            'prasarana' => ['total' => $prasarana->count(),   'valid' => $prasarana->where('status_validasi', 'validated')->count()],
            'clubs' => ['total' => $clubs->count(),        'valid' => $clubs->where('status_validasi', 'validated')->count()],
            'events' => ['total' => $events->count(),       'valid' => $events->where('status_validasi', 'validated')->count()],
            'partisipasi' => ['total' => $partisipasi->count(),  'valid' => $partisipasi->where('status_validasi', 'validated')->count()],
            'kampung' => ['total' => $kampung->count(),      'valid' => $kampung->where('status_validasi', 'validated')->count()],
        ];
        $totalKontribusi = collect($stats)->sum('total');
        $totalValid = collect($stats)->sum('valid');

        $poinValid = (int) $transaksi->where('status', 'valid')->sum('poin');
        $poinBulanIni = (int) $transaksi->where('status', 'valid')
            ->where('created_at', '>=', now()->startOfMonth())
            ->sum('poin');

        $rank = User::where('role', 'relawan')->where('total_poin', '>', $relawan->total_poin ?? 0)->count() + 1;
        $totalRelawan = User::where('role', 'relawan')->count();

        $badges = $relawan->badges()->get();

        $kontribusiPertama = $transaksi->sortBy('created_at')->first()?->created_at;
        $kontribusiTerakhir = $transaksi->sortByDesc('created_at')->first()?->created_at;

        return compact(
            'relawan', 'prasarana', 'clubs', 'events', 'partisipasi', 'kampung',
            'transaksi', 'targetNama', 'poinPerKategori', 'stats',
            'totalKontribusi', 'totalValid', 'poinValid', 'poinBulanIni',
            'rank', 'totalRelawan', 'badges', 'kontribusiPertama', 'kontribusiTerakhir',
        );
    }
}
