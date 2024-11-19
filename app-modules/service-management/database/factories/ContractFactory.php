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
        $startDate = fake()->dateTimeBetween('-1 year', '+1 year');
        $endDate = fake()->dateTimeBetween((clone $startDate)->modify('+1 day'), (clone $startDate)->modify('+1 year'));

        return [
            'name' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'contract_type' => ContractType::factory(),
            'vendor_name' => fake()->name(),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'contract_value' => Money::parseByDecimal(fake()->randomNumber(), config('money.defaultCurrency')),
        ];
    }
}
