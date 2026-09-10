<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\RelawanReportService;
use App\Support\RisetLogger;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\View\View;

/**
 * Live Report publik: link unik per relawan yang bisa dibagikan tanpa login.
 * URL: /r/{token}
 */
class PublicReportController extends Controller
{
    public function __construct(private readonly RelawanReportService $report) {}

    public function show(string $token): View
    {
        $relawan = $this->resolve($token);

        return view('laporan-relawan.live', $this->report->build($relawan));
    }

    public function pdf(string $token): Response
    {
        $relawan = $this->resolve($token);

        // Catatan: data durasi riset TIDAK disertakan pada PDF publik (privasi).
        $data = $this->report->build($relawan);

        $t0 = hrtime(true);
        $content = Pdf::loadView('laporan-relawan.pdf', $data)
            ->setPaper('a4', 'portrait')
            ->setOption('isPhpEnabled', true)
            ->output();
        RisetLogger::catatLaporan('live', 'pdf', $relawan->id, (hrtime(true) - $t0) / 1e6);

        return response($content, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="laporan-relawan_'.str($relawan->name)->slug().'_'.now()->format('Ymd').'.pdf"',
        ]);
    }

    private function resolve(string $token): User
    {
        $relawan = User::where('public_report_token', $token)->first();

        abort_if(! $relawan || ! $relawan->isRelawan(), 404);

        return $relawan;
    }
}
