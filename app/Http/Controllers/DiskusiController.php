<?php

namespace App\Http\Controllers;

use App\Models\DiskusiPost;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DiskusiController extends Controller
{
    public function index(Request $request): View
    {
        $provinsiList = DB::table('wilayah')
            ->whereRaw('LENGTH(kode) = 2')
            ->orderBy('nama')
            ->get();

        $provinsi = $request->get('provinsi') ?: auth()->user()->provinsi;

        // Fallback ke provinsi pertama kalau user belum punya provinsi di profilnya
        if (!$provinsi || !$provinsiList->firstWhere('kode', $provinsi)) {
            $provinsi = $provinsiList->first()?->kode;
        }

        $posts = DiskusiPost::with('user')
            ->provinsi($provinsi)
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $provinsiNama = $provinsiList->firstWhere('kode', $provinsi)?->nama ?? '-';

        return view('diskusi.index', compact('posts', 'provinsi', 'provinsiNama', 'provinsiList'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'provinsi' => 'required|string|max:5',
            'pesan' => 'required|string|max:1000',
        ]);

        DiskusiPost::create([
            'provinsi' => $validated['provinsi'],
            'user_id' => auth()->id(),
            'pesan' => $validated['pesan'],
        ]);

        return redirect()->route('diskusi.index', ['provinsi' => $validated['provinsi']])
            ->with('success', 'Pesan terkirim.');
    }

    public function destroy(DiskusiPost $diskusiPost): RedirectResponse
    {
        if ($diskusiPost->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403, 'Anda tidak memiliki izin untuk menghapus pesan ini.');
        }

        $provinsi = $diskusiPost->provinsi;
        $diskusiPost->delete();

        return redirect()->route('diskusi.index', ['provinsi' => $provinsi])
            ->with('success', 'Pesan dihapus.');
    }
}
