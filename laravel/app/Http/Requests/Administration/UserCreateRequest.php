<?php

namespace App\Http\Requests\Administration;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Concerns\UserGroupValidationRules;
use Illuminate\Foundation\Http\FormRequest;

class UserCreateRequest extends FormRequest
{
    use ProfileValidationRules;
    use PasswordValidationRules;
    use UserGroupValidationRules;
    public function rules(): array
    {
        return [
            'password' => $this->passwordRules(),
        ] + $this->profileRules() + $this->userGroupRules();
    }

    public function authorize(): bool
    {
        return $this->user()->isAdministrator();
    }
}
