<?php

namespace App\DTOs;

class PlayerSearchDTO{
    public ?string $firstname = null{
        get => $this->firstname;
        set => $value !== null ? trim($value) : null;
    }

    public ?string $lastname = null{
        get => $this->lastname;
        set => $value !== null ? trim($value) : null;
    }

    public ?int $year_of_birth = null{
        get => $this->year_of_birth;
        set => $value;
    }

    public array $clubIds = []{
        get => $this->clubIds;
        set => $value;
    }

    public array $positionIds = []{
        get => $this->positionIds;
        set => $value;
    }

    public function __construct(array $array){
        $this->firstname = $array['firstname'] ?? null;
        $this->lastname = $array['lastname'] ?? null;
        $this->year_of_birth = $array['year_of_birth'] ?? null;
        $this->clubIds = $array['club_ids'] ?? [];
        $this->positionIds = $array['position_ids'] ?? [];
    }
}
