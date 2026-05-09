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

    public ?int $age = null{
        get => $this->age;
        set => $value;
    }

    public array $clubs = []{
        get => $this->clubs;
        set => $value;
    }

    public array $positions = []{
        get => $this->positions;
        set => $value;
    }

    public function __construct(array $array){
        $this->firstname = $array['firstname'] ?? null;
        $this->lastname = $array['lastname'] ?? null;
        $this->age = $array['age'] ?? null;
        $this->clubs = $array['club_ids'] ?? [];
        $this->positions = $array['position_ids'] ?? [];
    }
}
