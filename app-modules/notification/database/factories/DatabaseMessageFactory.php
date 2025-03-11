<?php

namespace AidingApp\Notification\Database\Factories;

use AidingApp\Notification\Models\DatabaseMessage;
use AidingApp\Notification\Tests\Fixtures\TestDatabaseNotification;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DatabaseMessage>
 */
class DatabaseMessageFactory extends Factory
{
    public function definition(): array
    {
        return [
            'notification_class' => TestDatabaseNotification::class,
        ];
    }
}
