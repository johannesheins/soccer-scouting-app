<?php

namespace App\Services;

use App\DTOs\EvaluationSearchDTO;
use App\Models\Evaluation;
use Illuminate\Support\Collection;

class EvaluationSearchService
{
    public function searchEvaluations(EvaluationSearchDTO $dto, array|string $with): Collection
    {
        $query = Evaluation::query();

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

        return $query->with($with)->get();
    }
}
