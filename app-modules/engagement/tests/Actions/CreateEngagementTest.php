<?php

use AidingApp\Engagement\Actions\CreateEngagement;
use AidingApp\Engagement\DataTransferObjects\EngagementCreationData;
use AidingApp\Engagement\Models\Engagement;
use AidingApp\Engagement\Notifications\EngagementNotification;
use AidingApp\Engagement\Tests\RequestFactories\CreateEngagementRequestFactory;
use Illuminate\Support\Facades\Notification;

use function Pest\Laravel\assertDatabaseCount;

it('will create and send an engagement immediately', function () {
    Notification::fake();

    assertDatabaseCount(Engagement::class, 0);

    $data = CreateEngagementRequestFactory::new()->create();

    app(CreateEngagement::class)->execute(new EngagementCreationData(
        user: $data['user'],
        recipient: $data['recipient'],
        channel: $data['channel'],
        subject: $data['subject'],
        body: $data['body'],
        scheduledAt: null,
    ));

    assertDatabaseCount(Engagement::class, 1);

    expect(Engagement::first())
        ->user->is($data['user'])->toBeTrue()
        ->recipient->is($data['recipient'])->toBeTrue()
        ->channel->toBe($data['channel'])
        ->subject->toBe($data['subject'])
        ->body->toMatchArray($data['body'])
        ->scheduled_at->toBeNull()
        ->dispatched_at->not->toBeNull();

    Notification::assertSentTo(
        $data['recipient'],
        EngagementNotification::class
    );
});

it('will create but not dispatch a scheduled engagement', function () {
    Notification::fake();

    assertDatabaseCount(Engagement::class, 0);

    $data = CreateEngagementRequestFactory::new()->create();

    app(CreateEngagement::class)->execute(new EngagementCreationData(
        user: $data['user'],
        recipient: $data['recipient'],
        channel: $data['channel'],
        subject: $data['subject'],
        body: $data['body'],
        scheduledAt: $scheduledAt = now()->addMinute(),
    ));

    assertDatabaseCount(Engagement::class, 1);

    expect(Engagement::first())
        ->user->is($data['user'])->toBeTrue()
        ->recipient->is($data['recipient'])->toBeTrue()
        ->channel->toBe($data['channel'])
        ->subject->toBe($data['subject'])
        ->body->toMatchArray($data['body'])
        ->scheduled_at->startOfSecond()->eq($scheduledAt->startOfSecond())->toBeTrue()
        ->dispatched_at->toBeNull();

    Notification::assertNotSentTo(
        $data['recipient'],
        EngagementNotification::class
    );
});
