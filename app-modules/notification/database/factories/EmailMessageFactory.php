<?php

namespace AidingApp\Notification\Database\Factories;

use AidingApp\Notification\Models\EmailMessage;
use Illuminate\Database\Eloquent\Factories\Factory;
use TestEmailNotification;

/**
 * @extends Factory<EmailMessage>
 */
class EmailMessageFactory extends Factory
{
    public function definition(): array
    {
        return [
            'content' => 'test',
            'notification_class' => TestEmailNotification::class,
            'quota_usage' => 0,
        ];
    }
}
