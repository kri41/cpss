<?php

namespace App\Services;

use App\Models\Club;
use App\Models\DurasiEntri;
use App\Models\Event;
use App\Models\KampungOlahraga;
use App\Models\Partisipasi;
use App\Models\PembuatanLaporan;
use App\Models\PointTransaction;
use App\Models\Prasarana;
use App\Models\User;

/**
 * Menyusun data laporan lengkap satu relawan — dipakai oleh halaman detail,
 * PDF per relawan, dan halaman Live Report publik.
 */
class RelawanReportService
{
    /** related_type transaksi poin => model + kolom nama + label. */
    public const KONTRIBUSI = [
        'prasarana' => ['model' => Prasarana::class,       'nama' => 'nama_fasilitas',  'label' => 'Prasarana'],
        'club' => ['model' => Club::class,            'nama' => 'nama_club',       'label' => 'Klub/Komunitas'],
        'event' => ['model' => Event::class,           'nama' => 'nama_event',      'label' => 'Event'],
        'partisipasi' => ['model' => Partisipasi::class,     'nama' => 'lokasi_observasi', 'label' => 'Partisipasi'],
        'kampung_olahraga' => ['model' => KampungOlahraga::class, 'nama' => 'nama_kampung',    'label' => 'Kampung Olahraga'],
    ];

    public function build(User $relawan): array
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

    /* ================================================================
       DATA RISET — pengukuran waktu alur kerja (disertasi)
       ================================================================ */

    /**
     * Ringkasan durasi pengisian formulir + pembuatan laporan untuk satu relawan.
     * Dipakai pada halaman detail, PDF, dan ekspor CSV riset.
     */
    public function durasi(User $relawan): array
    {
        $entri = DurasiEntri::where('user_id', $relawan->id)
            ->orderBy('selesai_input_at')
            ->get();

        // Resolusi nama entitas
        $nama = [];
        foreach ($entri->groupBy('entri_type') as $type => $rows) {
            $model = DurasiEntri::MODEL[$type] ?? null;
            $kolom = DurasiEntri::KOLOM_NAMA[$type] ?? null;
            if (! $model) {
                continue;
            }
            $items = $model::whereIn('id', $rows->pluck('entri_id')->unique())->get()->keyBy('id');
            foreach ($rows as $r) {
                $nama[$r->id] = $items->get($r->entri_id)?->{$kolom} ?? '(data dihapus)';
            }
        }

        $laporan = PembuatanLaporan::where('subjek_user_id', $relawan->id)
            ->orWhere(fn ($q) => $q->where('user_id', $relawan->id)->whereIn('jenis', ['prasarana', 'events', 'clubs', 'partisipasi', 'dashboard']))
            ->orderBy('dibuat_at')
            ->get();

        $perKategori = $entri->groupBy('entri_type')->map(fn ($g) => [
            'jumlah' => $g->count(),
            'total_detik' => (int) $g->sum('durasi_detik'),
            'rata_detik' => (int) round($g->avg('durasi_detik')),
        ]);

        $entriPertama = $entri->first()?->mulai_input_at;
        $entriTerakhir = $entri->last()?->selesai_input_at;

        // Rentang fase input (entri pertama dibuka -> entri terakhir disimpan)
        $inputSpanDetik = ($entriPertama && $entriTerakhir && $entriTerakhir->gt($entriPertama))
            ? (int) round($entriPertama->diffInSeconds($entriTerakhir, false))
            : $entri->sum('durasi_detik');

        // "Total alur kerja" hanya bermakna bila SELURUH proses (entri pertama →
        // laporan) berada dalam satu sesi kerja: laporan <= 6 jam setelah entri
        // terakhir, dan rentang total <= 8 jam. Kalau tidak, dianggap null.
        $laporanSesi = $entriTerakhir
            ? $laporan->filter(fn ($l) => $l->dibuat_at
                && $l->dibuat_at->gte($entriTerakhir)
                && abs($l->dibuat_at->diffInHours($entriTerakhir, false)) <= 6)
            : collect();
        $laporanTerakhir = $laporanSesi->last()?->dibuat_at;

        $workflowDetik = ($entriPertama && $laporanTerakhir)
            ? (int) round($entriPertama->diffInSeconds($laporanTerakhir, false))
            : null;
        if ($workflowDetik !== null && ($workflowDetik <= 0 || $workflowDetik > 8 * 3600)) {
            $workflowDetik = null;
            $laporanTerakhir = null;
        }

        return [
            'entri' => $entri,
            'nama' => $nama,
            'laporan' => $laporan,
            'laporan_sesi' => $laporanSesi->values(),
            'per_kategori' => $perKategori,
            'jumlah_entri' => $entri->count(),
            'total_detik' => (int) $entri->sum('durasi_detik'),
            'rata_detik' => $entri->count() ? (int) round($entri->avg('durasi_detik')) : 0,
            'input_span_detik' => (int) $inputSpanDetik,
            'jumlah_terukur' => $entri->where('sumber', 'terukur')->count(),
            'jumlah_estimasi' => $entri->where('sumber', 'estimasi')->count(),
            'entri_pertama_at' => $entriPertama,
            'entri_terakhir_at' => $entriTerakhir,
            'laporan_terakhir_at' => $laporanTerakhir,
            'workflow_detik' => $workflowDetik,
            'render_ms_terakhir' => $laporanSesi->last()?->render_ms ?? $laporan->last()?->render_ms,
        ];
    }
}
