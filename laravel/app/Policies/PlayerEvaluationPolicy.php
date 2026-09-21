<?php

namespace App\Policies;

use App\Enums\Permission\PlayerEvaluationPermissions;
use App\Models\Evaluation;
use App\Models\User;

class PlayerEvaluationPolicy extends EvaluationPolicy
{
    public function view(User $user, Evaluation $evaluation): bool
    {
        if($user->hasRight(PlayerEvaluationPermissions::ViewAll)){
            return true;
        }

        return $user->hasRight(PlayerEvaluationPermissions::View) && $evaluation->creator()->is($user);
    }

    public function create(User $user): bool
    {
        return $user->hasRight(PlayerEvaluationPermissions::Create);
    }

    public function update(User $user, Evaluation $evaluation): bool
    {
        if($user->hasRight(PlayerEvaluationPermissions::EditAll)){
            return true;
        }

        return $user->hasRight(PlayerEvaluationPermissions::Edit) && $evaluation->creator()->is($user);
    }

    public function delete(User $user, Evaluation $evaluation): bool
    {
        if($user->hasRight(PlayerEvaluationPermissions::DestroyAll)){
            return true;
        }

        return $user->hasRight(PlayerEvaluationPermissions::Destroy) && $evaluation->creator()->is($user);
    }
}
