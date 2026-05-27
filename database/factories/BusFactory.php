<?php

namespace Database\Factories;

use App\Models\Bus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Bus>
 */
class BusFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company().' Bus',
            'model_name' => fake()->randomElement(['Limousine', 'Cabin', 'Sleeper']),
            'seat_count' => 2,
            'seat_map' => [
                ['seat_number' => 'A1', 'status' => 'available', 'deck' => 1],
                ['seat_number' => 'A2', 'status' => 'available', 'deck' => 1],
            ],
            'image_list_url' => [],
            'priority' => fake()->numberBetween(1, 100),
        ];
    }
}
