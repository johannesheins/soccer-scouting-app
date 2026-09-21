<?php

namespace App\Http\Requests\Evaluation;

use App\Concerns\ClubValidationRules;
use App\Concerns\EvaluationValidationRules;
use App\Concerns\PlayerValidationRules;
use Illuminate\Foundation\Http\FormRequest;

class PlayerEvaluationStoreRequest extends EvaluationStoreRequest
{
    use EvaluationValidationRules;
    use PlayerValidationRules;
    use ClubValidationRules;
    public function rules(): array
    {
        return parent::rules() + [
            'player_id' => $this->playerIdRules('required'),
        ];
    }

    public function authorize(): bool
    {
        return auth()->check();
    }
}
