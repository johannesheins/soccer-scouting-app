<?php

namespace App\Http\Requests;

use App\Concerns\EvaluationCriteriaGroupRules;
use Illuminate\Foundation\Http\FormRequest;

class EvaluationCriteriaGroupRequest extends FormRequest
{
    use EvaluationCriteriaGroupRules;
    public function rules(): array
    {
        return $this->evaluationCriteriaGroupRules();
    }

    public function authorize(): bool
    {
        return auth()->user()->is_administrator;
    }
}
