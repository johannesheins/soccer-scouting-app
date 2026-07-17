<?php

namespace App\Http\Requests\Evaluation;

use App\Concerns\EvaluationValidationRules;
use App\Concerns\PlayerValidationRules;
use App\Enums\Request\PlayerRequestNameEnum as Name;
use Illuminate\Foundation\Http\FormRequest;

class EvaluationStoreRequest extends FormRequest
{
    use EvaluationValidationRules;
    public function rules(): array
    {
        return $this->evaluationRules();
    }

    public function authorize(): bool
    {
        return auth()->check();
    }
}
