<?php

namespace App\Http\Controllers;

use App\Models\Club;
use App\Models\Event;
use App\Models\Partisipasi;
use App\Models\Prasarana;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'App\Http\Middleware\CheckRole:admin']);
    }

    /* ================================================================
       HELPERS
       ================================================================ */

    private function csvResponse(string $filename, callable $writer): StreamedResponse
    {
        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control'       => 'no-store, no-cache',
            'Pragma'              => 'no-cache',
        ];

        return response()->stream(function () use ($writer) {
            $output = fopen('php://output', 'w');
            // BOM UTF-8 agar Excel terbuka dengan encoding benar
            fputs($output, "\xEF\xBB\xBF");
            $writer($output);
            fclose($output);
        }, 200, $headers);
    }

    private function filename(string $module, Request $request): string
    {
        $kabupaten = $request->get('kabupaten') ? '_' . str($request->kabupaten)->slug() : '';
        return $module . $kabupaten . '_' . now()->format('Y-m') . '.csv';
    }

    /* ================================================================
       PRASARANA
       ================================================================ */

    public function prasarana(Request $request): StreamedResponse
    {
        $query = Prasarana::with('user')->latest();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nama_fasilitas', 'like', '%' . $request->search . '%')
                  ->orWhere('alamat', 'like', '%' . $request->search . '%');
            });
        }
        if ($request->filled('kabupaten'))     $query->where('kabupaten', $request->kabupaten);
        if ($request->filled('kecamatan'))     $query->where('kecamatan', $request->kecamatan);
        if ($request->filled('kategori'))      $query->where('kategori_olahraga', $request->kategori);
        if ($request->filled('status_validasi')) $query->where('status_validasi', $request->status_validasi);

        $rows = $query->get();

        return $this->csvResponse($this->filename('prasarana', $request), function ($out) use ($rows) {
            fputcsv($out, ['No', 'Nama Fasilitas', 'Kategori Olahraga', 'Alamat', 'Desa', 'Kecamatan',
                           'Kabupaten', 'Kondisi Rata-rata', 'Status Kondisi', 'Akses Disabilitas',
                           'Status Validasi', 'Relawan', 'Tanggal Input']);

            foreach ($rows as $i => $p) {
                fputcsv($out, [
                    $i + 1,
                    $p->nama_fasilitas,
                    $p->kategori_olahraga,
                    $p->alamat,
                    $p->desa,
                    $p->kecamatan,
                    $p->kabupaten,
                    $p->average_kondisi ?: '-',
                    $p->status,
                    $p->akses_disabilitas ? 'Ya' : 'Tidak',
                    ucfirst($p->status_validasi),
                    $p->user?->name ?? '-',
                    $p->created_at->format('d/m/Y'),
                ]);
            }
        });
    }

    /* ================================================================
       CLUBS
       ================================================================ */

    public function clubs(Request $request): StreamedResponse
    {
        $query = Club::with('user')->latest();

        if ($request->filled('search'))    $query->where('nama_club', 'like', '%' . $request->search . '%');
        if ($request->filled('kabupaten')) $query->where('kabupaten', $request->kabupaten);
        if ($request->filled('kecamatan')) $query->where('kecamatan', $request->kecamatan);
        if ($request->filled('cabang'))    $query->where('cabang_olahraga', $request->cabang);
        if ($request->filled('status_validasi')) $query->where('status_validasi', $request->status_validasi);

        $rows = $query->get();

        return $this->csvResponse($this->filename('clubs', $request), function ($out) use ($rows) {
            fputcsv($out, ['No', 'Nama Klub', 'Cabang Olahraga', 'Desa', 'Kecamatan', 'Kabupaten',
                           'Jumlah Anggota', 'Status Aktif', 'Status Validasi', 'Relawan', 'Tanggal Input']);

            foreach ($rows as $i => $c) {
                fputcsv($out, [
                    $i + 1,
                    $c->nama_club,
                    $c->cabang_olahraga,
                    $c->desa,
                    $c->kecamatan,
                    $c->kabupaten,
                    $c->jumlah_anggota ?? '-',
                    isset($c->aktif) ? ($c->aktif ? 'Aktif' : 'Non-aktif') : '-',
                    ucfirst($c->status_validasi),
                    $c->user?->name ?? '-',
                    $c->created_at->format('d/m/Y'),
                ]);
            }
        });
    }

    /* ================================================================
       EVENTS
       ================================================================ */

    public function events(Request $request): StreamedResponse
    {
        $query = Event::with('user')->latest();

        if ($request->filled('search'))    $query->where('nama_event', 'like', '%' . $request->search . '%');
        if ($request->filled('kabupaten')) $query->where('kabupaten', $request->kabupaten);
        if ($request->filled('kecamatan')) $query->where('kecamatan', $request->kecamatan);
        if ($request->filled('tingkat'))   $query->where('tingkat', $request->tingkat);
        if ($request->filled('status_validasi')) $query->where('status_validasi', $request->status_validasi);

        $rows = $query->get();

        return $this->csvResponse($this->filename('events', $request), function ($out) use ($rows) {
            fputcsv($out, ['No', 'Nama Event', 'Tingkat', 'Tanggal Mulai', 'Tanggal Selesai',
                           'Desa', 'Kecamatan', 'Kabupaten', 'Status Validasi', 'Relawan', 'Tanggal Input']);

            foreach ($rows as $i => $e) {
                fputcsv($out, [
                    $i + 1,
                    $e->nama_event,
                    $e->tingkat,
                    $e->tanggal_mulai ? \Carbon\Carbon::parse($e->tanggal_mulai)->format('d/m/Y') : '-',
                    $e->tanggal_selesai ? \Carbon\Carbon::parse($e->tanggal_selesai)->format('d/m/Y') : '-',
                    $e->desa,
                    $e->kecamatan,
                    $e->kabupaten,
                    ucfirst($e->status_validasi),
                    $e->user?->name ?? '-',
                    $e->created_at->format('d/m/Y'),
                ]);
            }
        });
    }

    /* ================================================================
       PARTISIPASI
       ================================================================ */

    public function partisipasi(Request $request): StreamedResponse
    {
        $query = Partisipasi::with(['user', 'kehadiran'])->latest();

        if ($request->filled('search'))    $query->where('lokasi_observasi', 'like', '%' . $request->search . '%');
        if ($request->filled('kabupaten')) $query->where('kabupaten', $request->kabupaten);
        if ($request->filled('kecamatan')) $query->where('kecamatan', $request->kecamatan);
        if ($request->filled('status_validasi')) $query->where('status_validasi', $request->status_validasi);

        $rows = $query->get();

        return $this->csvResponse($this->filename('partisipasi', $request), function ($out) use ($rows) {
            fputcsv($out, ['No', 'Tanggal Observasi', 'Lokasi', 'Desa', 'Kecamatan', 'Kabupaten',
                           'Estimasi Orang', 'Mayoritas Usia', 'Kehadiran Tercatat',
                           'Status Validasi', 'Relawan', 'Tanggal Input']);

            foreach ($rows as $i => $p) {
                fputcsv($out, [
                    $i + 1,
                    \Carbon\Carbon::parse($p->tanggal_observasi)->format('d/m/Y'),
                    $p->lokasi_observasi,
                    $p->desa,
                    $p->kecamatan,
                    $p->kabupaten,
                    $p->estimasi_jumlah_orang,
                    $p->mayoritas_usia,
                    $p->kehadiran->count(),
                    ucfirst($p->status_validasi),
                    $p->user?->name ?? '-',
                    $p->created_at->format('d/m/Y'),
                ]);
            }
        });
    }

    /* ================================================================
       LEADERBOARD
       ================================================================ */

    public function leaderboard(Request $request): StreamedResponse
    {
        $rows = User::where('total_poin', '>', 0)
            ->orderByDesc('total_poin')
            ->get();

        return $this->csvResponse('leaderboard_' . now()->format('Y-m') . '.csv', function ($out) use ($rows) {
            fputcsv($out, ['Peringkat', 'Nama Relawan', 'Email', 'Wilayah', 'Total Poin', 'Bergabung Sejak']);

            foreach ($rows as $i => $u) {
                $wilayah = collect([$u->desa, $u->kecamatan, $u->kabupaten])->filter()->implode(', ');
                fputcsv($out, [
                    $i + 1,
                    $u->name,
                    $u->email,
                    $wilayah ?: '-',
                    $u->total_poin,
                    $u->created_at->format('d/m/Y'),
                ]);
            }
        });
    }
}
