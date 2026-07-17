<?php

namespace App\Concerns;

trait RightValidationRules
{
    protected function rightRules(): array
    {
        return [
            'name' => $this->rightNameRules('required'),
            'description' => $this->rightDescriptionRules('nullable'),
        ];
    }

    protected function rightIdRules(...$rules): array
    {
        return ['integer', 'exists:rights,id', ...$rules];
    }

    protected function rightNameRules(...$rules): array
    {
        return ['string', 'max:255', ...$rules];
    }

    protected function rightDescriptionRules(...$rules): array
    {
        return ['string', 'max:255', ...$rules];
    }
}
