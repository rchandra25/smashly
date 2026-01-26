<?php

namespace App\Policies;

use App\Models\Game;
use App\Models\User;

class GamePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Game $game): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function viewPending(User $user): bool
    {
        return $user->isOrganizer();
    }

    public function approve(User $user, Game $game): bool
    {
        return $user->isOrganizer() && $game->isPending();
    }

    public function reject(User $user, Game $game): bool
    {
        return $user->isOrganizer() && $game->isPending();
    }

    public function dispute(User $user, Game $game): bool
    {
        return $game->involvesPlayer($user) && $game->isPending();
    }
}
