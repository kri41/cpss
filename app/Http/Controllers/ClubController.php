<?php

namespace App\Http\Controllers;

use App\Models\Club;
use App\Models\PointTransaction;
use App\Models\Prasarana;
use App\Models\JadwalLatihan;
use App\Models\UserNotification;
use App\Services\GamificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ClubController extends Controller
{
    /**
     * Display a listing of clubs.
     */
    public function index(Request $request): View
    {
        $query = Club::with(['user', 'prasarana'])->latest();

        // Guest (publik) hanya lihat yang sudah divalidasi
        if (!auth()->check()) {
            $query->validated();
        }

        // Filter: search nama
        if ($request->filled('search')) {
            $query->where('nama_club', 'like', '%' . $request->search . '%');
        }

        // Filter: kabupaten
        if ($request->filled('kabupaten')) {
            $query->where('kabupaten', $request->kabupaten);
        }

        // Filter: kecamatan
        if ($request->filled('kecamatan')) {
            $query->where('kecamatan', $request->kecamatan);
        }

        // Filter: aktif
        if ($request->filled('aktif')) {
            $query->where('aktif', $request->boolean('aktif'));
        }

        $clubs = $query->paginate(10)->withQueryString();

        // Data untuk dropdown filter
        $filterQuery = auth()->check() ? Club::query() : Club::validated();
        $kabupatenList = (clone $filterQuery)->distinct()->orderBy('kabupaten')->pluck('kabupaten')->filter();
        $kecamatanList = (clone $filterQuery)->when($request->filled('kabupaten'), fn($q) => $q->where('kabupaten', $request->kabupaten))->distinct()->orderBy('kecamatan')->pluck('kecamatan')->filter();

        $totalClubs = (clone $query)->count();
        $activeClubs = (clone $query)->where('aktif', true)->count();
        $clubsWithPrasarana = (clone $query)->whereNotNull('prasarana_id')->count();

        $isDashboard = request()->is('dashboard/*');
        $view = $isDashboard ? 'clubs.index-dashboard' : 'clubs.index';
        return view($view, compact('clubs', 'totalClubs', 'activeClubs', 'clubsWithPrasarana', 'kabupatenList', 'kecamatanList'));
    }

    /**
     * Show the form for creating a new club.
     */
    public function create(): View
    {
        $prasarana = Prasarana::all();
        return view('clubs.create', compact('prasarana'));
    }

    /**
     * Store a newly created club.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_club' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'ketua_club' => 'required|string|max:255',
            'narahubung' => 'required|string|max:255',
            'no_telepon' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'alamat' => 'nullable|string',
            'desa' => 'nullable|string|max:255',
            'kecamatan' => 'nullable|string|max:255',
            'kabupaten' => 'nullable|string|max:255',
            'prasarana_id' => 'nullable|exists:prasarana,id',
            'tanggal_berdiri' => 'nullable|date',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['aktif'] = true;

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('clubs', 'public');
            $validated['logo_path'] = $path;
        }

        $club = Club::create($validated);

        // Create default schedule if provided
        if ($request->has('jadwal')) {
            $this->createJadwal($club, $request->jadwal);
        }

        return redirect()->route('dashboard.clubs')
            ->with('success', 'Club berhasil didaftarkan. Menunggu validasi admin untuk kredit poin.');
    }

    /**
     * Display the specified club.
     */
    public function show(Club $club): View
    {
        $club->load(['user', 'prasarana', 'jadwalLatihan']);
        
        // Group schedules by day
        $jadwalByHari = $club->jadwalLatihan
            ->where('aktif', true)
            ->sortBy(function($j) {
                $days = ['Senin' => 1, 'Selasa' => 2, 'Rabu' => 3, 'Kamis' => 4, 'Jumat' => 5, 'Sabtu' => 6, 'Minggu' => 7];
                return $days[$j->hari] ?? 8;
            })
            ->groupBy('hari');

        return view('clubs.show', compact('club', 'jadwalByHari'));
    }

    /**
     * Show the form for editing the specified club.
     */
    public function edit(Club $club): View
    {
        if (!auth()->user()->canEdit($club)) {
            abort(403, 'Anda tidak memiliki izin untuk mengedit club ini.');
        }

        $prasarana = Prasarana::all();
        $club->load('jadwalLatihan');
        
        return view('clubs.edit', compact('club', 'prasarana'));
    }

    /**
     * Update the specified club.
     */
    public function update(Request $request, Club $club): RedirectResponse
    {
        if (!auth()->user()->canEdit($club)) {
            abort(403, 'Anda tidak memiliki izin untuk mengedit club ini.');
        }

        $validated = $request->validate([
            'nama_club' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'ketua_club' => 'required|string|max:255',
            'narahubung' => 'required|string|max:255',
            'no_telepon' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'alamat' => 'nullable|string',
            'desa' => 'nullable|string|max:255',
            'kecamatan' => 'nullable|string|max:255',
            'kabupaten' => 'nullable|string|max:255',
            'prasarana_id' => 'nullable|exists:prasarana,id',
            'tanggal_berdiri' => 'nullable|date',
            'aktif' => 'boolean',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $validated['aktif'] = $request->boolean('aktif', $club->aktif);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            if ($club->logo_path) {
                Storage::disk('public')->delete($club->logo_path);
            }
            $path = $request->file('logo')->store('clubs', 'public');
            $validated['logo_path'] = $path;
        }

        $club->update($validated);

        // Update schedules
        if ($request->has('jadwal')) {
            $club->jadwalLatihan()->delete();
            $this->createJadwal($club, $request->jadwal);
        }

        return redirect()->route('clubs.show', $club)
            ->with('success', 'Club berhasil diperbarui.');
    }

    /**
     * Remove the specified club.
     */
    public function destroy(Club $club): RedirectResponse
    {
        if (!auth()->user()->canEdit($club)) {
            abort(403, 'Anda tidak memiliki izin untuk menghapus club ini.');
        }

        // Delete logo if exists
        if ($club->logo_path) {
            Storage::disk('public')->delete($club->logo_path);
        }

        $club->delete();

        return redirect()->route('dashboard.clubs')
            ->with('success', 'Club berhasil dihapus.');
    }

    /**
     * Validate the specified club.
     */
    public function validateClub(Request $request, Club $club): RedirectResponse
    {
        if (!auth()->user()->canValidate($club)) {
            abort(403, 'Anda tidak memiliki izin untuk memvalidasi club ini.');
        }

        $club->update([
            'status_validasi' => 'validated',
            'komentar_validasi' => $request->input('komentar_validasi'),
        ]);

        // Gamification: berikan poin saat validasi (baru jika belum pernah, update jika sudah)
        $kode = GamificationService::resolveKodeAktivitas('club_baru', $club->user_id, 'club', $club->id);
        $tx = GamificationService::awardPoints(
            $club->user_id,
            $kode,
            'club',
            $club->id
        );

        $msg = 'Club berhasil divalidasi.';
        if ($tx) {
            $msg .= ' +' . $tx->poin . ' poin diberikan ke relawan.';

            UserNotification::create([
                'user_id' => $club->user_id,
                'type' => 'poin',
                'title' => '+' . $tx->poin . ' Poin Diterima',
                'message' => 'Club "' . $club->nama_club . '" telah divalidasi. Anda mendapatkan ' . $tx->poin . ' poin.',
                'data' => ['related_type' => 'club', 'related_id' => $club->id, 'poin' => $tx->poin],
            ]);
        }

        $redirect = redirect()->route('dashboard.clubs')->with('success', $msg);
        if ($tx) {
            $label = $tx->jenis_aksi === 'baru' ? 'Klub baru "' . $club->nama_club . '" divalidasi' : 'Update klub "' . $club->nama_club . '" divalidasi';
            $redirect->with('poin_diperoleh', ['poin' => $tx->poin, 'label' => $label]);
        }
        return $redirect;
    }

    /**
     * Cancel validation (super admin only).
     */
    public function cancelValidateClub(Club $club): RedirectResponse
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Hanya Super Admin yang dapat membatalkan validasi.');
        }

        $club->update([
            'status_validasi' => 'pending',
            'komentar_validasi' => null,
        ]);

        $tx = PointTransaction::where('related_type', 'club')
            ->where('related_id', $club->id)
            ->where('status', 'valid')
            ->first();

        if ($tx) {
            GamificationService::batalkanPoin($tx->id, auth()->id(), 'Validasi dibatalkan oleh Super Admin');
        }

        return redirect()->route('dashboard.clubs')
            ->with('success', 'Validasi club dibatalkan. Poin relawan telah ditarik.');
    }

    /**
     * Create schedules for club
     */
    private function createJadwal(Club $club, array $jadwalData): void
    {
        foreach ($jadwalData as $jadwal) {
            if (!empty($jadwal['hari']) && !empty($jadwal['jam_mulai']) && !empty($jadwal['jam_selesai'])) {
                JadwalLatihan::create([
                    'club_id' => $club->id,
                    'hari' => $jadwal['hari'],
                    'jam_mulai' => $jadwal['jam_mulai'],
                    'jam_selesai' => $jadwal['jam_selesai'],
                    'keterangan' => $jadwal['keterangan'] ?? null,
                    'aktif' => true,
                ]);
            }
        }
    }
}

