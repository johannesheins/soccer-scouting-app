<?php

namespace Database\Factories;

use App\Models\Evaluation;
use App\Models\Player;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class EvaluationFactory extends Factory
{
    protected $model = Evaluation::class;

    public function definition(): array
    {
        return [
            'hometeam' => $this->faker->word(),
            'awayteam' => $this->faker->word(),
            'kickoff' => Carbon::now(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'player_id' => Player::factory(),
            'user_id' => User::factory(),
        ];
    }
}
