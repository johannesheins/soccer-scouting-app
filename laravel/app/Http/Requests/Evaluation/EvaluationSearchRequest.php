<?php

namespace App\Http\Requests\Evaluation;

use App\Concerns\PlayerValidationRules;
use Illuminate\Foundation\Http\FormRequest;

class EvaluationSearchRequest extends FormRequest
{
    use PlayerValidationRules;
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

            'criteria_scores_from' => ['nullable', 'array'],
            'criteria_scores_from.*' => ['nullable', 'integer'],
            'criteria_scores_to' => ['nullable', 'array'],
            'criteria_scores_to.*' => ['nullable', 'integer'],

            'player_ids' => ['nullable', 'array'],
            'player_ids.*' => $this->playerIdRules('nullable'),
            'years_of_birth' => ['nullable', 'array'],
            'years_of_birth.*' => $this->yearOfBirthRules('nullable'),
            'club_ids' => ['nullable', 'array'],
            'club_ids.*' => $this->clubIdRules('nullable'),

            'open_tab' => ['nullable', 'string'],

            'open_accordion' => ['nullable', 'array'],
            'open_accordion.*' => ['nullable', 'boolean'],
        ];
    }

    public function authorize(): bool
    {
        return auth()->check();
    }
}
