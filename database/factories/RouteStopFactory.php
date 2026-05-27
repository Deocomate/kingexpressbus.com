<?php

namespace Database\Factories;

use App\Models\Route;
use App\Models\RouteStop;
use App\Models\Stop;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RouteStop>
 */
class RouteStopFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'route_id' => Route::factory(),
            'stop_id' => Stop::factory(),
            'stop_type' => fake()->randomElement(['pickup', 'dropoff', 'both']),
            'priority' => fake()->numberBetween(1, 100),
        ];
    }
}
