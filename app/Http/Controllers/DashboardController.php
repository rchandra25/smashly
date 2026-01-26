<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Services\EloService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private EloService $eloService
    ) {}

    public function index(): View
    {
        $user = auth()->user();

        // Get recent matches
        $recentMatches = $user->allMatches()
            ->with(['player1', 'player2', 'winner'])
            ->where('status', 'approved')
            ->orderByDesc('approved_at')
            ->limit(5)
            ->get();

        // Get Elo chart data
        $chartData = $this->eloService->getEloHistoryForChart($user);

        // Get stats
        $stats = [
            'matches_played' => $user->getMatchCount(),
            'wins' => $user->getWinCount(),
            'losses' => $user->getLossCount(),
            'win_rate' => $user->getWinRate(),
        ];

        // Get pending matches count for organizers
        $pendingCount = 0;
        if ($user->isOrganizer()) {
            $pendingCount = Game::where('status', 'pending')->count();
        }

        return view('dashboard', compact('user', 'recentMatches', 'chartData', 'stats', 'pendingCount'));
    }
}
