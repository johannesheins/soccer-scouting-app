<?php

namespace App\Http\Requests\Administration;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    use ProfileValidationRules;
    use PasswordValidationRules;
    public function rules(): array
    {
        $this->profileRules();
        $this->passwordRules();
        return [
            'userGroups' => ['nullable', 'array'],
            'userGroups.*' => ['int', 'exists:user_groups,id'],
        ];
    }

    public function authorize(): bool
    {
        return $this->user()->isAdministrator();
    }
}
