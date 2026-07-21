<?php

namespace App\DTOs;

class EvaluationSearchDTO
{
    public ?array $playerIds = []{
        get => $this->playerIds;
        set => $value;
    }
    public ?array $criteria_scores_from = [] {
        get => $this->criteria_scores_from;
        set => $value;
    }

    public ?array $criteria_scores_to = [] {
        get => $this->criteria_scores_to;
        set => $value;
    }

    public function __construct(array $array)
    {
        $this->playerIds = $array['player_ids'] ?? [];
        $this->criteria_scores_from = $array['criteria_scores_from'] ?? [];
        $this->criteria_scores_to = $array['criteria_scores_to'] ?? [];
    }
}
