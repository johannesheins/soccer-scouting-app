<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EvaluationCriteriaRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('evaluation_criteria', 'name')->ignore($this->route('evaluation_criterion'))],
            'minimum_player_age' => ['nullable', 'integer'],
            'multiplier' => ['required', 'integer'],
            'evaluation_criteria_group_id' => ['nullable', 'integer', 'exists:evaluation_criteria_groups,id'],
        ];
    }

    public function authorize(): bool
    {
        return auth()->user()->is_administrator;
    }
}
