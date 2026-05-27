<?php

namespace Database\Factories;

use App\Models\District;
use App\Models\Stop;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Stop>
 */
class StopFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'district_id' => District::factory(),
            'name' => fake()->streetName(),
            'address' => fake()->address(),
            'priority' => fake()->numberBetween(1, 100),
        ];
    }
}
