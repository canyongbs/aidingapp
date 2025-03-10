<?php

use AidingApp\Contact\Models\Contact;
use AidingApp\Engagement\Actions\CreateEngagementBatch;
use AidingApp\Engagement\DataTransferObjects\EngagementCreationData;
use AidingApp\Engagement\Models\EngagementBatch;
use AidingApp\Engagement\Tests\RequestFactories\CreateEngagementBatchRequestFactory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Testing\Fakes\PendingBatchFake;

use function Pest\Laravel\assertDatabaseCount;

it('will create an engagement batch', function () {
    Bus::fake();

    assertDatabaseCount(EngagementBatch::class, 0);

    $data = CreateEngagementBatchRequestFactory::new()->create();

    app(CreateEngagementBatch::class)->execute(new EngagementCreationData(
        user: $data['user'],
        recipient: Contact::factory()->count(10)->create(),
        channel: $data['channel'],
        subject: $data['subject'],
        body: $data['body'],
        scheduledAt: Carbon::parse($data['scheduledAt']),
    ));

    assertDatabaseCount(EngagementBatch::class, 1);

    expect(EngagementBatch::first())
        ->user->is($data['user'])->toBeTrue()
        ->channel->toBe($data['channel'])
        ->subject->toBe($data['subject'])
        ->body->toMatchArray($data['body'])
        ->scheduled_at->startOfSecond()->eq(Carbon::parse($data['scheduledAt'])->startOfSecond())->toBeTrue();

    Bus::assertBatched(fn (PendingBatchFake $batch): bool => $batch->jobs->count() === 10);
});
