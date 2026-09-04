<?php

namespace Database\Factories;

use App\Models\Player;
use App\Models\PlayerEvaluation;

class PlayerEvaluationFactory extends EvaluationFactory
{
    protected $model = PlayerEvaluation::class;

    public function definition(): array
    {
        return parent::definition() + [
            'player_id' => Player::factory(),
        ];
    }
}
