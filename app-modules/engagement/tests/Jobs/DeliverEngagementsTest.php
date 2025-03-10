<?php

use AidingApp\Engagement\Jobs\DeliverEngagements;
use AidingApp\Engagement\Models\Engagement;
use AidingApp\Engagement\Notifications\EngagementNotification;
use Illuminate\Support\Facades\Notification;

it('will send engagements that have been scheduled for a past date and have not been dispatched', function () {
    Notification::fake();

    $engagement = Engagement::factory()->create([
        'scheduled_at' => now()->subMinute(),
    ]);

    dispatch(app(DeliverEngagements::class));

    Notification::assertSentTo(
        $engagement->recipient,
        EngagementNotification::class
    );
});

it('will send engagements that have not been scheduled but have not been dispatched', function () {
    Notification::fake();

    $engagement = Engagement::factory()->deliverNow()->create();

    dispatch(app(DeliverEngagements::class));

    Notification::assertSentTo(
        $engagement->recipient,
        EngagementNotification::class
    );
});

it('will not send engagements that have been scheduled for a future date', function () {
    Notification::fake();

    $engagement = Engagement::factory()->create([
        'scheduled_at' => now()->addMinute(),
    ]);

    dispatch(app(DeliverEngagements::class));

    Notification::assertNotSentTo(
        $engagement->recipient,
        EngagementNotification::class
    );
});

it('will not send engagements that have already been dispatched', function () {
    Notification::fake();

    $engagement = Engagement::factory()->create([
        'scheduled_at' => now()->subMinute(),
        'dispatched_at' => now(),
    ]);

    dispatch(app(DeliverEngagements::class));

    Notification::assertNotSentTo(
        $engagement->recipient,
        EngagementNotification::class
    );
});
