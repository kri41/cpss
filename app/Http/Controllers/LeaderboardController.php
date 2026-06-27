<?php

namespace App\Http\Controllers;

use App\Models\PointTransaction;
use App\Models\User;
use Illuminate\View\View;
use Illuminate\Http\Request;

class LeaderboardController extends Controller
{
    /**
     * Display leaderboard with tabs: mingguan, bulanan, total
     */
    public function index(Request $request): View
    {
        $periode = $request->get('periode', 'bulanan'); // mingguan | bulanan | total

        $mingguAwal = now()->startOfWeek();
        $mingguAkhir = now()->endOfWeek();
        $bulanAwal = now()->startOfMonth();
        $bulanAkhir = now()->endOfMonth();

        $query = User::where('role', 'relawan')
            ->select('users.*')
            ->selectSub(function ($q) use ($mingguAwal, $mingguAkhir) {
                $q->from('point_transactions')
                  ->whereColumn('point_transactions.user_id', 'users.id')
                  ->where('point_transactions.status', 'valid')
                  ->whereBetween('point_transactions.created_at', [$mingguAwal, $mingguAkhir])
                  ->selectRaw('COALESCE(SUM(point_transactions.poin), 0)');
            }, 'poin_mingguan')
            ->selectSub(function ($q) use ($bulanAwal, $bulanAkhir) {
                $q->from('point_transactions')
                  ->whereColumn('point_transactions.user_id', 'users.id')
                  ->where('point_transactions.status', 'valid')
                  ->whereBetween('point_transactions.created_at', [$bulanAwal, $bulanAkhir])
                  ->selectRaw('COALESCE(SUM(point_transactions.poin), 0)');
            }, 'poin_bulanan')
            ->selectSub(function ($q) {
                $q->from('point_transactions')
                  ->whereColumn('point_transactions.user_id', 'users.id')
                  ->where('point_transactions.status', 'valid')
                  ->selectRaw('COALESCE(SUM(point_transactions.poin), 0)');
            }, 'poin_total');

        $orderColumn = match ($periode) {
            'mingguan' => 'poin_mingguan',
            'bulanan' => 'poin_bulanan',
            default => 'poin_total',
        };

        $leaderboard = $query->orderByDesc($orderColumn)
            ->orderBy('name')
            ->paginate(20);

        // Peringkat pribadi user yang login (meski di luar 10 besar)
        $personalRank = null;
        if (auth()->check() && auth()->user()->isRelawan()) {
            $sortQuery = User::where('role', 'relawan')
                ->select('users.id')
                ->selectSub(function ($q) use ($periode, $mingguAwal, $mingguAkhir, $bulanAwal, $bulanAkhir) {
                    $q->from('point_transactions')
                      ->whereColumn('point_transactions.user_id', 'users.id')
                      ->where('point_transactions.status', 'valid');
                    if ($periode === 'mingguan') {
                        $q->whereBetween('point_transactions.created_at', [$mingguAwal, $mingguAkhir]);
                    } elseif ($periode === 'bulanan') {
                        $q->whereBetween('point_transactions.created_at', [$bulanAwal, $bulanAkhir]);
                    }
                    $q->selectRaw('COALESCE(SUM(point_transactions.poin), 0)');
                }, 'poin_sort')
                ->orderByDesc('poin_sort')
                ->pluck('id')
                ->search(auth()->id());

            $personalRank = $sortQuery !== false ? $sortQuery + 1 : null;
        }

        return view('leaderboard.index', compact('leaderboard', 'periode', 'personalRank'));
    }

    /**
     * Display detail transaksi poin untuk user yang login
     */
    public function myPoints(): View
    {
        $transactions = PointTransaction::where('user_id', auth()->id())
            ->with('dibatalkanOleh')
            ->latest()
            ->paginate(20);

        $totalPoin = PointTransaction::totalPoinUser(auth()->id());
        $badges = auth()->user()->badges;

        return view('leaderboard.my-points', compact('transactions', 'totalPoin', 'badges'));
    }
}
