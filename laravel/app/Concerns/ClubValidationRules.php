<?php

namespace App\Concerns;

use App\Enums\Request\ClubRequestNameEnum as Name;

trait ClubValidationRules
{
    protected function clubRules(): array
    {
        return [
            reqN(Name::clubname) => $this->clubNameRules(),
            reqN(Name::zipCode) => $this->zipCodeRules(),
            reqN(Name::city) => $this->cityRules(),
        ];
    }

    protected function clubIdRules(...$rules): array
    {
        return ['integer', 'exists:clubs,id', ...$rules];
    }

    protected function clubNameRules(...$rules): array
    {
        return ['required', 'string', 'max:255', ...$rules];
    }

    protected function zipCodeRules(...$rules): array
    {
        return ['nullable', 'string', 'max:255', ...$rules];
    }

    protected function cityRules(...$rules): array
    {
        return ['nullable', 'string', 'max:255', ...$rules];
    }
}
