<?php

namespace App\Http\Requests\Evaluation;

use Illuminate\Foundation\Http\FormRequest;

class EvaluationCreateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'player_id' => 'integer|exists:players,id',
        ];
    }

    public function authorize(): bool
    {
        return auth()->check();
    }
}
