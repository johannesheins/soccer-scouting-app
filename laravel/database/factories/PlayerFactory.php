<?php

namespace Database\Factories;

use App\Enums\FootEnum;
use App\Models\Club;
use App\Models\Player;
use Illuminate\Database\Eloquent\Factories\Factory;

class PlayerFactory extends Factory
{
    protected $model = Player::class;

    public function definition(): array
    {
        $y1 = $this->faker->year('last');
        $y2 = $this->faker->year('now');
        return [
            'firstname' => fake()->firstName(),
            'lastname' => fake()->lastName(),
            'year_of_birth' => "{$y1}/{$y2}",
            'height' => fake()->numberBetween(160, 200),
            'strong_foot' => fake()->randomElement(FootEnum::cases())->value,

            'club_id' => Club::factory(),
        ];
    }
}
