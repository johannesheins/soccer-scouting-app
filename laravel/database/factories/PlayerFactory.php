<?php

namespace Database\Factories;

use App\Models\Club;
use App\Models\Player;
use Illuminate\Database\Eloquent\Factories\Factory;

class PlayerFactory extends Factory
{
    protected $model = Player::class;

    public function definition(): array
    {
        return [
            'firstname' => fake()->firstName(),
            'lastname' => fake()->lastName(),
            'month_of_birth' => fake()->month('now'),
            'year_of_birth' => fake()->year('now'),

            'club_id' => Club::factory(),
        ];
    }
}
