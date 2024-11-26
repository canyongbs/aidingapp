<?php

namespace AidingApp\ContractManagement\Database\Factories;

use AidingApp\ContractManagement\Models\ContractType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends ContractType>
 */
class ContractTypeFactory extends Factory
{
    private int $maxOrder;

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
            'order' => $this->getNewOrder(),
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

    public function getNewOrder(): int
    {
        return $this->maxOrder = $this->getMaxOrder() + 1;
    }

    public function getMaxOrder(): int
    {
        return $this->maxOrder ??= ContractType::max('order') ?? 0;
    }
}
