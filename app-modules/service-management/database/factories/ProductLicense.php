<?php

namespace AidingApp\ServiceManagement\Database\Factories;

use AidingApp\Contact\Models\Contact;
use AidingApp\ServiceManagement\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use AidingApp\ServiceManagement\Models\ProductLicense;

/**
 * @extends Factory<ProductLicense>
 */
class ProductLicenseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'license' => encrypt(fake()->sentence()),
            'product_id' => Product::factory(),
            'assigned_to' => Contact::factory(),
            'start_date' => fake()->dateTimeBetween('-1 year', '+1 year'),
            'end_date' => fn (array $attributes) => fake()->dateTimeBetween(
                (clone $attributes['start_date'])->modify('+1 day'),
                (clone $attributes['start_date'])->modify('+1 year')
            ),
        ];
    }
}
