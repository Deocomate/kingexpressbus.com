<?php

namespace Database\Factories;

use App\Models\DistrictType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DistrictType>
 */
class DistrictTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'priority' => fake()->numberBetween(1, 100),
        ];
    }
}
