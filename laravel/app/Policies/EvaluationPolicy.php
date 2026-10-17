<?php

namespace App\Policies;

use App\Enums\Permission\EvaluationPermissions;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class EvaluationPolicy
{
    use HandlesAuthorization;

    public function search(User $user): bool
    {
        return $user->hasRight(EvaluationPermissions::Search);
    }
}
