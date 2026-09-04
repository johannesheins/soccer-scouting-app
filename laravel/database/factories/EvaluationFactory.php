<?php

namespace Database\Factories;

use App\Models\Recommendation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class EvaluationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'date' => Carbon::now(),
            'strengths' => $this->faker->word(),
            'weaknesses' => $this->faker->word(),
            'remarks' => $this->faker->word(),
            'comment' => $this->faker->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'recommendation_id' => Recommendation::factory(),
            'created_by' => User::factory(),
        ];
    }
}
