<?php

namespace App\Http\Controllers;

use App\Http\Middleware\AuditLogger;
use App\Models\Partisipasi;
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
            'tanggal_observasi' => 'required|date',
            'estimasi_jumlah_orang' => 'required|integer|min:1',
            'mayoritas_usia' => 'required|in:Anak/Pelajar,Dewasa,Lansia',
        ]);

        $validated['user_id'] = auth()->id();

        $partisipasi = Partisipasi::create($validated);

        // Audit Log
        AuditLogger::logCreate('partisipasi', $partisipasi->id, $validated);

        return redirect()->route('partisipasi.index')
            ->with('success', 'Data partisipasi berhasil dicatat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Partisipasi $partisipasi): View
    {
        return view('partisipasi.show', compact('partisipasi'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Partisipasi $partisipasi): View
    {
        return view('partisipasi.edit', compact('partisipasi'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Partisipasi $partisipasi): RedirectResponse
    {
        $validated = $request->validate([
            'lokasi_observasi' => 'required|string|max:255',
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
        // Simpan data untuk audit log
        $oldData = $partisipasi->toArray();

        $partisipasi->delete();

        // Audit Log
        AuditLogger::logDelete('partisipasi', $partisipasi->id, $oldData);

        return redirect()->route('partisipasi.index')
            ->with('success', 'Data partisipasi berhasil dihapus.');
    }
}
