<?php

namespace Database\Factories;

use App\Models\Menu;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Menu>
 */
class MenuFactory extends Factory
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
            'url' => '/'.fake()->slug(),
            'parent_id' => Menu::ROOT_PARENT_ID,
            'priority' => fake()->numberBetween(1, 100),
            'type' => 'custom_link',
        ];
    }
}
