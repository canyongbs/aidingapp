<?php

namespace AidingApp\ContractManagement\Database\Factories;

use AidingApp\ContractManagement\Models\Contract;
use AidingApp\ContractManagement\Models\ContractType;
use Cknow\Money\Money;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Contract>
 */
class ContractFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'contract_type_id' => ContractType::factory(),
            'vendor_name' => fake()->name(),
            'start_date' => fake()->dateTimeBetween('-1 year', '+1 year'),
            'end_date' => fn (array $attributes) => fake()->dateTimeBetween(
                (clone $attributes['start_date'])->modify('+1 day'),
                (clone $attributes['start_date'])->modify('+1 year')
            ),
            'contract_value' => Money::parseByDecimal(fake()->randomNumber(), config('money.defaultCurrency')),
        ];
    }
}
