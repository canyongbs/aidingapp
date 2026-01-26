<?php

namespace AidingApp\Notification\Database\Factories;

use AidingApp\Notification\Enums\EmailMessageEventType;
use AidingApp\Notification\Models\EmailMessageEvent;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EmailMessageEvent>
 */
class EmailMessageEventFactory extends Factory
{
    public function definition(): array
    {
        return [
            'type' => $this->faker->randomElement(EmailMessageEventType::cases()),
            'payload' => [],
            'occurred_at' => now(),
        ];
    }
}
