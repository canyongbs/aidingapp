<?php

use AidingApp\Engagement\Jobs\DeliverEngagements;
use AidingApp\Engagement\Models\Engagement;
use AidingApp\Engagement\Notifications\EngagementNotification;
use Illuminate\Support\Facades\Notification;

//it successfully dispatches

//it notifies if response is not 200

it('successfully dispatches', function () {
    Notification::fake();

    $engagement = Engagement::factory()->deliverNow()->create();

    dispatch(app(DeliverEngagements::class));

    Notification::assertSentTo(
        $engagement->recipient,
        EngagementNotification::class
    );
});
