<?php

namespace Database\Factories;

use App\Models\Club;
use App\Models\GameEvaluation;
use App\Models\Player;

class GameEvaluationFactory extends EvaluationFactory
{
    protected $model = GameEvaluation::class;

    public function definition(): array
    {
        return parent::definition() + [
            'player_id' => Player::factory(),
            'home_team_id' => Club::factory(),
            'guest_team_id' => Club::factory(),
        ];
    }
}
