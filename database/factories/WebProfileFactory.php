<?php

namespace Database\Factories;

use App\Models\WebProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WebProfile>
 */
class WebProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'profile_name' => fake()->company(),
            'is_default' => false,
            'title' => fake()->sentence(3),
            'description' => fake()->sentence(),
            'email' => fake()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'hotline' => fake()->phoneNumber(),
            'address' => fake()->address(),
        ];
    }

    public function default(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_default' => true,
        ]);
    }
}
