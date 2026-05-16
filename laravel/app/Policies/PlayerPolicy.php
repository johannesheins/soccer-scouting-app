<?php

namespace App\Policies;

use App\Enums\RightEnum;
use App\Models\Player;
use App\Models\User;

class PlayerPolicy
{
    public function index(User $user): bool
    {
        return $user->hasRight(RightEnum::PlayerIndex);
    }

    public function search(User $user): bool
    {
        return $user->hasRight(RightEnum::PlayerIndex);
    }

    public function view(User $user): bool
    {
        return $user->hasRight(RightEnum::PlayerView);
    }

    public function create(User $user): bool
    {
        return $user->hasRight(RightEnum::PlayerCreate);
    }

    public function update(User $user, Player $player): bool
    {
        return $user->hasRight(RightEnum::PlayerEdit);
    }

    public function delete(User $user, Player $player): bool
    {
        return $user->hasRight(RightEnum::PlayerDestroy);
    }
}
