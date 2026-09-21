<?php

namespace App\Services;

use App\DTOs\EvaluationSearchDTO;
use App\Enums\EvaluationTypes;
use App\Enums\Permission\GameEvaluationPermissions;
use App\Enums\Permission\PlayerEvaluationPermissions;
use App\Models\GameEvaluation;
use App\Models\PlayerEvaluation;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class EvaluationSearchService
{
    public function searchEvaluations(EvaluationSearchDTO $dto, User $user, array|string $with, array|string $withFor): Collection
    {
        $gameEvaluationQuery = $this->generalConditions(GameEvaluation::query(), $dto);

        $gameEvaluationQuery->when($dto->playerIds, fn($q) => $q->whereIn('player_id', $dto->playerIds));
        $gameEvaluationQuery->whereHas('player', function ($q) use ($dto) {
            $q->when($dto->yearsOfBirth, fn ($q) => $q->whereIn('year_of_birth', $dto->yearsOfBirth))
                ->when($dto->clubIds, fn ($q) => $q->whereIn('club_id', $dto->clubIds));
        });


        if(!$user->hasRight(GameEvaluationPermissions::ViewAll)){
            $gameEvaluationQuery->where('created_by', $user->id);
        }


        $playerEvaluationQuery = $this->generalConditions(PlayerEvaluation::query(), $dto);

        $playerEvaluationQuery->when($dto->playerIds, fn($q) => $q->whereIn('player_id', $dto->playerIds));
        $playerEvaluationQuery->whereHas('player', function ($q) use ($dto) {
            $q->when($dto->yearsOfBirth, fn ($q) => $q->whereIn('year_of_birth', $dto->yearsOfBirth))
                ->when($dto->clubIds, fn ($q) => $q->whereIn('club_id', $dto->clubIds));
        });


        if(!$user->hasRight(PlayerEvaluationPermissions::ViewAll)){
            $playerEvaluationQuery->where('created_by', $user->id);
        }

        $withGameEvaluation = array_merge($with, $withFor[EvaluationTypes::GAME->value]);
        $withPlayerEvaluation = array_merge($with, $withFor[EvaluationTypes::PLAYER->value]);

        return $gameEvaluationQuery->with($withGameEvaluation)->get()
            ->concat($playerEvaluationQuery->with($withPlayerEvaluation)->get()->all())
            ->sortByDesc('created_at')
            ->values();
    }

    private function generalConditions($query, EvaluationSearchDTO $dto): Builder
    {
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

        return $query;
    }
}
