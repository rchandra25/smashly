<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'is_organizer',
        'current_elo',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_organizer' => 'boolean',
            'current_elo' => 'integer',
        ];
    }

    public function eloHistory(): HasMany
    {
        return $this->hasMany(EloHistory::class);
    }

    public function gameSessions(): BelongsToMany
    {
        return $this->belongsToMany(GameSession::class, 'session_players')
            ->withPivot('status')
            ->withTimestamps();
    }

    public function organizedSessions(): HasMany
    {
        return $this->hasMany(GameSession::class, 'organizer_id');
    }

    public function matchesAsPlayer1(): HasMany
    {
        return $this->hasMany(Game::class, 'player1_id');
    }

    public function matchesAsPlayer2(): HasMany
    {
        return $this->hasMany(Game::class, 'player2_id');
    }

    public function matchesWon(): HasMany
    {
        return $this->hasMany(Game::class, 'winner_id');
    }

    public function allMatches()
    {
        return Game::where('player1_id', $this->id)
            ->orWhere('player2_id', $this->id);
    }

    public function approvedMatches()
    {
        return $this->allMatches()->where('status', 'approved');
    }

    public function getMatchCount(): int
    {
        return $this->approvedMatches()->count();
    }

    public function getWinCount(): int
    {
        return $this->matchesWon()->where('status', 'approved')->count();
    }

    public function getLossCount(): int
    {
        return $this->getMatchCount() - $this->getWinCount();
    }

    public function getWinRate(): float
    {
        $total = $this->getMatchCount();
        return $total > 0 ? round(($this->getWinCount() / $total) * 100, 1) : 0;
    }

    public function getLatestEloChange(): ?int
    {
        $latest = $this->eloHistory()->latest()->first();
        return $latest?->elo_change;
    }

    public function isOrganizer(): bool
    {
        return $this->is_organizer;
    }
}
