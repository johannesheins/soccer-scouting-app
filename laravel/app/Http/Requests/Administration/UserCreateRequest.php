<?php

namespace App\Http\Requests\Administration;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;

class UserCreateRequest extends UserUpdateRequest
{
    use ProfileValidationRules;
    use PasswordValidationRules;
    public function rules(): array
    {
        return parent::rules() + [
            'password' => $this->passwordRules(),
        ];
    }
}
