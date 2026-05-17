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
            'player_id' => ['required', 'exists:players'],
            'hometeam' => $this->teamNameRules(),
            'awayteam' => $this->teamNameRules(),
            'kickoff' => ['required', 'date'],
        ];
    }

    public function authorize(): bool
    {
        return auth()->check();
    }
}
