<?php

namespace App\Http\Controllers;

use App\Models\KomponenSyarat;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KomponenSyaratController extends Controller
{
    public function index(): View
    {
        $komponen = KomponenSyarat::orderBy('urutan')->get();
        return view('komponen-syarat.index', compact('komponen'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama'           => 'required|string|max:255',
            'deskripsi'      => 'nullable|string',
            'target_checkin' => 'required|integer|min:1',
            'poin'           => 'required|integer|min:0',
            'urutan'         => 'nullable|integer|min:0',
        ]);

        $validated['urutan'] = $validated['urutan'] ?? ((int) KomponenSyarat::max('urutan') + 1);
        $validated['aktif']  = true;

        KomponenSyarat::create($validated);

        return back()->with('success', 'Komponen syarat berhasil ditambahkan.');
    }

    public function update(Request $request, KomponenSyarat $komponenSyarat): RedirectResponse
    {
        $validated = $request->validate([
            'nama'           => 'required|string|max:255',
            'deskripsi'      => 'nullable|string',
            'target_checkin' => 'required|integer|min:1',
            'poin'           => 'required|integer|min:0',
            'urutan'         => 'nullable|integer|min:0',
            'aktif'          => 'boolean',
        ]);

        $validated['aktif'] = $request->boolean('aktif');
        $komponenSyarat->update($validated);

        return back()->with('success', 'Komponen syarat berhasil diperbarui.');
    }

    public function destroy(KomponenSyarat $komponenSyarat): RedirectResponse
    {
        $komponenSyarat->delete();
        return back()->with('success', 'Komponen syarat berhasil dihapus.');
    }
}
