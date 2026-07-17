<?php

namespace App\Http\Controllers;

use App\Models\Badge;
use App\Models\Club;
use App\Models\Event;
use App\Models\Partisipasi;
use App\Models\PointTransaction;
use App\Models\Prasarana;
use App\Models\User;
use Illuminate\View\View;

class ProfilController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        $stats = [
            'prasarana'  => Prasarana::where('user_id', $user->id)->count(),
            'clubs'      => Club::where('user_id', $user->id)->count(),
            'events'     => Event::where('user_id', $user->id)->count(),
            'partisipasi'=> Partisipasi::where('user_id', $user->id)->count(),
        ];

        $stats['total'] = array_sum($stats);

        $stats['prasarana_validated']   = Prasarana::where('user_id', $user->id)->where('status_validasi', 'validated')->count();
        $stats['clubs_validated']       = Club::where('user_id', $user->id)->where('status_validasi', 'validated')->count();
        $stats['events_validated']      = Event::where('user_id', $user->id)->where('status_validasi', 'validated')->count();
        $stats['partisipasi_validated'] = Partisipasi::where('user_id', $user->id)->where('status_validasi', 'validated')->count();

        $allBadges = Badge::all();
        $earnedBadgeIds = $user->badges->pluck('id')->toArray();

        $badges = $allBadges->map(function ($badge) use ($earnedBadgeIds, $user) {
            $badge->earned = in_array($badge->id, $earnedBadgeIds);
            $badge->earned_at = $badge->earned
                ? $user->badges->where('id', $badge->id)->first()?->pivot?->earned_at
                : null;
            return $badge;
        });

        $rank = User::where('total_poin', '>', $user->total_poin ?? 0)->count() + 1;
        $totalActiveUsers = User::where('total_poin', '>', 0)->count();

        $recentTransactions = PointTransaction::where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        return view('profil.index', compact(
            'user',
            'stats',
            'badges',
            'rank',
            'totalActiveUsers',
            'recentTransactions'
        ));
    }
}
