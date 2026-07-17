<?php

namespace App\Concerns;

use Illuminate\Validation\Rule;

trait EvaluationCriteriaGroupRules
{
    protected function evaluationCriteriaGroupRules(): array
    {
        $uniqueName = Rule::unique('evaluation_criteria_groups')->ignore($this->route('evaluation_criteria_group'));
        return [
            'name' => $this->evaluationCriteriaGroupNameRules('required', $uniqueName)
        ];
    }

    protected function evaluationCriteriaGroupIdRules(...$rules): array
    {
        return ['integer', 'exists:evaluation_criteria_groups,id', ...$rules];
    }

    protected function evaluationCriteriaGroupNameRules(...$rules): array
    {
        return ['string', 'max:255', ...$rules];
    }
}
