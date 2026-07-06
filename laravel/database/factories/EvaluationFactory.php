<?php

namespace Database\Factories;

use App\Models\Club;
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
            'home_team_id' => Club::factory(),
            'away_team_id' => Club::factory(),
            'kickoff_date' => Carbon::now()->format('Y-m-d'),
            'kickoff_time' => Carbon::now()->format("H:i:s"),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'player_id' => Player::factory(),
            'created_by' => User::factory(),
        ];
    }
}
