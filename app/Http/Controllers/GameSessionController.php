<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGameSessionRequest;
use App\Models\GameSession;
use App\Services\EloService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GameSessionController extends Controller
{
    public function __construct(
        private EloService $eloService
    ) {}

    public function index(): View
    {
        $upcomingSessions = GameSession::with(['organizer', 'players'])
            ->where('status', 'upcoming')
            ->where('session_date', '>=', now()->toDateString())
            ->orderBy('session_date')
            ->orderBy('start_time')
            ->get();

        $pastSessions = GameSession::with(['organizer', 'players'])
            ->whereIn('status', ['completed', 'in_progress'])
            ->orderByDesc('session_date')
            ->limit(10)
            ->get();

        return view('sessions.index', compact('upcomingSessions', 'pastSessions'));
    }

    public function create(): View
    {
        $this->authorize('create', GameSession::class);

        return view('sessions.create');
    }

    public function store(StoreGameSessionRequest $request): RedirectResponse
    {
        $session = GameSession::create([
            ...$request->validated(),
            'organizer_id' => auth()->id(),
        ]);

        return redirect()
            ->route('sessions.show', $session)
            ->with('success', 'Session created successfully.');
    }

    public function show(GameSession $session): View
    {
        $session->load(['organizer', 'players', 'matches.player1', 'matches.player2', 'matches.winner']);

        $suggestedPairings = [];
        if ($session->isInProgress() && $session->players->count() >= 2) {
            $suggestedPairings = $this->eloService->suggestBalancedPairings($session->players);
        }

        return view('sessions.show', compact('session', 'suggestedPairings'));
    }

    public function join(GameSession $session): RedirectResponse
    {
        $this->authorize('join', $session);

        $session->players()->attach(auth()->id(), ['status' => 'registered']);

        return redirect()
            ->route('sessions.show', $session)
            ->with('success', 'You have joined the session.');
    }

    public function leave(GameSession $session): RedirectResponse
    {
        $this->authorize('leave', $session);

        $session->players()->detach(auth()->id());

        return redirect()
            ->route('sessions.show', $session)
            ->with('success', 'You have left the session.');
    }

    public function updateStatus(Request $request, GameSession $session): RedirectResponse
    {
        $this->authorize('updateStatus', $session);

        $request->validate([
            'status' => ['required', 'in:upcoming,in_progress,completed,cancelled'],
        ]);

        $session->update(['status' => $request->input('status')]);

        return redirect()
            ->route('sessions.show', $session)
            ->with('success', 'Session status updated.');
    }
}
