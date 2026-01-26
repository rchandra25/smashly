<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GameSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'organizer_id',
        'title',
        'description',
        'session_date',
        'start_time',
        'end_time',
        'location',
        'max_players',
        'status',
    ];

    protected $casts = [
        'session_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'max_players' => 'integer',
    ];

    public function organizer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }

    public function players(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'session_players')
            ->withPivot('status')
            ->withTimestamps();
    }

    public function matches(): HasMany
    {
        return $this->hasMany(Game::class, 'game_session_id');
    }

    public function isUpcoming(): bool
    {
        return $this->status === 'upcoming';
    }

    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function isFull(): bool
    {
        return $this->max_players !== null && $this->players()->count() >= $this->max_players;
    }

    public function hasPlayer(User $user): bool
    {
        return $this->players()->where('user_id', $user->id)->exists();
    }
}
