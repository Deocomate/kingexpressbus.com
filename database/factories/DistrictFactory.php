<?php

namespace Database\Factories;

use App\Models\District;
use App\Models\DistrictType;
use App\Models\Province;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<District>
 */
class DistrictFactory extends Factory
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
            'province_id' => Province::factory(),
            'district_type_id' => DistrictType::factory(),
            'name' => $name,
            'slug' => str($name)->slug()->toString(),
            'title' => $name,
            'description' => fake()->sentence(),
            'priority' => fake()->numberBetween(1, 100),
        ];
    }
}
