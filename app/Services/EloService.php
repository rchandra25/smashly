<?php

namespace App\Services;

use App\Models\EloHistory;
use App\Models\Game;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class EloService
{
    private const DEFAULT_K_FACTOR = 32;
    private const NEW_PLAYER_K_FACTOR = 40;
    private const HIGH_RATED_K_FACTOR = 16;
    private const NEW_PLAYER_THRESHOLD = 30;
    private const HIGH_RATED_THRESHOLD = 2400;
    private const DEFAULT_ELO = 1200;

    public function calculateExpectedProbability(int $ratingA, int $ratingB): float
    {
        return 1 / (1 + pow(10, ($ratingB - $ratingA) / 400));
    }

    public function getKFactor(User $player): int
    {
        $matchCount = $player->getMatchCount();

        if ($matchCount < self::NEW_PLAYER_THRESHOLD) {
            return self::NEW_PLAYER_K_FACTOR;
        }

        if ($player->current_elo >= self::HIGH_RATED_THRESHOLD) {
            return self::HIGH_RATED_K_FACTOR;
        }

        return self::DEFAULT_K_FACTOR;
    }

    public function calculateRatingChange(
        int $playerRating,
        int $opponentRating,
        float $actualScore,
        int $kFactor
    ): int {
        $expectedScore = $this->calculateExpectedProbability($playerRating, $opponentRating);
        return (int) round($kFactor * ($actualScore - $expectedScore));
    }

    public function processMatch(Game $match): void
    {
        if (!$match->winner_id) {
            return;
        }

        DB::transaction(function () use ($match) {
            $player1 = $match->player1;
            $player2 = $match->player2;

            $player1EloBefore = $player1->current_elo;
            $player2EloBefore = $player2->current_elo;

            $expectedProbability = $this->calculateExpectedProbability(
                $player1EloBefore,
                $player2EloBefore
            );

            $player1Won = $match->winner_id === $player1->id;
            $player1ActualScore = $player1Won ? 1 : 0;
            $player2ActualScore = $player1Won ? 0 : 1;

            $player1KFactor = $this->getKFactor($player1);
            $player2KFactor = $this->getKFactor($player2);

            $player1Change = $this->calculateRatingChange(
                $player1EloBefore,
                $player2EloBefore,
                $player1ActualScore,
                $player1KFactor
            );

            $player2Change = $this->calculateRatingChange(
                $player2EloBefore,
                $player1EloBefore,
                $player2ActualScore,
                $player2KFactor
            );

            // Update player ratings
            $player1->update(['current_elo' => $player1EloBefore + $player1Change]);
            $player2->update(['current_elo' => $player2EloBefore + $player2Change]);

            // Update match with Elo data
            $match->update([
                'player1_elo_before' => $player1EloBefore,
                'player2_elo_before' => $player2EloBefore,
                'player1_elo_change' => $player1Change,
                'player2_elo_change' => $player2Change,
                'expected_probability' => $expectedProbability,
            ]);

            // Create Elo history entries
            EloHistory::create([
                'user_id' => $player1->id,
                'elo_rating' => $player1EloBefore + $player1Change,
                'elo_change' => $player1Change,
                'match_id' => $match->id,
                'reason' => $player1Won ? 'match_win' : 'match_loss',
            ]);

            EloHistory::create([
                'user_id' => $player2->id,
                'elo_rating' => $player2EloBefore + $player2Change,
                'elo_change' => $player2Change,
                'match_id' => $match->id,
                'reason' => $player1Won ? 'match_loss' : 'match_win',
            ]);
        });
    }

    public function explainEloChange(Game $match, User $player): string
    {
        if (!$match->involvesPlayer($player)) {
            return 'Player was not involved in this match.';
        }

        $isPlayer1 = $match->player1_id === $player->id;
        $opponent = $isPlayer1 ? $match->player2 : $match->player1;
        $playerEloBefore = $isPlayer1 ? $match->player1_elo_before : $match->player2_elo_before;
        $opponentEloBefore = $isPlayer1 ? $match->player2_elo_before : $match->player1_elo_before;
        $eloChange = $isPlayer1 ? $match->player1_elo_change : $match->player2_elo_change;
        $won = $match->winner_id === $player->id;

        $expectedWinProbability = $this->calculateExpectedProbability($playerEloBefore, $opponentEloBefore);
        $expectedPercent = round($expectedWinProbability * 100);

        $eloDiff = $playerEloBefore - $opponentEloBefore;
        $wasHigherRated = $eloDiff > 0;

        $explanation = [];

        if ($won) {
            $explanation[] = "You won against {$opponent->name}.";
        } else {
            $explanation[] = "You lost against {$opponent->name}.";
        }

        $explanation[] = sprintf(
            "Your rating was %d vs their %d (you were %s by %d points).",
            $playerEloBefore,
            $opponentEloBefore,
            $wasHigherRated ? 'higher rated' : 'lower rated',
            abs($eloDiff)
        );

        $explanation[] = sprintf(
            "Based on the rating difference, you had a %d%% expected chance to win.",
            $expectedPercent
        );

        if ($won && !$wasHigherRated) {
            $explanation[] = sprintf(
                "As an upset win against a higher-rated player, you gained %+d points.",
                $eloChange
            );
        } elseif ($won && $wasHigherRated) {
            $explanation[] = sprintf(
                "As an expected win against a lower-rated player, you gained %+d points.",
                $eloChange
            );
        } elseif (!$won && $wasHigherRated) {
            $explanation[] = sprintf(
                "As an upset loss against a lower-rated player, you lost %d points.",
                abs($eloChange)
            );
        } else {
            $explanation[] = sprintf(
                "As an expected loss against a higher-rated player, you lost %d points.",
                abs($eloChange)
            );
        }

        return implode(' ', $explanation);
    }

    public function suggestBalancedPairings(Collection $players): array
    {
        if ($players->count() < 2) {
            return [];
        }

        // Sort players by Elo rating
        $sortedPlayers = $players->sortBy('current_elo')->values();
        $pairings = [];
        $paired = [];

        // Greedy pairing: match each unpaired player with closest unpaired Elo
        foreach ($sortedPlayers as $player) {
            if (in_array($player->id, $paired)) {
                continue;
            }

            $bestMatch = null;
            $smallestDiff = PHP_INT_MAX;

            foreach ($sortedPlayers as $opponent) {
                if ($opponent->id === $player->id || in_array($opponent->id, $paired)) {
                    continue;
                }

                $diff = abs($player->current_elo - $opponent->current_elo);
                if ($diff < $smallestDiff) {
                    $smallestDiff = $diff;
                    $bestMatch = $opponent;
                }
            }

            if ($bestMatch) {
                $paired[] = $player->id;
                $paired[] = $bestMatch->id;

                $expectedProbability = $this->calculateExpectedProbability(
                    $player->current_elo,
                    $bestMatch->current_elo
                );

                $pairings[] = [
                    'player1' => $player,
                    'player2' => $bestMatch,
                    'elo_difference' => $smallestDiff,
                    'player1_win_probability' => round($expectedProbability * 100),
                    'player2_win_probability' => round((1 - $expectedProbability) * 100),
                ];
            }
        }

        return $pairings;
    }

    public function getRankings(?string $period = null): Collection
    {
        $query = User::query()
            ->where('current_elo', '>', 0)
            ->orderByDesc('current_elo');

        if ($period === 'week') {
            // Get users with matches in the last week
            $query->whereHas('matchesAsPlayer1', function ($q) {
                $q->where('status', 'approved')
                    ->where('created_at', '>=', now()->subWeek());
            })->orWhereHas('matchesAsPlayer2', function ($q) {
                $q->where('status', 'approved')
                    ->where('created_at', '>=', now()->subWeek());
            });
        } elseif ($period === 'month') {
            // Get users with matches in the last month
            $query->whereHas('matchesAsPlayer1', function ($q) {
                $q->where('status', 'approved')
                    ->where('created_at', '>=', now()->subMonth());
            })->orWhereHas('matchesAsPlayer2', function ($q) {
                $q->where('status', 'approved')
                    ->where('created_at', '>=', now()->subMonth());
            });
        }

        return $query->get()->map(function ($user, $index) {
            $user->rank = $index + 1;
            return $user;
        });
    }

    public function getEloHistoryForChart(User $user, int $limit = 30): array
    {
        $history = $user->eloHistory()
            ->orderBy('created_at')
            ->limit($limit)
            ->get();

        // Add initial point if history exists
        $labels = [];
        $data = [];

        if ($history->isEmpty()) {
            // Return current Elo as single point
            return [
                'labels' => ['Now'],
                'data' => [$user->current_elo],
            ];
        }

        foreach ($history as $entry) {
            $labels[] = $entry->created_at->format('M j');
            $data[] = $entry->elo_rating;
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    public function createInitialEloHistory(User $user): void
    {
        EloHistory::create([
            'user_id' => $user->id,
            'elo_rating' => self::DEFAULT_ELO,
            'elo_change' => 0,
            'reason' => 'initial',
        ]);
    }
}
