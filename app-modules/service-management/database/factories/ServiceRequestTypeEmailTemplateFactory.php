<?php

namespace AidingApp\ServiceManagement\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\AidingApp\ServiceManagement\Models\Model>
 */
class ServiceRequestTypeEmailTemplateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'service_request_type_id' => null,
            'type' => $this->faker->randomElement(['created', 'assigned', 'update', 'status-change', 'closed', 'survey-response']),
            'subject' => $this->faker->sentence(),
            'body' => $this->faker->paragraph(),
        ];
    }
}
