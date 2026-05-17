<?php

namespace App\Policies;

use App\Enums\RightEnum;
use App\Models\Evaluation;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class EvaluationPolicy
{
    use HandlesAuthorization;

    public function index(User $user): bool
    {
        return $user->hasRight(RightEnum::EvaluationIndex);
    }

    public function search(User $user, Evaluation $evaluation): bool
    {
        return $user->hasRight(RightEnum::EvaluationSearch);
    }

    public function view(User $user, Evaluation $evaluation): bool
    {
        if($user->hasRight(RightEnum::EvaluationViewAll)){
            return true;
        }

        return $user->hasRight(RightEnum::EvaluationView) && $evaluation->user()->is($user);
    }

    public function create(User $user): bool
    {
        return $user->hasRight(RightEnum::EvaluationCreate);
    }

    public function update(User $user, Evaluation $evaluation): bool
    {
        if($user->hasRight(RightEnum::EvaluationEditAll)){
            return true;
        }

        return $user->hasRight(RightEnum::EvaluationEdit) && $evaluation->user()->is($user);
    }

    public function delete(User $user, Evaluation $evaluation): bool
    {
        if($user->hasRight(RightEnum::EvaluationDestroyAll)){
            return true;
        }

        return $user->hasRight(RightEnum::EvaluationDestroy) && $evaluation->user()->is($user);
    }
}
