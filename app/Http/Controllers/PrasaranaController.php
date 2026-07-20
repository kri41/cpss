<?php

namespace App\Http\Controllers;

use App\Http\Middleware\AuditLogger;
use App\Models\PointTransaction;
use App\Models\Prasarana;
use App\Models\UserNotification;
use App\Services\GamificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class PrasaranaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $isDashboard = request()->is('dashboard/*');
        $user = auth()->user();

        $query = Prasarana::with('user')->latest();

        // Guest (publik) hanya lihat yang sudah divalidasi
        if (!auth()->check()) {
            $query->validated();
        }

        // Relawan di dashboard hanya lihat data di wilayahnya sendiri
        if ($isDashboard && $user?->isRelawan()) {
            $user->scopeToOwnWilayah($query);
        }

        // Filter: search nama
        if ($request->filled('search')) {
            $query->where('nama_fasilitas', 'like', '%' . $request->search . '%');
        }

        // Filter: kabupaten
        if ($request->filled('kabupaten')) {
            $query->where('kabupaten', $request->kabupaten);
        }

        // Filter: kecamatan
        if ($request->filled('kecamatan')) {
            $query->where('kecamatan', $request->kecamatan);
        }

        // Filter: kategori olahraga
        if ($request->filled('kategori')) {
            $query->where('kategori_olahraga', 'like', '%' . $request->kategori . '%');
        }

        $prasarana = $query->paginate(10)->withQueryString();

        // Data untuk dropdown filter (hanya dari data yang visible)
        $filterQuery = auth()->check() ? Prasarana::query() : Prasarana::validated();
        if ($isDashboard && $user?->isRelawan()) {
            $user->scopeToOwnWilayah($filterQuery);
        }
        $kabupatenList = (clone $filterQuery)->distinct()->orderBy('kabupaten')->pluck('kabupaten')->filter();
        $kecamatanList = (clone $filterQuery)->when($request->filled('kabupaten'), fn($q) => $q->where('kabupaten', $request->kabupaten))->distinct()->orderBy('kecamatan')->pluck('kecamatan')->filter();
        $kategoriList = (clone $filterQuery)->distinct()->orderBy('kategori_olahraga')->pluck('kategori_olahraga')->filter();

        $view = $isDashboard ? 'prasarana.index-dashboard' : 'prasarana.index';
        return view($view, compact('prasarana', 'kabupatenList', 'kecamatanList', 'kategoriList'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('prasarana.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_fasilitas' => 'required|string|max:255',
            'club_komunitas' => 'nullable|string|max:255',
            'kategori_olahraga' => 'required|string|max:100',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'alamat' => 'nullable|string',
            'desa' => 'nullable|string|max:255',
            'kecamatan' => 'nullable|string|max:255',
            'kabupaten' => 'nullable|string|max:255',
            // Kondisi (1-5)
            'kondisi_lantai' => 'nullable|integer|min:1|max:5',
            'kondisi_ring' => 'nullable|integer|min:1|max:5',
            'kondisi_net' => 'nullable|integer|min:1|max:5',
            'kondisi_gawang' => 'nullable|integer|min:1|max:5',
            'kondisi_lapangan' => 'nullable|integer|min:1|max:5',
            'kondisi_ventilasi' => 'nullable|integer|min:1|max:5',
            'kondisi_pencahayaan' => 'nullable|integer|min:1|max:5',
            'kondisi_kamar_mandi' => 'nullable|integer|min:1|max:5',
            // Akses & Fasilitas
            'akses_disabilitas' => 'boolean',
            'akses_parkir' => 'boolean',
            'akses_transportasi' => 'boolean',
            'fasilitas_ruang_ganti' => 'boolean',
            'fasilitas_tribun' => 'boolean',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'foto_tambahan' => 'nullable|array|max:4',
            'foto_tambahan.*' => 'image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['akses_disabilitas'] = $request->boolean('akses_disabilitas', false);
        $validated['akses_parkir'] = $request->boolean('akses_parkir', false);
        $validated['akses_transportasi'] = $request->boolean('akses_transportasi', false);
        $validated['fasilitas_ruang_ganti'] = $request->boolean('fasilitas_ruang_ganti', false);
        $validated['fasilitas_tribun'] = $request->boolean('fasilitas_tribun', false);

        // Handle foto utama
        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('prasarana', 'public');
            $validated['foto_path'] = $path;
        }

        // Handle foto tambahan (maks 4 foto)
        $fotoPaths = [];
        if ($request->hasFile('foto_tambahan')) {
            foreach ($request->file('foto_tambahan') as $file) {
                $fotoPaths[] = $file->store('prasarana', 'public');
            }
        }
        $validated['foto_tambahan'] = !empty($fotoPaths) ? $fotoPaths : null;

        $prasarana = Prasarana::create($validated);

        // Audit Log
        AuditLogger::logCreate('prasarana', $prasarana->id, $validated);

        return redirect()->route('dashboard.prasarana')
            ->with('success', 'Data prasarana berhasil ditambahkan. Menunggu validasi admin untuk kredit poin.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Prasarana $prasarana): View
    {
        return view('prasarana.show', compact('prasarana'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Prasarana $prasarana): View
    {
        if (!auth()->user()->canEdit($prasarana)) {
            abort(403, 'Anda tidak memiliki izin untuk mengedit data prasarana ini.');
        }

        return view('prasarana.edit', compact('prasarana'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Prasarana $prasarana): RedirectResponse
    {
        if (!auth()->user()->canEdit($prasarana)) {
            abort(403, 'Anda tidak memiliki izin untuk mengedit data prasarana ini.');
        }

        $validated = $request->validate([
            'nama_fasilitas' => 'required|string|max:255',
            'club_komunitas' => 'nullable|string|max:255',
            'kategori_olahraga' => 'required|string|max:100',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'alamat' => 'nullable|string',
            'desa' => 'nullable|string|max:255',
            'kecamatan' => 'nullable|string|max:255',
            'kabupaten' => 'nullable|string|max:255',
            // Kondisi (1-5)
            'kondisi_lantai' => 'nullable|integer|min:1|max:5',
            'kondisi_ring' => 'nullable|integer|min:1|max:5',
            'kondisi_net' => 'nullable|integer|min:1|max:5',
            'kondisi_gawang' => 'nullable|integer|min:1|max:5',
            'kondisi_lapangan' => 'nullable|integer|min:1|max:5',
            'kondisi_ventilasi' => 'nullable|integer|min:1|max:5',
            'kondisi_pencahayaan' => 'nullable|integer|min:1|max:5',
            'kondisi_kamar_mandi' => 'nullable|integer|min:1|max:5',
            // Akses & Fasilitas
            'akses_disabilitas' => 'boolean',
            'akses_parkir' => 'boolean',
            'akses_transportasi' => 'boolean',
            'fasilitas_ruang_ganti' => 'boolean',
            'fasilitas_tribun' => 'boolean',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'foto_tambahan' => 'nullable|array|max:4',
            'foto_tambahan.*' => 'image|mimes:jpeg,png,jpg|max:2048',
            'hapus_foto_tambahan' => 'nullable|array',
            'hapus_foto_tambahan.*' => 'nullable|string',
        ]);

        $validated['akses_disabilitas'] = $request->boolean('akses_disabilitas', false);
        $validated['akses_parkir'] = $request->boolean('akses_parkir', false);
        $validated['akses_transportasi'] = $request->boolean('akses_transportasi', false);
        $validated['fasilitas_ruang_ganti'] = $request->boolean('fasilitas_ruang_ganti', false);
        $validated['fasilitas_tribun'] = $request->boolean('fasilitas_tribun', false);

        // Simpan data lama untuk audit log
        $oldData = $prasarana->toArray();

        // Handle foto utama
        if ($request->hasFile('foto')) {
            if ($prasarana->foto_path) {
                Storage::disk('public')->delete($prasarana->foto_path);
            }
            $path = $request->file('foto')->store('prasarana', 'public');
            $validated['foto_path'] = $path;
        }

        // Handle hapus foto tambahan yang dipilih
        $existingFoto = $prasarana->foto_tambahan ?? [];
        $toDelete = $request->input('hapus_foto_tambahan', []);
        foreach ($toDelete as $path) {
            Storage::disk('public')->delete($path);
            $existingFoto = array_values(array_filter($existingFoto, fn($p) => $p !== $path));
        }

        // Handle upload foto tambahan baru
        if ($request->hasFile('foto_tambahan')) {
            $sisa = 4 - count($existingFoto);
            foreach (array_slice($request->file('foto_tambahan'), 0, $sisa) as $file) {
                $existingFoto[] = $file->store('prasarana', 'public');
            }
        }
        $validated['foto_tambahan'] = !empty($existingFoto) ? array_values($existingFoto) : null;

        $prasarana->update($validated);

        // Audit Log
        AuditLogger::logUpdate('prasarana', $prasarana->id, $oldData, $prasarana->fresh()->toArray());

        return redirect()->route('dashboard.prasarana')
            ->with('success', 'Data prasarana berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Prasarana $prasarana): RedirectResponse
    {
        if (!auth()->user()->canEdit($prasarana)) {
            abort(403, 'Anda tidak memiliki izin untuk menghapus data prasarana ini.');
        }

        // Simpan data untuk audit log
        $oldData = $prasarana->toArray();

        // Hapus foto jika ada
        if ($prasarana->foto_path) {
            Storage::disk('public')->delete($prasarana->foto_path);
        }

        $prasarana->delete();

        // Audit Log
        AuditLogger::logDelete('prasarana', $prasarana->id, $oldData);

        return redirect()->route('dashboard.prasarana')
            ->with('success', 'Data prasarana berhasil dihapus.');
    }

    /**
     * Validate the specified prasarana.
     */
    public function validatePrasarana(Request $request, Prasarana $prasarana): RedirectResponse
    {
        if (!auth()->user()->canValidate($prasarana)) {
            abort(403, 'Anda tidak memiliki izin untuk memvalidasi data prasarana ini.');
        }

        $prasarana->update([
            'status_validasi' => 'validated',
            'komentar_validasi' => $request->input('komentar_validasi'),
        ]);

        // Gamification: berikan poin saat validasi (baru jika belum pernah, update jika sudah)
        $kode = GamificationService::resolveKodeAktivitas('prasarana_baru', $prasarana->user_id, 'prasarana', $prasarana->id);
        $tx = GamificationService::awardPoints(
            $prasarana->user_id,
            $kode,
            'prasarana',
            $prasarana->id
        );

        $msg = 'Data prasarana berhasil divalidasi.';
        if ($tx) {
            $msg .= ' +' . $tx->poin . ' poin diberikan ke relawan.';

            // Notifikasi ke relawan
            UserNotification::create([
                'user_id' => $prasarana->user_id,
                'type' => 'poin',
                'title' => '+' . $tx->poin . ' Poin Diterima',
                'message' => 'Laporan prasarana "' . $prasarana->nama_fasilitas . '" telah divalidasi. Anda mendapatkan ' . $tx->poin . ' poin.',
                'data' => ['related_type' => 'prasarana', 'related_id' => $prasarana->id, 'poin' => $tx->poin],
            ]);
        }

        $redirect = redirect()->route('dashboard.prasarana')->with('success', $msg);
        if ($tx) {
            $label = $tx->jenis_aksi === 'baru' ? 'Prasarana baru "' . $prasarana->nama_fasilitas . '" divalidasi' : 'Update prasarana "' . $prasarana->nama_fasilitas . '" divalidasi';
            $redirect->with('poin_diperoleh', ['poin' => $tx->poin, 'label' => $label]);
        }
        return $redirect;
    }

    /**
     * Cancel validation (super admin only).
     */
    public function cancelValidatePrasarana(Prasarana $prasarana): RedirectResponse
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Hanya Super Admin yang dapat membatalkan validasi.');
        }

        $prasarana->update([
            'status_validasi' => 'pending',
            'komentar_validasi' => null,
        ]);

        // Batalkan poin terkait jika ada
        $tx = PointTransaction::where('related_type', 'prasarana')
            ->where('related_id', $prasarana->id)
            ->where('status', 'valid')
            ->first();

        if ($tx) {
            GamificationService::batalkanPoin($tx->id, auth()->id(), 'Validasi dibatalkan oleh Super Admin');
        }

        return redirect()->route('dashboard.prasarana')
            ->with('success', 'Validasi prasarana dibatalkan. Poin relawan telah ditarik.');
    }
}

