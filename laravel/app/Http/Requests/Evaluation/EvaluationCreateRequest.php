<?php

namespace App\Http\Requests\Evaluation;

use App\Concerns\PlayerValidationRules;
use App\Enums\Request\PlayerRequestNameEnum as Name;
use Illuminate\Foundation\Http\FormRequest;

class EvaluationCreateRequest extends FormRequest
{
    use PlayerValidationRules;
    public function rules(): array
    {
        return [
            reqN(Name::playerId) => $this->playerIdRules(),
        ];
    }

    public function authorize(): bool
    {
        return auth()->check();
    }
}
