<?php

namespace App\Concerns;

use App\Enums\FootEnum;
use App\Enums\Request\PlayerRequestNameEnum as Name;

trait PlayerValidationRules
{
    use ClubValidationRules;
    protected function playerRules(): array
    {
        return [
            reqN(Name::firstname) => $this->firstnameRules(),
            reqN(Name::lastname) => $this->lastnameRules(),
            reqN(Name::yearOfBirth) => $this->yearOfBirthRules(),
            reqN(Name::height) => $this->heightRules(),
            reqN(Name::strongFoot) => $this->strongFootRules(),
            reqN(Name::clubId) => $this->clubIdRules(),
            reqN(Name::positionIds) => ['array'],
            reqN(Name::positionIds, '.*') => $this->positionIdRules()
        ];
    }

    protected function firstnameRules(...$rules): array
    {
        return ['string', 'max:255', ...$rules];
    }

    protected function lastnameRules(...$rules): array
    {
        return ['string', 'max:255', ...$rules];
    }

    protected function yearOfBirthRules(...$rules): array
    {
        return ['integer', 'digits:4', ...$rules];
    }

    protected function heightRules(...$rules): array
    {
        return ['integer', ...$rules];
    }

    public function strongFootRules(...$rules): array
    {
        $footCases = implode(',', array_column(FootEnum::cases(), 'value'));
        return ['string', "in:$footCases", ...$rules];
    }

    protected function positionIdRules(...$rules): array
    {
        return ['integer', 'exists:positions,id', ...$rules];
    }

    protected function playerIdRules(...$rules): array
    {
        return ['integer', 'exists:players,id', ...$rules];
    }
}
