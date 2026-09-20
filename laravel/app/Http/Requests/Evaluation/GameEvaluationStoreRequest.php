<?php

namespace App\Http\Requests\Evaluation;

use App\Concerns\ClubValidationRules;
use App\Concerns\EvaluationValidationRules;
use App\Concerns\PlayerValidationRules;
use Illuminate\Foundation\Http\FormRequest;

class GameEvaluationStoreRequest extends EvaluationStoreRequest
{
    use EvaluationValidationRules;
    use PlayerValidationRules;
    use ClubValidationRules;
    public function rules(): array
    {
        return parent::rules() + [
            'player_id' => $this->playerIdRules('required'),
            'home_team_id' => $this->clubIdRules('required'),
            'guest_team_id' => $this->clubIdRules('required'),
        ];
    }

    public function authorize(): bool
    {
        return auth()->check();
    }
}
