<?php

namespace App\Http\Requests\Settings;

use App\Concerns\ClubValidationRules;
use App\Concerns\PlayerValidationRules;
use Illuminate\Foundation\Http\FormRequest;

class PlayerQuickSearchRequest extends FormRequest
{
    use ClubValidationRules;
    use PlayerValidationRules;

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'club_ids' => ['array', 'max:3'],
            'club_ids.*' => $this->clubIdRules(),
            'years_of_birth' => ['array', 'max:6'],
            'years_of_birth.*' => $this->yearOfBirthRules(),
        ];
    }
}
