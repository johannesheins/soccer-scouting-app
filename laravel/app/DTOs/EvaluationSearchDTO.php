<?php

namespace App\DTOs;

class EvaluationSearchDTO
{
    public ?array $criteria_scores_from = [] {
        get => $this->criteria_scores_from;
        set => $value;
    }

    public ?array $criteria_scores_to = [] {
        get => $this->criteria_scores_to;
        set => $value;
    }


    public ?array $playerIds = []{
        get => $this->playerIds;
        set => $value;
    }

    public ?array $clubIds = []{
        get => $this->clubIds;
        set => $value;
    }

    public ?array $yearsOfBirth = []{
        get => $this->yearsOfBirth;
        set => $value;
    }

    public function __construct(array $array)
    {
        $this->criteria_scores_from = $array['criteria_scores_from'] ?? [];
        $this->criteria_scores_to = $array['criteria_scores_to'] ?? [];

        $this->playerIds = $array['player_ids'] ?? [];
        $this->clubIds = $array['club_ids'] ?? [];
        $this->yearsOfBirth = $array['years_of_birth'] ?? [];
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
