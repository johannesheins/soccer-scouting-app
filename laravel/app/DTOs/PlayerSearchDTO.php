<?php

namespace App\DTOs;

class PlayerSearchDTO
{
    public ?string $firstname = null {
        get => $this->firstname;
        set => $value !== null ? trim($value) : null;
    }

    public ?string $lastname = null {
        get => $this->lastname;
        set => $value !== null ? trim($value) : null;
    }

    public array $yearsOfBirth = [] {
        get => $this->yearsOfBirth;
        set => $value;
    }

    public ?int $heightFrom = null {
        get => $this->heightFrom;
        set => $value;
    }

    public ?int $heightTo = null {
        get => $this->heightTo;
        set => $value;
    }

    public array $strongFoots = [] {
        get => $this->strongFoots;
        set => $value;
    }

    public array $clubIds = [] {
        get => $this->clubIds;
        set => $value;
    }

    public array $positionIds = [] {
        get => $this->positionIds;
        set => $value;
    }

    public function __construct(array $array)
    {
        $this->firstname = $array['firstname'] ?? null;
        $this->lastname = $array['lastname'] ?? null;
        $this->yearsOfBirth = $array['years_of_birth'] ?? [];
        $this->heightFrom = $array['height_from'] ?? null;
        $this->heightTo = $array['height_to'] ?? null;
        $this->strongFoots = $array['strong_foots'] ?? [];
        $this->clubIds = $array['club_ids'] ?? [];
        $this->positionIds = $array['position_ids'] ?? [];
    }
}
