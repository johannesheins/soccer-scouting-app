<?php

namespace App\DTOs;

class ClubSearchDTO
{
    public ?string $clubname = null {
        get => $this->clubname;
        set => $value !== null ? trim($value) : null;
    }

    public ?string $zipCode = null {
        get => $this->zipCode;
        set => $value !== null ? trim($value) : null;
    }

    public ?string $city = null {
        get => $this->city;
        set => $value !== null ? trim($value) : null;
    }

    public function __construct(array $array)
    {
        $this->clubname = $array['clubname'] ?? null;
        $this->zipCode = $array['zip_code'] ?? null;
        $this->city = $array['city'] ?? null;
    }
}
