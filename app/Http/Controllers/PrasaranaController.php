<?php

namespace App\Http\Controllers;

use App\Http\Middleware\AuditLogger;
use App\Models\Prasarana;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class PrasaranaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $prasarana = Prasarana::with('user')
            ->latest()
            ->paginate(10);
        
        return view('prasarana.index', compact('prasarana'));
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
            'kondisi_lantai' => 'required|in:Baik,Sedang,Rusak Berat',
            'akses_disabilitas' => 'boolean',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['akses_disabilitas'] = $request->boolean('akses_disabilitas', false);

        // Handle file upload
        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('prasarana', 'public');
            $validated['foto_path'] = $path;
        }

        $prasarana = Prasarana::create($validated);

        // Audit Log
        AuditLogger::logCreate('prasarana', $prasarana->id, $validated);

        return redirect()->route('prasarana.index')
            ->with('success', 'Data prasarana berhasil ditambahkan.');
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
        return view('prasarana.edit', compact('prasarana'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Prasarana $prasarana): RedirectResponse
    {
        $validated = $request->validate([
            'nama_fasilitas' => 'required|string|max:255',
            'club_komunitas' => 'nullable|string|max:255',
            'kategori_olahraga' => 'required|string|max:100',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'kondisi_lantai' => 'required|in:Baik,Sedang,Rusak Berat',
            'akses_disabilitas' => 'boolean',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $validated['akses_disabilitas'] = $request->boolean('akses_disabilitas', false);

        // Simpan data lama untuk audit log
        $oldData = $prasarana->toArray();

        // Handle file upload
        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($prasarana->foto_path) {
                Storage::disk('public')->delete($prasarana->foto_path);
            }
            $path = $request->file('foto')->store('prasarana', 'public');
            $validated['foto_path'] = $path;
        }

        $prasarana->update($validated);

        // Audit Log
        AuditLogger::logUpdate('prasarana', $prasarana->id, $oldData, $prasarana->fresh()->toArray());

        return redirect()->route('prasarana.index')
            ->with('success', 'Data prasarana berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Prasarana $prasarana): RedirectResponse
    {
        // Simpan data untuk audit log
        $oldData = $prasarana->toArray();

        // Hapus foto jika ada
        if ($prasarana->foto_path) {
            Storage::disk('public')->delete($prasarana->foto_path);
        }

        $prasarana->delete();

        // Audit Log
        AuditLogger::logDelete('prasarana', $prasarana->id, $oldData);

        return redirect()->route('prasarana.index')
            ->with('success', 'Data prasarana berhasil dihapus.');
    }
}
