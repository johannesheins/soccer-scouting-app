<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EvaluationCriteriaRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'minimum_player_age' => ['nullable', 'integer'],
            'multiplier' => ['required', 'integer'],
        ];
    }

    public function authorize(): bool
    {
        return auth()->user()->is_administrator;
    }
}
