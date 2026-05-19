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
//            'player_id' => ['required', 'exists:players'],
//            'hometeam' => ['required', 'exists:teams,id'],
//            'awayteam' => ['required', 'exists:teams,id'],
//            'comment' => ['required', 'string', 'max:65535'],
//            'kickoff' => ['required', 'date'],
            'criteriaScores' => ['required', 'array'],
            'criteriaScores.*.evaluation_criteria_id' => ['required', 'exists:evaluation_criteria,id'],
            'criteriaScores.*.score' => ['required', 'min:0', 'max:10'],
        ];
    }

    public function authorize(): bool
    {
        return auth()->check();
    }
}
