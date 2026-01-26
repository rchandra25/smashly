<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMatchRequest;
use App\Models\Game;
use App\Models\User;
use App\Services\EloService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MatchController extends Controller
{
    public function __construct(
        private EloService $eloService
    ) {}

    public function index(Request $request): View
    {
        $matches = auth()->user()->allMatches()
            ->with(['player1', 'player2', 'winner', 'gameSession'])
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('matches.index', compact('matches'));
    }

    public function create(Request $request): View
    {
        $users = User::where('id', '!=', auth()->id())
            ->orderBy('name')
            ->get();

        $selectedOpponent = $request->query('opponent_id')
            ? User::find($request->query('opponent_id'))
            : null;

        $expectedProbability = null;
        if ($selectedOpponent) {
            $expectedProbability = $this->eloService->calculateExpectedProbability(
                auth()->user()->current_elo,
                $selectedOpponent->current_elo
            );
        }

        return view('matches.create', compact('users', 'selectedOpponent', 'expectedProbability'));
    }

    public function store(StoreMatchRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $player1 = auth()->user();
        $player2 = User::findOrFail($validated['opponent_id']);

        $winnerId = $validated['player1_score'] > $validated['player2_score']
            ? $player1->id
            : $player2->id;

        $match = Game::create([
            'game_session_id' => $validated['game_session_id'] ?? null,
            'player1_id' => $player1->id,
            'player2_id' => $player2->id,
            'player1_score' => $validated['player1_score'],
            'player2_score' => $validated['player2_score'],
            'winner_id' => $winnerId,
            'submitted_by' => $player1->id,
            'status' => 'pending',
        ]);

        return redirect()
            ->route('matches.show', $match)
            ->with('success', 'Match submitted for approval.');
    }

    public function show(Game $game): View
    {
        $game->load(['player1', 'player2', 'winner', 'submittedBy', 'approvedBy', 'gameSession']);

        $explanation = null;
        if ($game->isApproved() && $game->involvesPlayer(auth()->user())) {
            $explanation = $this->eloService->explainEloChange($game, auth()->user());
        }

        return view('matches.show', ['match' => $game, 'explanation' => $explanation]);
    }

    public function pending(): View
    {
        $this->authorize('viewPending', Game::class);

        $matches = Game::where('status', 'pending')
            ->with(['player1', 'player2', 'submittedBy'])
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('matches.pending', compact('matches'));
    }

    public function approve(Game $game): RedirectResponse
    {
        $this->authorize('approve', $game);

        $game->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        $this->eloService->processMatch($game);

        return redirect()
            ->route('matches.pending')
            ->with('success', 'Match approved and Elo ratings updated.');
    }

    public function reject(Request $request, Game $game): RedirectResponse
    {
        $this->authorize('reject', $game);

        $request->validate([
            'rejection_reason' => ['nullable', 'string', 'max:500'],
        ]);

        $game->update([
            'status' => 'rejected',
            'rejection_reason' => $request->input('rejection_reason'),
        ]);

        return redirect()
            ->route('matches.pending')
            ->with('success', 'Match rejected.');
    }
}
