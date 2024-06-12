<?php

namespace AidingApp\Contact\Database\Factories;

use App\Models\User;
use AidingApp\Contact\Models\OrganizationType;
use AidingApp\Contact\Models\OrganizationIndustry;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\AidingApp\Contact\Models\Organization>
 */
class OrganizationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'email' => fake()->companyEmail(),
            'phone_number' => fake()->phoneNumber(),
            'website' => fake()->url(),
            'industry_id' => OrganizationIndustry::factory(),
            'type_id' => OrganizationType::factory(),
            'description' => fake()->text(),
            'number_of_employees' => fake()->randomNumber(),
            'address' => fake()->address(),
            'city' => fake()->city(),
            'state' => fake()->state(),
            'postalcode' => fake()->postcode(),
            'country' => fake()->country(),
            'linkedin_url' => fake()->url(),
            'facebook_url' => fake()->url(),
            'twitter_url' => fake()->url(),
            'created_by_id' => User::factory(),
        ];
    }
}
