<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EvaluationCriteriaGroupRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('evaluation_criteria_groups')->ignore($this->route('evaluation_criteria_group'))],
        ];
    }

    public function authorize(): bool
    {
        return auth()->user()->is_administrator;
    }
}
