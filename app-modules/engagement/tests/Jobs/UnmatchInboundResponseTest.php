<?php

namespace AidingApp\Engagement\Tests\Jobs;

use AidingApp\Contact\Models\Contact;
use AidingApp\Engagement\Enums\EngagementResponseType;
use AidingApp\Engagement\Jobs\UnmatchedInboundCommunicationsJob;
use AidingApp\Engagement\Models\UnmatchedInboundCommunication;

use function Pest\Laravel\assertDatabaseHas;

it('will properly match to and create an engagement response for a contact', function () {
    $unmatchedInboundRecordOne = UnmatchedInboundCommunication::factory()->email()->create();
    $unmatchedInboundRecordTwo = UnmatchedInboundCommunication::factory()->email()->create();

    $contact = Contact::factory()->create([
        'email' => $unmatchedInboundRecordOne->sender,
    ]);

    expect(UnmatchedInboundCommunication::count())->toBe(2);
    expect(Contact::query()->whereHas('engagementResponses')->count())->toBe(0);

    assertDatabaseHas('unmatched_inbound_communications', [
        'sender' => $unmatchedInboundRecordOne->sender,
    ]);

    $job = new UnmatchedInboundCommunicationsJob();
    $job->handle();

    expect(UnmatchedInboundCommunication::count())->toBe(1);
    expect(Contact::query()->whereHas('engagementResponses')->count())->toBe(1);

    assertDatabaseHas('unmatched_inbound_communications', [
        'sender' => $unmatchedInboundRecordTwo->sender,
    ]);
    assertDatabaseHas('engagement_responses', [
        'type' => EngagementResponseType::Email,
        'content' => $unmatchedInboundRecordOne->body,
        'subject' => $unmatchedInboundRecordOne->subject,
        'sender_id' => $contact->getKey(),
    ]);
});

it('can check unmatched inbound communications with no match', function () {
    $unmatchedInboundEmailRecord = UnmatchedInboundCommunication::factory()->email()->create();

    expect(UnmatchedInboundCommunication::count())->toBe(1);

    Contact::factory()->create();

    expect(Contact::query()->whereHas('engagementResponses')->exists())->toBeFalse();

    assertDatabaseHas('unmatched_inbound_communications', [
        'sender' => $unmatchedInboundEmailRecord->sender,
        'type' => EngagementResponseType::Email,
    ]);
    $job = new UnmatchedInboundCommunicationsJob();
    $job->handle();

    expect(UnmatchedInboundCommunication::count())->toBe(1);
    expect(Contact::query()->whereHas('engagementResponses')->count())->toBe(0);
});
