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
            'hometeam' => ['required', 'exists:teams,id'],
            'awayteam' => ['required', 'exists:teams,id'],
            'comment' => ['required', 'string', 'max:65535'],
            'kickoff' => ['required', 'date'],
        ];
    }

    public function authorize(): bool
    {
        return auth()->check();
    }
}
