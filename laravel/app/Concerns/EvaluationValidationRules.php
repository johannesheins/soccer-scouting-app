<?php

namespace App\Concerns;

trait EvaluationValidationRules
{
    protected function evaluationRules(): array
    {
        return [
            'date' => ['required', 'date', 'before_or_equal:today'],

            'criteriaScores' => ['required', 'array'],
            'criteriaScores.*.evaluation_criteria_id' => ['required', 'exists:evaluation_criteria,id'],
            'criteriaScores.*.score' => ['required', 'numeric', 'min:0', 'max:10'],

            'strengths' => $this->strengthsRules('nullable'),
            'weaknesses' => $this->weaknessesRules('nullable'),
            'recommendation_id' => $this->recommendationIdRules('required'),
            'comment' => $this->commentRules('nullable'),
        ];
    }

    protected function strengthsRules(...$rules): array
    {
        return ['string', 'max:255', ...$rules];
    }

    protected function weaknessesRules(...$rules): array
    {
        return ['string', 'max:255', ...$rules];
    }

    protected function recommendationIdRules(...$rules): array
    {
        return ['integer', 'exists:recommendations,id', ...$rules];
    }

    protected function commentRules(...$rules): array
    {
        return ['string', 'max:255', ...$rules];
    }
}
