<?php

namespace App\Concerns;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;

trait UserGroupValidationRules
{
    /**
     * Get the validation rules used to validate user profiles.
     *
     * @return array<string, array<int, ValidationRule|array<mixed>|string>>
     */
    protected function userGroupRules(): array
    {
        return [
            'userGroups' => ['nullable', 'array'],
            'userGroups.*' => ['int', 'exists:user_groups,id'],
        ];
    }
}
