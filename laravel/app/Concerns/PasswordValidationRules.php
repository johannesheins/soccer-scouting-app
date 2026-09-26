<?php

namespace App\Concerns;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rules\Password;

trait PasswordValidationRules
{
    /**
     * Get the validation rules used to validate passwords.
     *
     * @return array<int, ValidationRule|array<mixed>|string>
     */
    protected function passwordRules(...$rules): array
    {
        return ['required', 'string', Password::default(), 'confirmed', ...$rules];
    }

    /**
     * Get the validation rules used to validate the current password.
     *
     * @return array<int, ValidationRule|array<mixed>|string>
     */
    protected function currentPasswordRules(): array
    {
        return ['required', 'string', 'current_password'];
    }
}
