<?php

namespace Database\Factories;

use App\Models\Bus;
use App\Models\Route;
use App\Models\Trip;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Trip>
 */
class TripFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'bus_id' => Bus::factory(),
            'route_id' => Route::factory(),
            'start_time' => '08:00:00',
            'end_time' => '12:00:00',
            'price' => 250000,
            'is_active' => true,
            'priority' => fake()->numberBetween(1, 100),
        ];
    }
}
