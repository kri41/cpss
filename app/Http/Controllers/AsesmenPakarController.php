<?php

namespace App\Http\Controllers;

use App\Models\AsesmenPakar;
use App\Models\DurasiEntri;
use App\Models\JenisOlahraga;
use App\Models\Prasarana;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Alur asesmen pakar (untuk RM3 — Cohen's Kappa & RM4 — efisiensi waktu):
 *  1. Ekspor "Daftar Lokasi Sasaran" (blind: tanpa kondisi) → dicetak untuk asesor.
 *  2. Form transkripsi lembar audit kertas → tabel asesmen_pakar.
 *  3. Halaman pembanding relawan vs pakar + ekspor CSV berpasangan.
 */
class AsesmenPakarController extends Controller
{
    /* ================================================================
       DAFTAR ASESMEN
       ================================================================ */
    public function index(): View
    {
        $asesmen = AsesmenPakar::with(['prasarana', 'pencatat'])
            ->latest()
            ->paginate(20);

        $totalPrasarana = Prasarana::count();
        $sudahDiasesmen = AsesmenPakar::distinct('prasarana_id')->count('prasarana_id');

        return view('asesmen-pakar.index', compact('asesmen', 'totalPrasarana', 'sudahDiasesmen'));
    }

    /* ================================================================
       1. DAFTAR LOKASI SASARAN — BLIND (tanpa kolom kondisi)
       ================================================================ */
    public function lokasiSasaran(Request $request): View
    {
        [$rows, $filter] = $this->lokasiQuery($request);

        $kabupatenList = Prasarana::whereNotNull('kabupaten')->distinct()->orderBy('kabupaten')->pluck('kabupaten');
        $kecamatanList = Prasarana::whereNotNull('kecamatan')->distinct()->orderBy('kecamatan')->pluck('kecamatan');

        return view('asesmen-pakar.lokasi-sasaran', compact('rows', 'filter', 'kabupatenList', 'kecamatanList'));
    }

    public function lokasiSasaranCsv(Request $request): StreamedResponse
    {
        [$rows] = $this->lokasiQuery($request);

        return $this->streamCsv('lokasi-sasaran-asesor_'.now()->format('Ymd-Hi').'.csv',
            ['prasarana_id', 'nama_fasilitas', 'kategori_olahraga', 'latitude', 'longitude', 'desa', 'kecamatan', 'kabupaten'],
            $rows->map(fn ($p) => [
                $p->id,
                $p->nama_fasilitas,
                $p->jenisOlahraga->pluck('nama')->implode(', '),
                $p->latitude,
                $p->longitude,
                $p->desa,
                $p->kecamatan,
                $p->kabupaten,
            ])
        );
    }

    public function lokasiSasaranPdf(Request $request): Response
    {
        [$rows, $filter] = $this->lokasiQuery($request);

        $pdf = Pdf::loadView('asesmen-pakar.lokasi-sasaran-pdf', compact('rows', 'filter'))
            ->setPaper('a4', 'portrait')
            ->setOption('isPhpEnabled', true);

        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="lokasi-sasaran-asesor_'.now()->format('Ymd').'.pdf"',
        ]);
    }

    /**
     * Borang audit kosong — polos, tanpa logo/warna ("konvensional").
     * Satu halaman per prasarana, diisi tangan oleh asesor di lapangan.
     */
    public function borangPdf(Request $request): Response
    {
        [$rows] = $this->lokasiQuery($request);
        $jenisOlahraga = JenisOlahraga::where('aktif', true)->orderBy('nama')->get();
        $ratingLabels = Prasarana::RATING_LABELS;
        $kondisi = AsesmenPakar::KONDISI;
        $akses = AsesmenPakar::AKSES;

        $pdf = Pdf::loadView('asesmen-pakar.borang-pdf', compact('rows', 'jenisOlahraga', 'ratingLabels', 'kondisi', 'akses'))
            ->setPaper('a4', 'portrait');

        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="borang-audit-kosong_'.now()->format('Ymd').'.pdf"',
        ]);
    }

    /**
     * Query prasarana untuk daftar lokasi sasaran. HANYA identitas + lokasi +
     * kategori — TIDAK memuat kolom kondisi/aksesibilitas agar asesor blind.
     */
    private function lokasiQuery(Request $request): array
    {
        $filter = [
            'kabupaten' => $request->input('kabupaten'),
            'kecamatan' => $request->input('kecamatan'),
            'status' => $request->input('status', 'validated'),
            'belum' => $request->boolean('belum'),
        ];

        $rows = Prasarana::query()
            ->select(['id', 'nama_fasilitas', 'latitude', 'longitude', 'desa', 'kecamatan', 'kabupaten', 'status_validasi'])
            ->with('jenisOlahraga:id,nama')
            ->when($filter['kabupaten'], fn ($q) => $q->where('kabupaten', $filter['kabupaten']))
            ->when($filter['kecamatan'], fn ($q) => $q->where('kecamatan', $filter['kecamatan']))
            ->when($filter['status'] && $filter['status'] !== 'semua', fn ($q) => $q->where('status_validasi', $filter['status']))
            ->when($filter['belum'], fn ($q) => $q->whereDoesntHave('asesmenPakar'))
            ->orderBy('kabupaten')->orderBy('kecamatan')->orderBy('nama_fasilitas')
            ->get();

        return [$rows, $filter];
    }

    /* ================================================================
       2. FORM TRANSKRIPSI
       ================================================================ */
    public function create(): View
    {
        return view('asesmen-pakar.create', $this->formData());
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);
        $data['dicatat_oleh'] = auth()->id();

        AsesmenPakar::create($data);

        return redirect()->route('asesmen-pakar.index')
            ->with('success', 'Data asesmen pakar berhasil disimpan.');
    }

    public function edit(AsesmenPakar $asesmenPakar): View
    {
        return view('asesmen-pakar.edit', [
            'asesmen' => $asesmenPakar,
            ...$this->formData(),
        ]);
    }

    public function update(Request $request, AsesmenPakar $asesmenPakar): RedirectResponse
    {
        $asesmenPakar->update($this->validateData($request));

        return redirect()->route('asesmen-pakar.index')
            ->with('success', 'Data asesmen pakar berhasil diperbarui.');
    }

    public function destroy(AsesmenPakar $asesmenPakar): RedirectResponse
    {
        $asesmenPakar->delete();

        return redirect()->route('asesmen-pakar.index')
            ->with('success', 'Data asesmen pakar dihapus.');
    }

    /* ================================================================
       3. PEMBANDING RELAWAN vs PAKAR
       ================================================================ */
    public function banding(): View
    {
        $pasangan = $this->pasanganBanding();

        // Ringkasan kesepakatan cepat (analisis Kappa yang sesungguhnya di Python)
        $ringkasan = ['kondisi' => [], 'akses' => []];
        foreach (AsesmenPakar::KONDISI as $key => $label) {
            $ringkasan['kondisi'][$key] = $this->hitungSepakat($pasangan, $key);
        }
        foreach (AsesmenPakar::AKSES as $key => $label) {
            $ringkasan['akses'][$key] = $this->hitungSepakat($pasangan, $key);
        }

        return view('asesmen-pakar.banding', compact('pasangan', 'ringkasan'));
    }

    public function bandingCsv(): StreamedResponse
    {
        $pasangan = $this->pasanganBanding();

        $header = ['prasarana_id', 'nama_fasilitas', 'kabupaten', 'kecamatan', 'desa', 'relawan'];
        foreach (AsesmenPakar::KONDISI as $key => $label) {
            $header[] = $key.'_relawan';
            $header[] = $key.'_pakar';
        }
        foreach (AsesmenPakar::AKSES as $key => $label) {
            $header[] = $key.'_relawan';
            $header[] = $key.'_pakar';
        }
        $header = array_merge($header, [
            'kategori_relawan', 'kategori_pakar',
            'relawan_durasi_input_detik', 'relawan_sumber_durasi',
            'pakar_nama_asesor', 'pakar_tanggal', 'pakar_waktu_mulai', 'pakar_waktu_selesai',
            'pakar_durasi_asesmen_detik', 'pakar_durasi_laporan_detik',
        ]);

        $rows = $pasangan->map(function ($x) {
            $p = $x['prasarana'];
            $a = $x['asesmen'];

            $row = [$p->id, $p->nama_fasilitas, $p->kabupaten, $p->kecamatan, $p->desa, $p->user?->name];
            foreach (array_keys(AsesmenPakar::KONDISI) as $key) {
                $row[] = $p->{$key};
                $row[] = $a->{$key};
            }
            foreach (array_keys(AsesmenPakar::AKSES) as $key) {
                $row[] = $this->boolCsv($p->{$key});
                $row[] = $this->boolCsv($a->{$key});
            }

            return array_merge($row, [
                $p->jenisOlahraga->pluck('nama')->sort()->implode('; '),
                JenisOlahraga::whereIn('id', $a->kategori_olahraga_ids ?? [])->orderBy('nama')->pluck('nama')->implode('; '),
                $x['durasi']?->durasi_detik,
                $x['durasi']?->sumber,
                $a->nama_asesor,
                $a->tanggal_asesmen?->format('Y-m-d'),
                $a->waktu_mulai?->format('Y-m-d H:i:s'),
                $a->waktu_selesai?->format('Y-m-d H:i:s'),
                $a->durasi_asesmen_detik,
                $a->durasi_laporan_detik,
            ]);
        });

        return $this->streamCsv('banding_relawan-vs-pakar_'.now()->format('Ymd-Hi').'.csv', $header, $rows);
    }

    /* ================================================================
       HELPER
       ================================================================ */

    /** Prasarana yang punya asesmen pakar, dengan data relawan + durasi input. */
    private function pasanganBanding()
    {
        $asesmenList = AsesmenPakar::with('prasarana.jenisOlahraga', 'prasarana.user')
            ->get()
            ->keyBy('prasarana_id');

        $durasi = DurasiEntri::where('entri_type', 'prasarana')
            ->whereIn('entri_id', $asesmenList->keys())
            ->get()
            ->keyBy('entri_id');

        return $asesmenList
            ->filter(fn ($a) => $a->prasarana)
            ->map(fn ($a) => [
                'prasarana' => $a->prasarana,
                'asesmen' => $a,
                'durasi' => $durasi->get($a->prasarana_id),
            ])
            ->values();
    }

    private function hitungSepakat($pasangan, string $key): array
    {
        $sepakat = 0;
        $total = 0;
        foreach ($pasangan as $x) {
            $r = $x['prasarana']->{$key};
            $p = $x['asesmen']->{$key};
            if ($r === null || $p === null) {
                continue;
            }
            $total++;
            if ((int) $r === (int) $p) {
                $sepakat++;
            }
        }

        return ['sepakat' => $sepakat, 'total' => $total, 'persen' => $total ? round($sepakat / $total * 100) : null];
    }

    private function formData(): array
    {
        return [
            'prasaranaList' => Prasarana::select('id', 'nama_fasilitas', 'kecamatan', 'kabupaten')
                ->orderBy('kabupaten')->orderBy('nama_fasilitas')->get(),
            'jenisOlahragaList' => JenisOlahraga::where('aktif', true)->orderBy('nama')->get(),
            'ratingLabels' => Prasarana::RATING_LABELS,
        ];
    }

    private function validateData(Request $request): array
    {
        $rules = [
            'prasarana_id' => ['required', 'exists:prasarana,id'],
            'nama_asesor' => ['required', 'string', 'max:255'],
            'tanggal_asesmen' => ['nullable', 'date'],
            'waktu_mulai' => ['nullable', 'date'],
            'waktu_selesai' => ['nullable', 'date', 'after_or_equal:waktu_mulai'],
            'durasi_laporan_menit' => ['nullable', 'numeric', 'min:0', 'max:600'],
            'kategori_olahraga_ids' => ['nullable', 'array'],
            'kategori_olahraga_ids.*' => ['exists:jenis_olahraga,id'],
            'catatan' => ['nullable', 'string', 'max:2000'],
        ];
        foreach (array_keys(AsesmenPakar::KONDISI) as $key) {
            $rules[$key] = ['nullable', 'integer', 'min:1', 'max:5'];
        }
        foreach (array_keys(AsesmenPakar::AKSES) as $key) {
            $rules[$key] = ['nullable', 'in:0,1'];
        }

        $data = $request->validate($rules);

        // menit → detik
        $data['durasi_laporan_detik'] = $request->filled('durasi_laporan_menit')
            ? (int) round($request->durasi_laporan_menit * 60)
            : null;
        unset($data['durasi_laporan_menit']);

        // "0"/"1"/null untuk boolean nullable
        foreach (array_keys(AsesmenPakar::AKSES) as $key) {
            $data[$key] = $request->filled($key) ? (bool) $request->input($key) : null;
        }

        return $data;
    }

    private function boolCsv($v): string
    {
        return $v === null ? '' : ($v ? '1' : '0');
    }

    private function streamCsv(string $filename, array $header, iterable $rows): StreamedResponse
    {
        return response()->streamDownload(function () use ($header, $rows) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, $header, ',', '"', '');
            foreach ($rows as $row) {
                fputcsv($out, $row, ',', '"', '');
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
