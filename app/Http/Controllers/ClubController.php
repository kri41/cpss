<?php

namespace App\Http\Controllers;

use App\Models\Club;
use App\Models\Prasarana;
use App\Models\JadwalLatihan;
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
    public function index(): View
    {
        $clubs = Club::with(['user', 'prasarana'])->latest()->paginate(10);

        $totalClubs = Club::count();
        $activeClubs = Club::where('aktif', true)->count();
        $clubsWithPrasarana = Club::whereNotNull('prasarana_id')->count();

        return view('clubs.index', compact('clubs', 'totalClubs', 'activeClubs', 'clubsWithPrasarana'));
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

        // Gamification: Club Baru
        GamificationService::awardPoints(
            auth()->id(),
            'club_baru',
            'club',
            $club->id
        );

        return redirect()->route('clubs.show', $club)
            ->with('success', 'Club berhasil dibuat.');
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

        // Gamification: Club Update
        GamificationService::awardPoints(
            auth()->id(),
            'club_update',
            'club',
            $club->id
        );

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

        return redirect()->route('clubs.index')
            ->with('success', 'Club berhasil dihapus.');
    }

    /**
     * Validate the specified club.
     */
    public function validateClub(Club $club): RedirectResponse
    {
        if (!auth()->user()->canValidate($club)) {
            abort(403, 'Anda tidak memiliki izin untuk memvalidasi club ini.');
        }

        $club->update(['status_validasi' => 'validated']);

        return redirect()->route('clubs.index')
            ->with('success', 'Club berhasil divalidasi.');
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
