<?php

namespace App\Http\Requests;

use App\Concerns\TeamValidationRules;
use Illuminate\Foundation\Http\FormRequest;

class EvaluationRequest extends FormRequest
{
    use TeamValidationRules;
    public function rules(): array
    {
        return [
            'player_id' => ['required', 'exists:players,id'],
            'home_team_id' => ['required', 'exists:clubs,id'],
            'away_team_id' => ['required', 'exists:clubs,id'],
            'kickoff_date' => ['required', 'date'],
            'kickoff_time' => ['required', 'date_format:H:i'],
            'strengths' => ['nullable', 'string', 'max:255'],
            'weaknesses' => ['nullable', 'string', 'max:255'],
            'recommendation_id' => ['nullable', 'exists:recommendations,id'],
            'comment' => ['nullable', 'string', 'max:65535'],
            'criteriaScores' => ['required', 'array'],
            'criteriaScores.*.evaluation_criteria_id' => ['required', 'exists:evaluation_criteria,id'],
            'criteriaScores.*.score' => ['required', 'numeric', 'min:0', 'max:10'],
        ];
    }

    public function authorize(): bool
    {
        return auth()->check();
    }
}
