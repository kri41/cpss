<?php

namespace App\Http\Controllers;

use App\Http\Middleware\AuditLogger;
use App\Models\Talenta;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class TalentaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $talenta = Talenta::with('user')
            ->latest()
            ->paginate(10);
        
        return view('talenta.index', compact('talenta'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('talenta.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_atlet' => 'required|string|max:255',
            'cabang_olahraga' => 'required|string|max:100',
            'asal_sekolah_atau_klub' => 'required|string|max:255',
            'prestasi_tertinggi' => 'nullable|string|max:500',
            'status_pembinaan' => 'required|in:Aktif PPLP,Mandiri,Lulus',
        ]);

        $validated['user_id'] = auth()->id();

        $talenta = Talenta::create($validated);

        // Audit Log
        AuditLogger::logCreate('talenta', $talenta->id, $validated);

        return redirect()->route('talenta.index')
            ->with('success', 'Data talenta berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Talenta $talenta): View
    {
        return view('talenta.show', compact('talenta'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Talenta $talenta): View
    {
        return view('talenta.edit', compact('talenta'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Talenta $talenta): RedirectResponse
    {
        $validated = $request->validate([
            'nama_atlet' => 'required|string|max:255',
            'cabang_olahraga' => 'required|string|max:100',
            'asal_sekolah_atau_klub' => 'required|string|max:255',
            'prestasi_tertinggi' => 'nullable|string|max:500',
            'status_pembinaan' => 'required|in:Aktif PPLP,Mandiri,Lulus',
        ]);

        // Simpan data lama untuk audit log
        $oldData = $talenta->toArray();

        $talenta->update($validated);

        // Audit Log
        AuditLogger::logUpdate('talenta', $talenta->id, $oldData, $talenta->fresh()->toArray());

        return redirect()->route('talenta.index')
            ->with('success', 'Data talenta berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Talenta $talenta): RedirectResponse
    {
        // Simpan data untuk audit log
        $oldData = $talenta->toArray();

        $talenta->delete();

        // Audit Log
        AuditLogger::logDelete('talenta', $talenta->id, $oldData);

        return redirect()->route('talenta.index')
            ->with('success', 'Data talenta berhasil dihapus.');
    }
}
