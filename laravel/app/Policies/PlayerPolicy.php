<?php

namespace App\Policies;

use App\Enums\Permission\PlayerPermissions;
use App\Models\Player;
use App\Models\User;

class PlayerPolicy
{
    public function index(User $user): bool
    {
        return $user->hasRight(PlayerPermissions::Index);
    }

    public function search(User $user): bool
    {
        return $user->hasRight(PlayerPermissions::Index);
    }

    public function view(User $user): bool
    {
        return $user->hasRight(PlayerPermissions::View);
    }

    public function create(User $user): bool
    {
        return $user->hasRight(PlayerPermissions::Create);
    }

    public function update(User $user, Player $player): bool
    {
        return $user->hasRight(PlayerPermissions::Edit);
    }

    public function delete(User $user, Player $player): bool
    {
        return $user->hasRight(PlayerPermissions::Destroy);
    }
}
