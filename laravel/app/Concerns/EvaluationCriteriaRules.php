<?php

namespace App\Concerns;

use Illuminate\Validation\Rule;

trait EvaluationCriteriaRules
{
    use EvaluationCriteriaGroupRules;
    protected function evaluationCriteriaRules(): array
    {
        return [
            'name' => $this->evaluationCriteriaNameRules('required', Rule::unique('evaluation_criteria', 'name')->ignore($this->route('evaluation_criterion'))),
            'minimum_player_age' => $this->evaluationCriteriaMinimumPlayerAgeRules('nullable'),
            'evaluation_criteria_group_id' => $this->evaluationCriteriaGroupIdRules('nullable'),
        ];
    }

    protected function evaluationCriteriaNameRules(...$rules): array
    {
        return ['string', 'max:255', ...$rules];
    }

    protected function evaluationCriteriaMinimumPlayerAgeRules(...$rules): array
    {
        return ['integer', ...$rules];
    }
}
