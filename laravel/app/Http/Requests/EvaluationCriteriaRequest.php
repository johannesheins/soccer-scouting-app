<?php

namespace App\Http\Requests;

use App\Concerns\EvaluationCriteriaRules;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EvaluationCriteriaRequest extends FormRequest
{
    use EvaluationCriteriaRules;
    public function rules(): array
    {
        return $this->evaluationCriteriaRules();
    }

    public function authorize(): bool
    {
        return auth()->user()->is_administrator;
    }
}
