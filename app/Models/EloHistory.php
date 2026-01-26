<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EloHistory extends Model
{
    use HasFactory;

    protected $table = 'elo_history';

    protected $fillable = [
        'user_id',
        'elo_rating',
        'elo_change',
        'match_id',
        'reason',
    ];

    protected $casts = [
        'elo_rating' => 'integer',
        'elo_change' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function match(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }
}
