<?php

namespace Database\Factories;

use App\Models\BusService;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BusService>
 */
class BusServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['Wi-Fi', 'Water', 'USB', 'Blanket', 'TV']),
            'icon' => fake()->randomElement(['fa-solid fa-wifi', 'fa-solid fa-bottle-water', 'fa-solid fa-plug']),
            'priority' => fake()->numberBetween(1, 100),
        ];
    }
}
