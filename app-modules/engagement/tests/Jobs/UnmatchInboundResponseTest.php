<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

    Aiding App™ is licensed under the Elastic License 2.0. For more details,
    see <https://github.com/canyongbs/aidingapp/blob/main/LICENSE.>

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Aiding App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    <https://www.canyongbs.com> or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

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
