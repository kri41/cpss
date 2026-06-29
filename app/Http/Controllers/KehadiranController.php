<?php

namespace App\Http\Controllers;

use App\Models\Kehadiran;
use Illuminate\Http\Request;

class KehadiranController extends Controller
{
    public function autocompleteNama(Request $request)
    {
        $query = $request->get('q');
        $names = Kehadiran::where('nama_peserta', 'like', "%{$query}%")
            ->select('nama_peserta')
            ->distinct()
            ->limit(10)
            ->pluck('nama_peserta');
        return response()->json($names);
    }
}
