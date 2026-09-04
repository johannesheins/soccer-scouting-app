<?php

namespace App\Policies;

use App\Enums\Permission\ClubPermissions;
use App\Models\Club;
use App\Models\User;

class ClubPolicy
{
    public function index(User $user): bool
    {
        return $user->hasRight(ClubPermissions::Index);
    }

    public function search(User $user): bool
    {
        return $user->hasRight(ClubPermissions::Search);
    }

    public function view(User $user): bool
    {
        return $user->hasRight(ClubPermissions::View);
    }

    public function create(User $user): bool
    {
        return $user->hasRight(ClubPermissions::Create);
    }

    public function update(User $user, Club $club): bool
    {
        return $user->hasRight(ClubPermissions::Edit);
    }

    public function delete(User $user, Club $club): bool
    {
        return $user->hasRight(ClubPermissions::Destroy);
    }
}
