<?php

namespace App\Http\Requests\Evaluation;

use Illuminate\Foundation\Http\FormRequest;

class EvaluationSearchRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $query = $this->route('query');
        if($query === null){
            return;
        }
        $encoded = json_decode(base64_decode($query), true) ?? [];

        $this->merge($encoded);
    }

    public function rules(): array
    {
        return [
            'player_ids' => ['nullable', 'array'],
            'player_ids.*' => ['nullable', 'integer', 'exists:players,id'],

            'criteria_scores_from' => ['nullable', 'array'],
            'criteria_scores_from.*' => ['nullable', 'integer'],

            'criteria_scores_to' => ['nullable', 'array'],
            'criteria_scores_to.*' => ['nullable', 'integer'],

            'open_accordion' => ['nullable', 'array'],
            'open_accordion.*' => ['nullable', 'boolean'],
        ];
    }

    public function authorize(): bool
    {
        return auth()->check();
    }
}
