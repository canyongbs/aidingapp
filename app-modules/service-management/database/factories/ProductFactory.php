<?php

namespace AidingApp\ServiceManagement\Database\Factories;

use AidingApp\ServiceManagement\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->word(),
            'url' => fake()->url(),
            'description' => fake()->paragraph(),
            'version' => fake()->numerify('#.#.#'),
            'additional_notes' => fake()->paragraph(),
        ];
    }
}
