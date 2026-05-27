<?php

namespace Database\Factories;

use App\Models\Province;
use App\Models\Route;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Route>
 */
class RouteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->sentence(3).' '.fake()->unique()->bothify('####');

        return [
            'province_start_id' => Province::factory(),
            'province_end_id' => Province::factory(),
            'name' => $name,
            'slug' => str($name)->slug()->toString(),
            'title' => $name,
            'description' => fake()->sentence(),
            'duration' => fake()->numberBetween(2, 8).' hours',
            'distance_km' => fake()->numberBetween(80, 500),
            'price_default' => fake()->numberBetween(100000, 800000),
            'image_list_url' => [],
            'available_hotel_pickup' => fake()->boolean(),
            'priority' => fake()->numberBetween(1, 100),
        ];
    }
}
