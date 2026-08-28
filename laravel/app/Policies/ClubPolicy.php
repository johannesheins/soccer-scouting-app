<?php

namespace App\Policies;

use App\Enums\RightEnum;
use App\Models\Club;
use App\Models\User;

class ClubPolicy
{
    public function index(User $user): bool
    {
        return $user->hasRight(RightEnum::ClubIndex);
    }

    public function search(User $user): bool
    {
        return $user->hasRight(RightEnum::ClubSearch);
    }

    public function view(User $user): bool
    {
        return $user->hasRight(RightEnum::ClubView);
    }

    public function create(User $user): bool
    {
        return $user->hasRight(RightEnum::ClubCreate);
    }

    public function update(User $user, Club $club): bool
    {
        return $user->hasRight(RightEnum::ClubEdit);
    }

    public function delete(User $user, Club $club): bool
    {
        return $user->hasRight(RightEnum::ClubDestroy);
    }
}
