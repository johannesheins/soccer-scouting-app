<?php

namespace App\Concerns;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;

trait UserGroupValidationRules
{
    use RightValidationRules;

    protected function userGroupOwnRules(): array
    {
        return [
            'name' => $this->userGroupNameRules('required'),
            'rights' => ['nullable', 'array'],
            'rights.*' => $this->rightIdRules(),
        ];
    }

    protected function userGroupNameRules(...$rules): array
    {
        return ['string', 'max:255', ...$rules];
    }

    /**
     * Get the validation rules used to validate user profiles.
     *
     * @return array<string, array<int, ValidationRule|array<mixed>|string>>
     */
    protected function userGroupRules(): array
    {
        return [
            'user_groups' => ['nullable', 'array'],
            'user_groups.*' => ['int', 'exists:user_groups,id'],
        ];
    }
}
