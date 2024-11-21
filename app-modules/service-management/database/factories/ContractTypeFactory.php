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
            'is_default' => false,
            'order' => null,
        ];
    }

    public function default(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'is_default' => true,
            ];
        });
    }

    public function configure()
    {
        return $this->afterCreating(function ($model) {
            $maxOrder = static::newModel()->max('order') ?? 0;
            $model->update(['order' => $maxOrder + 1]);
        });
    }
}
