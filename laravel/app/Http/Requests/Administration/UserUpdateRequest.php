<?php

namespace App\Http\Requests\Administration;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Concerns\UserGroupValidationRules;
use Illuminate\Foundation\Http\FormRequest;

class UserUpdateRequest extends FormRequest
{
    use ProfileValidationRules;
    use USerGroupValidationRules;
    public function rules(): array
    {
        return $this->profileRules($this->route('user')->id) + $this->userGroupRules();
    }

    public function authorize(): bool
    {
        return $this->user()->isAdministrator();
    }
}
