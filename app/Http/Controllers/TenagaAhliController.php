<?php

namespace App\Http\Controllers;

use App\Http\Middleware\AuditLogger;
use App\Models\TenagaAhli;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class TenagaAhliController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $tenagaAhli = TenagaAhli::with('user')
            ->latest()
            ->paginate(10);
        
        return view('tenaga-ahli.index', compact('tenagaAhli'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('tenaga-ahli.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_tenaga_ahli' => 'required|string|max:255',
            'profesi' => 'required|in:Pelatih,Wasit/Juri,Guru PJOK,Instruktur Senam',
            'nomor_sertifikat' => 'nullable|string|max:100|unique:tenaga_ahli,nomor_sertifikat',
            'tingkat_lisensi' => 'required|in:Daerah,Nasional,Internasional,Belum Berlisensi',
        ]);

        $validated['user_id'] = auth()->id();

        $tenagaAhli = TenagaAhli::create($validated);

        // Audit Log
        AuditLogger::logCreate('tenaga_ahli', $tenagaAhli->id, $validated);

        return redirect()->route('tenaga-ahli.index')
            ->with('success', 'Data tenaga ahli berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(TenagaAhli $tenagaAhli): View
    {
        return view('tenaga-ahli.show', compact('tenagaAhli'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TenagaAhli $tenagaAhli): View
    {
        return view('tenaga-ahli.edit', compact('tenagaAhli'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TenagaAhli $tenagaAhli): RedirectResponse
    {
        $validated = $request->validate([
            'nama_tenaga_ahli' => 'required|string|max:255',
            'profesi' => 'required|in:Pelatih,Wasit/Juri,Guru PJOK,Instruktur Senam',
            'nomor_sertifikat' => 'nullable|string|max:100|unique:tenaga_ahli,nomor_sertifikat,' . $tenagaAhli->id,
            'tingkat_lisensi' => 'required|in:Daerah,Nasional,Internasional,Belum Berlisensi',
        ]);

        // Simpan data lama untuk audit log
        $oldData = $tenagaAhli->toArray();

        $tenagaAhli->update($validated);

        // Audit Log
        AuditLogger::logUpdate('tenaga_ahli', $tenagaAhli->id, $oldData, $tenagaAhli->fresh()->toArray());

        return redirect()->route('tenaga-ahli.index')
            ->with('success', 'Data tenaga ahli berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TenagaAhli $tenagaAhli): RedirectResponse
    {
        // Simpan data untuk audit log
        $oldData = $tenagaAhli->toArray();

        $tenagaAhli->delete();

        // Audit Log
        AuditLogger::logDelete('tenaga_ahli', $tenagaAhli->id, $oldData);

        return redirect()->route('tenaga-ahli.index')
            ->with('success', 'Data tenaga ahli berhasil dihapus.');
    }
}
