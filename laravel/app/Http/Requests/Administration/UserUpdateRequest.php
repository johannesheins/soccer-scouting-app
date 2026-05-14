<?php

namespace App\Http\Requests\Administration;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use Illuminate\Foundation\Http\FormRequest;

class UserUpdateRequest extends FormRequest
{
    use ProfileValidationRules;
    use PasswordValidationRules;
    public function rules(): array
    {

        return [
                'userGroups' => ['nullable', 'array'],
                'userGroups.*' => ['int', 'exists:user_groups,id'],
                'password' => $this->passwordRules(),
            ] + $this->profileRules();
    }

    public function authorize(): bool
    {
        return $this->user()->isAdministrator();
    }
}
