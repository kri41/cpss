<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\User;
use Illuminate\View\View;

/**
 * Rekap Live publik: SATU link (token tunggal, diatur admin lewat menu
 * Laporan Relawan) yang menampilkan status SEMUA relawan — sudah/belum
 * memenuhi ambang poin sertifikasi — tanpa perlu login. Dipakai admin untuk
 * dibagikan ke pihak luar (mis. atasan/LMS) sebagai bukti pemantauan.
 * URL: /rekap/{token}
 */
class PublicRekapController extends Controller
{
    public function show(string $token): View
    {
        abort_unless(hash_equals(Setting::rekapLiveToken(), $token), 404);

        $relawan = User::where('role', 'relawan')
            ->orderByDesc('total_poin')
            ->orderBy('name')
            ->get(['id', 'name', 'kecamatan', 'kabupaten', 'total_poin']);

        $minPoin = User::MIN_POIN_LIVE_REPORT;
        $memenuhi = $relawan->where('total_poin', '>=', $minPoin)->count();

        $agg = [
            'total_relawan' => $relawan->count(),
            'memenuhi' => $memenuhi,
            'belum' => $relawan->count() - $memenuhi,
            'min_poin' => $minPoin,
        ];

        return view('laporan-relawan.rekap-live', compact('relawan', 'agg', 'minPoin'));
    }
}
