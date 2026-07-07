<?php

namespace Database\Factories;

use App\Models\EvaluationCriteria;
use Illuminate\Database\Eloquent\Factories\Factory;

class EvaluationCriteriaFactory extends Factory
{
    protected $model = EvaluationCriteria::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->name(),
            'minimum_player_age' => $this->faker->numberBetween(0, 40),
            'multiplier' => $this->faker->numberBetween(1, 5),
        ];
    }
}
