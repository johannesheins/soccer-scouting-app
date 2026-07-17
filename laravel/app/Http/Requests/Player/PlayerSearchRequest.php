<?php

namespace App\Http\Requests\Player;

use App\Concerns\PlayerValidationRules;
use App\Enums\FootEnum;
use Illuminate\Foundation\Http\FormRequest;

class PlayerSearchRequest extends FormRequest
{
    use PlayerValidationRules;
    public function rules(): array
    {
        return [
            'firstname' => $this->firstnameRules('nullable'),
            'lastname' => $this->lastnameRules('nullable'),

            'club_ids' => ['nullable', 'array'],
            'club_ids.*' => $this->clubIdRules('nullable'),

            'years_of_birth' => ['nullable', 'array'],
            'years_of_birth.*' => $this->yearOfBirthRules('nullable'),

            'height_from' => $this->heightRules('nullable'),
            'height_to' => $this->heightRules('nullable'),

            'strong_foots' => ['nullable', 'array'],
            'strong_foots.*' => $this->strongFootRules('nullable'),

            'position_ids' => ['nullable', 'array'],
            'position_ids.*' => $this->positionIdRules('nullable'),
        ];
    }

    public function authorize(): bool
    {
        return auth()->check();
    }
}
