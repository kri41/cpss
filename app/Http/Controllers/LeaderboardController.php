<?php

namespace App\Http\Controllers;

use App\Models\Club;
use App\Models\KampungOlahraga;
use App\Models\PointTransaction;
use App\Models\User;
use Illuminate\View\View;
use Illuminate\Http\Request;

class LeaderboardController extends Controller
{
    /**
     * Display leaderboard with tabs: relawan (mingguan/bulanan/total), kampung, klub
     */
    public function index(Request $request): View
    {
        $tab = $request->get('tab', 'relawan'); // relawan | kampung | klub

        if ($tab === 'kampung') {
            $kampungLeaderboard = KampungOlahraga::validated()
                ->with('fasil')
                ->withCount('checkins')
                ->get()
                ->sortByDesc(fn($k) => $k->skorPoin())
                ->values();

            return view('leaderboard.index', compact('tab', 'kampungLeaderboard'));
        }

        if ($tab === 'klub') {
            $klubLeaderboard = Club::validated()->aktif()
                ->withCount('checkins')
                ->orderByDesc('checkins_count')
                ->get();

            return view('leaderboard.index', compact('tab', 'klubLeaderboard'));
        }

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

        return view('leaderboard.index', compact('tab', 'leaderboard', 'periode', 'personalRank'));
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
