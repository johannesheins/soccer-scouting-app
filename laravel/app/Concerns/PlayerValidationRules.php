<?php

namespace App\Concerns;

use App\Enums\FootEnum;

trait PlayerValidationRules
{
    protected function playerRules(): array
    {
        return [
            'firstname' => $this->firstnameRules(),
            'lastname' => $this->lastnameRules(),
            'year_of_birth' => $this->yearOfBirthRules(),
            'height' => $this->heightRules(),
            'strong_foot' => $this->strongFootRules(),
            'club_id' => $this->clubIdRules(),
            'position_ids' => $this->positionIdRules(),
            'position_ids.*' => $this->positionIdsRules()
        ];
    }

    protected function firstnameRules(): array
    {
        return ['required', 'string', 'max:255'];
    }

    protected function lastnameRules(): array
    {
        return ['required', 'string', 'max:255'];
    }

    protected function yearOfBirthRules(): array
    {
        return ['required', 'string', 'regex:/^\d{4}\/\d{4}$/'];
    }

    protected function heightRules(): array
    {
        return ['required', 'integer'];
    }

    public function strongFootRules(): array
    {
        $footCases = implode(',', array_column(FootEnum::cases(), 'value'));
        return ['required', 'string', "in:$footCases"];
    }

    public function clubIdRules(): array
    {
        return ['required', 'exists:clubs,id'];
    }

    public function positionIdRules(): array
    {
        return ['required', 'array'];
    }

    protected function positionIdsRules(): array
    {
        return ['integer', 'exists:positions,id'];
    }

    protected function playerId($required = true): array
    {
        $rules = ['integer', 'exists:players,id'];
        if($required) {
            $rules[] = 'required';
        }
        return $rules;
    }
}
