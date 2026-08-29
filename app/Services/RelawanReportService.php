<?php

namespace App\Services;

use App\Models\Club;
use App\Models\Event;
use App\Models\KampungOlahraga;
use App\Models\Partisipasi;
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
}
