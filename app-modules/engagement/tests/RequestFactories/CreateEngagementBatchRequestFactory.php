<?php

namespace AidingApp\Engagement\Tests\RequestFactories;

use AidingApp\Engagement\Models\EngagementBatch;
use AidingApp\Notification\Enums\NotificationChannel;
use App\Models\User;
use Worksome\RequestFactories\RequestFactory;

class CreateEngagementBatchRequestFactory extends RequestFactory
{
    public function definition(): array
    {
        return [
            'user' => User::factory()->create(),
            'subject' => fake()->sentence,
            'body' => ['type' => 'doc', 'content' => [['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => fake()->paragraph]]]]],
            'scheduledAt' => fake()->dateTimeBetween('-1 year', '-1 day'),
            'channel' => NotificationChannel::Email,
        ];
    }

    public function deliverNow(): self
    {
        return $this->state([
            'scheduledAt' => null,
        ]);
    }

    public function deliverLater(): self
    {
        return $this->state([
            'scheduledAt' => fake()->dateTimeBetween('+1 day', '+1 week'),
        ]);
    }

    public function ofBatch(): self
    {
        return $this->state([
            'engagement_batch_id' => EngagementBatch::factory(),
        ]);
    }

    public function email(): self
    {
        return $this->state([
            'channel' => NotificationChannel::Email,
        ]);
    }
}
