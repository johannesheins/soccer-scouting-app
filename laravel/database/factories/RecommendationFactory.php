<?php

namespace Database\Factories;

use App\Models\Recommendation;
use Illuminate\Database\Eloquent\Factories\Factory;

class RecommendationFactory extends Factory
{
    protected $model = Recommendation::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
        ];
    }
}
