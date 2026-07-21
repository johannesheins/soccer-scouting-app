<?php

namespace App\Services;

use App\DTOs\EvaluationSearchDTO;
use App\Enums\RightEnum;
use App\Models\Evaluation;
use App\Models\User;
use Illuminate\Support\Collection;

class EvaluationSearchService
{
    public function searchEvaluations(EvaluationSearchDTO $dto, User $user, array|string $with): Collection
    {
        $query = Evaluation::query();

        $query->when($dto->playerIds, fn($q) => $q->whereIn('player_id', $dto->playerIds));
        $query->whereHas('player', function ($q) use ($dto) {
            $q->when($dto->yearsOfBirth, fn ($q) => $q->whereIn('year_of_birth', $dto->yearsOfBirth))
                ->when($dto->clubIds, fn ($q) => $q->whereIn('club_id', $dto->clubIds));
        });

        $criteria = array_merge(
            array_keys($dto->criteria_scores_from),
            array_keys($dto->criteria_scores_to)
        );

        foreach ($criteria as $criterion){
            $query->whereHas('criteriaScores', function ($q) use ($dto, $criterion){
                $q->where('evaluation_criteria_id', $criterion);

                $from = $dto->criteria_scores_from[$criterion] ?? null;
                if($from !== null) {
                    $q->where('score', '>=', $from);
                }

                $to = $dto->criteria_scores_to[$criterion] ?? null;
                if($to !== null){
                    $q->where('score', '<=', $to);
                }
            });
        }

        if(!$user->hasRight(RightEnum::EvaluationViewAll)){
            $query->where('created_by', $user->id);
        }

        return $query->with($with)->get();
    }
}
