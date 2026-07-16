<?php

namespace App\Concerns;

use App\Enums\FootEnum;
use App\Enums\Request\PlayerRequestNameEnum as Name;

trait PlayerValidationRules
{
    protected function playerRules(): array
    {
        return [
            reqN(Name::firstname) => $this->firstnameRules(),
            reqN(Name::lastname) => $this->lastnameRules(),
            reqN(Name::yearOfBirth) => $this->yearOfBirthRules(),
            reqN(Name::height) => $this->heightRules(),
            reqN(Name::strongFoot) => $this->strongFootRules(),
            reqN(Name::clubId) => $this->clubIdRules(),
            reqN(Name::positionIds) => $this->positionIdRules(),
            reqN(Name::positionIds, '.*') => $this->positionIdsRules()
        ];
    }

    protected function firstnameRules(): array
    {
        return ['string', 'max:255'];
    }

    protected function lastnameRules(): array
    {
        return ['string', 'max:255'];
    }

    protected function yearOfBirthRules(): array
    {
        return ['integer', 'digits:4'];
    }

    protected function heightRules(): array
    {
        return ['integer'];
    }

    public function strongFootRules(): array
    {
        $footCases = implode(',', array_column(FootEnum::cases(), 'value'));
        return ['string', "in:$footCases"];
    }

    public function clubIdRules(): array
    {
        return ['exists:clubs,id'];
    }

    public function positionIdRules(): array
    {
        return ['array'];
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
