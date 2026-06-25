<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EvaluationCriteriaGroupRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:evaluation_criteria_groups,name'],
        ];
    }

    public function authorize(): bool
    {
        return auth()->user()->is_administrator;
    }
}
