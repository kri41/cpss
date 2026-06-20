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

        $query = User::where('role', 'relawan')
            ->withCount(['pointTransactions as poin_mingguan' => function ($q) {
                $q->where('status', 'valid')
                  ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
            }])
            ->withCount(['pointTransactions as poin_bulanan' => function ($q) {
                $q->where('status', 'valid')
                  ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()]);
            }])
            ->withCount(['pointTransactions as poin_total' => function ($q) {
                $q->where('status', 'valid');
            }]);

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
            $allUsers = User::where('role', 'relawan')
                ->withCount(['pointTransactions as poin_sort' => function ($q) use ($periode) {
                    $q->where('status', 'valid');
                    if ($periode === 'mingguan') {
                        $q->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    } elseif ($periode === 'bulanan') {
                        $q->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()]);
                    }
                }])
                ->orderByDesc('poin_sort')
                ->pluck('id')
                ->search(auth()->id());

            $personalRank = $allUsers !== false ? $allUsers + 1 : null;
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
