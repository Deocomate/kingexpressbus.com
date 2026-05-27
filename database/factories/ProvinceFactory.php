<?php

namespace Database\Factories;

use App\Models\Province;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Province>
 */
class ProvinceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->city().' '.fake()->unique()->bothify('####');

        return [
            'name' => $name,
            'slug' => str($name)->slug()->toString(),
            'title' => $name,
            'description' => fake()->sentence(),
            'priority' => fake()->numberBetween(1, 100),
        ];
    }
}
