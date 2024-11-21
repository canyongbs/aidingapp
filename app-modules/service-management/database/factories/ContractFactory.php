<?php

namespace AidingApp\ServiceManagement\Database\Factories;

use Cknow\Money\Money;
use AidingApp\ServiceManagement\Models\Contract;
use Illuminate\Database\Eloquent\Factories\Factory;
use AidingApp\ServiceManagement\Models\ContractType;

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
