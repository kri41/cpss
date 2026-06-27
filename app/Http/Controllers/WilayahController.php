<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class WilayahController extends Controller
{
    public function getProvinces()
    {
        $provinces = DB::table('wilayah')
            ->whereRaw('LENGTH(kode) = 2')
            ->orderBy('nama')
            ->get();
        return response()->json($provinces);
    }

    public function getKabupaten($province_id)
    {
        $kabupaten = DB::table('wilayah')
            ->where('kode', 'LIKE', $province_id . '.%')
            ->whereRaw('LENGTH(kode) = 5')
            ->orderBy('nama')
            ->get();
        return response()->json($kabupaten);
    }

    public function getKecamatan($regency_id)
    {
        $kecamatan = DB::table('wilayah')
            ->where('kode', 'LIKE', $regency_id . '.%')
            ->whereRaw('LENGTH(kode) = 8')
            ->orderBy('nama')
            ->get();
        return response()->json($kecamatan);
    }

    public function getDesa($district_id)
    {
        $desa = DB::table('wilayah')
            ->where('kode', 'LIKE', $district_id . '.%')
            ->whereRaw('LENGTH(kode) = 13')
            ->orderBy('nama')
            ->get();
        return response()->json($desa);
    }
}
