<?php

namespace AidingApp\ServiceManagement\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use AidingApp\ServiceManagement\Models\ContractType;

/**
 * @extends ContractType>
 */
class ContractTypeFactory extends Factory
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
            'is_default' => fake()->boolean(),
            'order' => fake()->randomNumber(),
        ];
    }
}
