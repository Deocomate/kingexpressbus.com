<?php

namespace Database\Factories;

use App\Models\HolidaySurcharge;
use App\Models\HolidaySurchargeRoute;
use App\Models\Route;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<HolidaySurchargeRoute>
 */
class HolidaySurchargeRouteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'holiday_surcharge_id' => HolidaySurcharge::factory(),
            'route_id' => Route::factory(),
            'route_surcharge_amount' => 75000,
        ];
    }
}
