<?php

namespace Database\Factories;

use App\Models\Club;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClubFactory extends Factory
{
    protected $model = Club::class;

    public function definition(): array
    {
        return [
            'clubname' => $this->faker->word(),
            'zip_code' => $this->faker->postcode(),
            'city' => $this->faker->city(),
        ];
    }
}
