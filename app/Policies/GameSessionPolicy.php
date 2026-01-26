<?php

namespace App\Policies;

use App\Models\GameSession;
use App\Models\User;

class GameSessionPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, GameSession $gameSession): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->isOrganizer();
    }

    public function update(User $user, GameSession $gameSession): bool
    {
        return $user->id === $gameSession->organizer_id;
    }

    public function delete(User $user, GameSession $gameSession): bool
    {
        return $user->id === $gameSession->organizer_id;
    }

    public function join(User $user, GameSession $gameSession): bool
    {
        return !$gameSession->hasPlayer($user)
            && !$gameSession->isFull()
            && $gameSession->isUpcoming();
    }

    public function leave(User $user, GameSession $gameSession): bool
    {
        return $gameSession->hasPlayer($user) && $gameSession->isUpcoming();
    }

    public function updateStatus(User $user, GameSession $gameSession): bool
    {
        return $user->id === $gameSession->organizer_id;
    }
}
