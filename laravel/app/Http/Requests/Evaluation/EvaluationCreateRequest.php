<?php

namespace App\Http\Requests\Evaluation;

use App\Concerns\PlayerValidationRules;
use Illuminate\Foundation\Http\FormRequest;

class EvaluationCreateRequest extends FormRequest
{
    use PlayerValidationRules;
    public function rules(): array
    {
        return [
            'player_id' => $this->playerId(false)
        ];
    }

    public function authorize(): bool
    {
        return auth()->check();
    }
}
