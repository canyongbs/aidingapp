<?php

namespace AidingApp\ServiceManagement\Database\Factories;

use AidingApp\Contact\Models\Contact;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestFeedback;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ServiceRequestFeedback>
 */
class ServiceRequestFeedbackFactory extends Factory
{
    public function definition(): array
    {
        return [
            'csat_answer' => $this->faker->numberBetween(1, 5),
            'nps_answer' => $this->faker->numberBetween(1, 5),
            'service_request_id' => ServiceRequest::factory(),
            'contact_id' => Contact::factory(),
        ];
    }
}
