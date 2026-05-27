<?php

namespace Database\Factories;

use App\Models\HolidaySurcharge;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<HolidaySurcharge>
 */
class HolidaySurchargeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true),
            'reason' => fake()->sentence(),
            'start_date' => now()->addDay(),
            'end_date' => now()->addDays(3),
            'global_surcharge_amount' => 50000,
            'is_active' => true,
            'priority' => fake()->numberBetween(1, 100),
        ];
    }
}
