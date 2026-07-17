<?php

namespace App\Http\Requests\Administration;

use App\Concerns\UserGroupValidationRules;
use Illuminate\Foundation\Http\FormRequest;

class UserGroupRequest extends FormRequest
{
    use UserGroupValidationRules;
    public function rules(): array
    {
        return $this->userGroupOwnRules();
    }

    public function authorize(): bool
    {
        return $this->user()->isAdministrator();
    }
}
