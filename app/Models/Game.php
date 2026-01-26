<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Game extends Model
{
    use HasFactory;

    protected $table = 'matches';

    protected $fillable = [
        'game_session_id',
        'player1_id',
        'player2_id',
        'player1_score',
        'player2_score',
        'winner_id',
        'submitted_by',
        'status',
        'approved_by',
        'approved_at',
        'rejection_reason',
        'player1_elo_before',
        'player2_elo_before',
        'player1_elo_change',
        'player2_elo_change',
        'expected_probability',
    ];

    protected $casts = [
        'player1_score' => 'integer',
        'player2_score' => 'integer',
        'player1_elo_before' => 'integer',
        'player2_elo_before' => 'integer',
        'player1_elo_change' => 'integer',
        'player2_elo_change' => 'integer',
        'expected_probability' => 'decimal:4',
        'approved_at' => 'datetime',
    ];

    public function gameSession(): BelongsTo
    {
        return $this->belongsTo(GameSession::class);
    }

    public function player1(): BelongsTo
    {
        return $this->belongsTo(User::class, 'player1_id');
    }

    public function player2(): BelongsTo
    {
        return $this->belongsTo(User::class, 'player2_id');
    }

    public function winner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'winner_id');
    }

    public function loser(): BelongsTo
    {
        $loserId = $this->winner_id === $this->player1_id ? $this->player2_id : $this->player1_id;
        return $this->belongsTo(User::class, 'player2_id')->where('id', $loserId);
    }

    public function submittedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function eloHistory(): HasMany
    {
        return $this->hasMany(EloHistory::class);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function isDisputed(): bool
    {
        return $this->status === 'disputed';
    }

    public function involvesPlayer(User $user): bool
    {
        return $this->player1_id === $user->id || $this->player2_id === $user->id;
    }

    public function getOpponent(User $user): ?User
    {
        if ($this->player1_id === $user->id) {
            return $this->player2;
        }
        if ($this->player2_id === $user->id) {
            return $this->player1;
        }
        return null;
    }

    public function getPlayerScore(User $user): ?int
    {
        if ($this->player1_id === $user->id) {
            return $this->player1_score;
        }
        if ($this->player2_id === $user->id) {
            return $this->player2_score;
        }
        return null;
    }

    public function getPlayerEloChange(User $user): ?int
    {
        if ($this->player1_id === $user->id) {
            return $this->player1_elo_change;
        }
        if ($this->player2_id === $user->id) {
            return $this->player2_elo_change;
        }
        return null;
    }

    public function getPlayerEloBefore(User $user): ?int
    {
        if ($this->player1_id === $user->id) {
            return $this->player1_elo_before;
        }
        if ($this->player2_id === $user->id) {
            return $this->player2_elo_before;
        }
        return null;
    }

    public function getLoserId(): ?int
    {
        if (!$this->winner_id) {
            return null;
        }
        return $this->winner_id === $this->player1_id ? $this->player2_id : $this->player1_id;
    }
}
