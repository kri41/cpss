<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class RelawanController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::where('role', 'relawan');

        if ($request->filled('provinsi')) {
            $query->where('provinsi', $request->provinsi);
        }
        if ($request->filled('kabupaten')) {
            $query->where('kabupaten', $request->kabupaten);
        }
        if ($request->filled('kecamatan')) {
            $query->where('kecamatan', $request->kecamatan);
        }

        $relawan = $query->latest()->paginate(12)->withQueryString();

        // Ambil daftar provinsi, kabupaten, kecamatan unik dari relawan untuk filter
        $filterProvinsi = User::where('role', 'relawan')->whereNotNull('provinsi')->distinct()->pluck('provinsi');
        $filterKabupaten = User::where('role', 'relawan')->whereNotNull('kabupaten')->distinct()->pluck('kabupaten');
        $filterKecamatan = User::where('role', 'relawan')->whereNotNull('kecamatan')->distinct()->pluck('kecamatan');

        // Nama wilayah dari tabel referensi jika ada
        $wilayahNama = DB::table('wilayah')->pluck('nama', 'kode');

        return view('relawan.index', compact(
            'relawan',
            'filterProvinsi',
            'filterKabupaten',
            'filterKecamatan',
            'wilayahNama'
        ));
    }
}
