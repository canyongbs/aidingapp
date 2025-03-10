<?php

use AidingApp\Contact\Models\Contact;
use AidingApp\Engagement\Jobs\CreateBatchedEngagement;
use AidingApp\Engagement\Models\Engagement;
use AidingApp\Engagement\Models\EngagementBatch;
use AidingApp\Engagement\Notifications\EngagementNotification;
use Illuminate\Support\Facades\Notification;

use function Pest\Laravel\assertDatabaseCount;

it('will create and send an engagement immediately', function () {
    Notification::fake();

    $engagementBatch = EngagementBatch::factory()->deliverNow()->create();
    $recipient = Contact::factory()->create();

    assertDatabaseCount(Engagement::class, 0);

    dispatch(app(CreateBatchedEngagement::class, [
        'engagementBatch' => $engagementBatch,
        'recipient' => $recipient,
    ]));

    assertDatabaseCount(Engagement::class, 1);

    expect(Engagement::first())
        ->engagementBatch->is($engagementBatch)->toBeTrue()
        ->user->is($engagementBatch->user)->toBeTrue()
        ->recipient->is($recipient)->toBeTrue()
        ->channel->toBe($engagementBatch->channel)
        ->subject->toBe($engagementBatch->subject)
        ->body->toMatchArray($engagementBatch->body)
        ->scheduled_at->toBeNull()
        ->dispatched_at->not->toBeNull();

    Notification::assertSentTo(
        $recipient,
        EngagementNotification::class
    );
});

it('will create but not dispatch a scheduled engagement', function () {
    Notification::fake();

    $engagementBatch = EngagementBatch::factory()->create([
        'scheduled_at' => now()->addMinute(),
    ]);
    $recipient = Contact::factory()->create();

    assertDatabaseCount(Engagement::class, 0);

    dispatch(app(CreateBatchedEngagement::class, [
        'engagementBatch' => $engagementBatch,
        'recipient' => $recipient,
    ]));

    assertDatabaseCount(Engagement::class, 1);

    expect(Engagement::first())
        ->engagementBatch->is($engagementBatch)->toBeTrue()
        ->user->is($engagementBatch->user)->toBeTrue()
        ->recipient->is($recipient)->toBeTrue()
        ->channel->toBe($engagementBatch->channel)
        ->subject->toBe($engagementBatch->subject)
        ->body->toMatchArray($engagementBatch->body)
        ->scheduled_at->startOfSecond()->eq($engagementBatch->scheduled_at->startOfSecond())->toBeTrue()
        ->dispatched_at->toBeNull();

    Notification::assertNotSentTo(
        $recipient,
        EngagementNotification::class
    );
});
