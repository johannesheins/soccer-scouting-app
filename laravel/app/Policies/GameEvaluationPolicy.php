<?php

namespace App\Policies;

use App\Enums\Permission\GameEvaluationPermissions;
use App\Models\Evaluation;
use App\Models\User;

class GameEvaluationPolicy extends EvaluationPolicy
{
    public function view(User $user, Evaluation $evaluation): bool
    {
        if($user->hasRight(GameEvaluationPermissions::ViewAll)){
            return true;
        }

        return $user->hasRight(GameEvaluationPermissions::View) && $evaluation->creator()->is($user);
    }

    public function create(User $user): bool
    {
        return $user->hasRight(GameEvaluationPermissions::Create);
    }

    public function update(User $user, Evaluation $evaluation): bool
    {
        if($user->hasRight(GameEvaluationPermissions::EditAll)){
            return true;
        }

        return $user->hasRight(GameEvaluationPermissions::Edit) && $evaluation->creator()->is($user);
    }

    public function delete(User $user, Evaluation $evaluation): bool
    {
        if($user->hasRight(GameEvaluationPermissions::DestroyAll)){
            return true;
        }

        return $user->hasRight(GameEvaluationPermissions::Destroy) && $evaluation->creator()->is($user);
    }
}
