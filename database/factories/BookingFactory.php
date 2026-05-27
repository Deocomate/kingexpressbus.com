<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Stop;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Booking>
 */
class BookingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'booking_code' => strtoupper(fake()->unique()->bothify('KEB####??')),
            'user_id' => User::factory()->customer(),
            'trip_id' => Trip::factory(),
            'booking_date' => now()->addDay()->toDateString(),
            'customer_name' => fake()->name(),
            'customer_email' => fake()->safeEmail(),
            'customer_phone' => fake()->phoneNumber(),
            'pickup_stop_id' => Stop::factory(),
            'dropoff_stop_id' => Stop::factory(),
            'quantity' => 1,
            'total_price' => 250000,
            'status' => 'pending',
            'payment_method' => 'cash_on_pickup',
            'payment_status' => 'unpaid',
            'base_unit_price' => 250000,
            'final_unit_price' => 250000,
        ];
    }
}
