<?php

namespace App\Concerns;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rules\Password;

trait ClubValidationRules
{
    protected function clubIdRules(...$rules): array
    {
        return ['integer', 'exists:clubs,id', ...$rules];
    }

    protected function clubNameRules(...$rules): array
    {
        return ['required', 'string', 'max:255', ...$rules];
    }
}
