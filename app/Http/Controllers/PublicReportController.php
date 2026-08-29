<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\RelawanReportService;
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

        $pdf = Pdf::loadView('laporan-relawan.pdf', $this->report->build($relawan))
            ->setPaper('a4', 'portrait')
            ->setOption('isPhpEnabled', true);

        return $pdf->download('laporan-relawan_'.str($relawan->name)->slug().'_'.now()->format('Ymd').'.pdf');
    }

    private function resolve(string $token): User
    {
        $relawan = User::where('public_report_token', $token)->first();

        abort_if(! $relawan || ! $relawan->isRelawan(), 404);

        return $relawan;
    }
}
