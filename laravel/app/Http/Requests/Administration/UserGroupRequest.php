<?php

namespace App\Http\Requests\Administration;

use Illuminate\Foundation\Http\FormRequest;

class UserGroupRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required|string|max:255'],
        ];
    }

    public function authorize(): bool
    {
        return $this->user()->isAdministrator();
    }
}
