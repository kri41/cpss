<?php

namespace App\Http\Controllers;

use App\Http\Middleware\AuditLogger;
use App\Models\Kehadiran;
use App\Models\Partisipasi;
use App\Models\PointTransaction;
use App\Models\UserNotification;
use App\Services\GamificationService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class PartisipasiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $partisipasi = Partisipasi::with('user')
            ->latest()
            ->paginate(10);
        
        return view('partisipasi.index', compact('partisipasi'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('partisipasi.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'lokasi_observasi' => 'required|string|max:255',
            'desa' => 'nullable|string|max:255',
            'kecamatan' => 'nullable|string|max:255',
            'kabupaten' => 'nullable|string|max:255',
            'provinsi' => 'nullable|string|max:255',
            'tanggal_observasi' => 'required|date',
            'estimasi_jumlah_orang' => 'required|integer|min:1',
            'mayoritas_usia' => 'required|in:Anak/Pelajar,Dewasa,Lansia',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['qr_token'] = Partisipasi::generateQrToken();

        $partisipasi = Partisipasi::create($validated);

        // Audit Log
        AuditLogger::logCreate('partisipasi', $partisipasi->id, $validated);

        return redirect()->route('partisipasi.index')
            ->with('success', 'Data partisipasi berhasil dicatat. Menunggu validasi admin untuk kredit poin.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Partisipasi $partisipasi): View
    {
        $partisipasi->load('kehadiran');
        $jenisOlahraga = \App\Models\JenisOlahraga::where('aktif', true)->orderBy('nama')->pluck('nama');
        return view('partisipasi.show', compact('partisipasi', 'jenisOlahraga'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Partisipasi $partisipasi): View
    {
        if (!auth()->user()->canEdit($partisipasi)) {
            abort(403, 'Anda tidak memiliki izin untuk mengedit data partisipasi ini.');
        }

        return view('partisipasi.edit', compact('partisipasi'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Partisipasi $partisipasi): RedirectResponse
    {
        if (!auth()->user()->canEdit($partisipasi)) {
            abort(403, 'Anda tidak memiliki izin untuk mengedit data partisipasi ini.');
        }

        $validated = $request->validate([
            'lokasi_observasi' => 'required|string|max:255',
            'desa' => 'nullable|string|max:255',
            'kecamatan' => 'nullable|string|max:255',
            'kabupaten' => 'nullable|string|max:255',
            'provinsi' => 'nullable|string|max:255',
            'tanggal_observasi' => 'required|date',
            'estimasi_jumlah_orang' => 'required|integer|min:1',
            'mayoritas_usia' => 'required|in:Anak/Pelajar,Dewasa,Lansia',
        ]);

        // Simpan data lama untuk audit log
        $oldData = $partisipasi->toArray();

        $partisipasi->update($validated);

        // Audit Log
        AuditLogger::logUpdate('partisipasi', $partisipasi->id, $oldData, $partisipasi->fresh()->toArray());

        return redirect()->route('partisipasi.index')
            ->with('success', 'Data partisipasi berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Partisipasi $partisipasi): RedirectResponse
    {
        if (!auth()->user()->canEdit($partisipasi)) {
            abort(403, 'Anda tidak memiliki izin untuk menghapus data partisipasi ini.');
        }

        // Simpan data untuk audit log
        $oldData = $partisipasi->toArray();

        $partisipasi->delete();

        // Audit Log
        AuditLogger::logDelete('partisipasi', $partisipasi->id, $oldData);

        return redirect()->route('partisipasi.index')
            ->with('success', 'Data partisipasi berhasil dihapus.');
    }

    /**
     * Validate the specified partisipasi.
     */
    public function validatePartisipasi(Request $request, Partisipasi $partisipasi): RedirectResponse
    {
        if (!auth()->user()->canValidate($partisipasi)) {
            abort(403, 'Anda tidak memiliki izin untuk memvalidasi data partisipasi ini.');
        }

        $partisipasi->update([
            'status_validasi' => 'validated',
            'komentar_validasi' => $request->input('komentar_validasi'),
        ]);

        // Gamification: berikan poin saat validasi
        $tx = GamificationService::awardPoints(
            $partisipasi->user_id,
            'partisipasi_valid',
            'partisipasi',
            $partisipasi->id,
            [
                'lokasi' => $partisipasi->lokasi_observasi,
                'tanggal' => $partisipasi->tanggal_observasi->format('Y-m-d'),
            ]
        );

        $msg = 'Data partisipasi berhasil divalidasi.';
        if ($tx) {
            $msg .= ' +' . $tx->poin . ' poin diberikan ke relawan.';

            UserNotification::create([
                'user_id' => $partisipasi->user_id,
                'type' => 'poin',
                'title' => '+' . $tx->poin . ' Poin Diterima',
                'message' => 'Laporan partisipasi di "' . ($partisipasi->lokasi_observasi ?? '-') . '" telah divalidasi. Anda mendapatkan ' . $tx->poin . ' poin.',
                'data' => ['related_type' => 'partisipasi', 'related_id' => $partisipasi->id, 'poin' => $tx->poin],
            ]);
        }

        $redirect = redirect()->route('partisipasi.index')->with('success', $msg);
        if ($tx) {
            $redirect->with('poin_diperoleh', ['poin' => $tx->poin, 'label' => 'Partisipasi di "' . ($partisipasi->lokasi_observasi ?? '-') . '" divalidasi']);
        }
        return $redirect;
    }

    /**
     * Cancel validation (super admin only).
     */
    public function cancelValidatePartisipasi(Partisipasi $partisipasi): RedirectResponse
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Hanya Super Admin yang dapat membatalkan validasi.');
        }

        $partisipasi->update([
            'status_validasi' => 'pending',
            'komentar_validasi' => null,
        ]);

        $tx = PointTransaction::where('related_type', 'partisipasi')
            ->where('related_id', $partisipasi->id)
            ->where('status', 'valid')
            ->first();

        if ($tx) {
            GamificationService::batalkanPoin($tx->id, auth()->id(), 'Validasi dibatalkan oleh Super Admin');
        }

        return redirect()->route('partisipasi.index')
            ->with('success', 'Validasi partisipasi dibatalkan. Poin relawan telah ditarik.');
    }

    /* ============================================================
       KEHADIRAN (PRESENSI SEDERHANA)
       ============================================================ */

    /**
     * Store kehadiran peserta untuk sebuah partisipasi
     */
    public function storeKehadiran(Request $request, Partisipasi $partisipasi): RedirectResponse
    {
        $validated = $request->validate([
            'nama_peserta' => 'required|string|max:255',
            'jenis_olahraga' => 'nullable|string|max:100',
            'rpe' => 'nullable|integer|min:1|max:10',
            'jenis_kelamin' => 'nullable|in:L,P',
            'usia' => 'nullable|integer|min:0|max:120',
            'kelompok_usia' => 'nullable|in:Anak,Remaja,Dewasa,Lansia',
            'kategori_khusus' => 'nullable|string|max:255',
            'catatan' => 'nullable|string',
        ]);

        $validated['partisipasi_id'] = $partisipasi->id;
        $validated['status'] = 'Hadir';
        $validated['created_by'] = auth()->id();
        $validated['sumber'] = 'manual';

        Kehadiran::create($validated);

        return redirect()->route('partisipasi.show', $partisipasi)
            ->with('success', 'Kehadiran peserta berhasil dicatat.');
    }

    /**
     * Update kehadiran peserta
     */
    public function updateKehadiran(Request $request, Kehadiran $kehadiran): RedirectResponse
    {
        $validated = $request->validate([
            'nama_peserta' => 'required|string|max:255',
            'jenis_kelamin' => 'nullable|in:L,P',
            'usia' => 'nullable|integer|min:0|max:120',
            'kelompok_usia' => 'nullable|in:Anak,Remaja,Dewasa,Lansia',
            'kategori_khusus' => 'nullable|string|max:255',
            'catatan' => 'nullable|string',
        ]);

        $kehadiran->update($validated);

        return redirect()->route('partisipasi.show', $kehadiran->partisipasi_id)
            ->with('success', 'Data kehadiran berhasil diperbarui.');
    }

    /**
     * Hapus kehadiran peserta
     */
    public function destroyKehadiran(Kehadiran $kehadiran): RedirectResponse
    {
        $partisipasiId = $kehadiran->partisipasi_id;
        $kehadiran->delete();

        return redirect()->route('partisipasi.show', $partisipasiId)
            ->with('success', 'Data kehadiran berhasil dihapus.');
    }

    /* ============================================================
       QR CODE & PENDAFTARAN PUBLIK
       ============================================================ */

    /**
     * Tampilkan QR Code untuk partisipasi
     */
    public function showQr(Partisipasi $partisipasi): View
    {
        $qrUrl = route('partisipasi.daftar', $partisipasi);
        return view('partisipasi.qr', compact('partisipasi', 'qrUrl'));
    }

    /**
     * Halaman publik untuk partisipan mendaftar via QR
     */
    public function daftarPublik(Request $request, Partisipasi $partisipasi)
    {
        $jenisOlahraga = \App\Models\JenisOlahraga::where('aktif', true)->orderBy('nama')->pluck('nama');
        if ($request->isMethod('get')) {
            return view('partisipasi.daftar', compact('partisipasi', 'jenisOlahraga'));
        }

        $validated = $request->validate([
            'nama_peserta' => 'required|string|max:255',
            'jenis_olahraga' => 'nullable|string|max:100',
            'rpe' => 'nullable|integer|min:1|max:10',
            'jenis_kelamin' => 'nullable|in:L,P',
            'usia' => 'nullable|integer|min:0|max:120',
            'kelompok_usia' => 'nullable|in:Anak,Remaja,Dewasa,Lansia',
            'kategori_khusus' => 'nullable|string|max:255',
            'catatan' => 'nullable|string',
        ]);

        $validated['partisipasi_id'] = $partisipasi->id;
        $validated['status'] = 'Hadir';
        $validated['created_by'] = $partisipasi->user_id;
        $validated['sumber'] = 'qr';

        Kehadiran::create($validated);

        return back()->with('success', 'Terima kasih! Kehadiran Anda telah dicatat.')->with('jenisOlahraga', $jenisOlahraga);
    }
}
