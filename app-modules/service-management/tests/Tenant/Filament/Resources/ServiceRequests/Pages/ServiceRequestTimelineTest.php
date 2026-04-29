<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Aiding App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Aiding App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    <https://www.canyongbs.com> or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

use AidingApp\Contact\Models\Contact;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestHistory;
use AidingApp\ServiceManagement\Models\ServiceRequestPriority;
use AidingApp\ServiceManagement\Models\ServiceRequestStatus;
use App\Models\User;

use function Pest\Laravel\actingAs;

it('writes a single Created history row when a service request is created', function () {
    $user = User::factory()->create();

    actingAs($user);

    $serviceRequest = ServiceRequest::factory()->create();

    $createdRows = $serviceRequest->histories
        ->filter(fn (ServiceRequestHistory $row) => $row->isCreatedEvent())
        ->values();

    expect($createdRows)->toHaveCount(1);

    $createdRow = $createdRows->first();

    expect($createdRow->new_values)
        ->toHaveKey('status_id', $serviceRequest->status_id)
        ->toHaveKey('priority_id', $serviceRequest->priority_id)
        ->toHaveKey('title', $serviceRequest->title)
        ->and($createdRow->actor_id)->toBe($user->getKey())
        ->and($createdRow->actor_type)->toBe($user->getMorphClass());
});

it('does not write field-change history rows on the initial save', function () {
    actingAs(User::factory()->create());

    $serviceRequest = ServiceRequest::factory()->create();

    $fieldRows = $serviceRequest->histories
        ->reject(fn (ServiceRequestHistory $row) => $row->isCreatedEvent());

    expect($fieldRows)->toHaveCount(0);
});

it('writes one history row per real field changed on subsequent saves', function () {
    actingAs(User::factory()->create());

    $serviceRequest = ServiceRequest::factory()->create();

    $newPriority = ServiceRequestPriority::factory()
        ->for($serviceRequest->priority->type, 'type')
        ->create();

    $newStatus = ServiceRequestStatus::factory()->create();

    // Reload so wasRecentlyCreated is reset and the next save dispatches history.
    $serviceRequest = $serviceRequest->fresh();

    $serviceRequest->update([
        'priority_id' => $newPriority->getKey(),
        'status_id' => $newStatus->getKey(),
    ]);

    $fieldRows = $serviceRequest->histories()
        ->get()
        ->reject(fn (ServiceRequestHistory $row) => $row->isCreatedEvent())
        ->values();

    expect($fieldRows)->toHaveCount(2);

    $changedFields = $fieldRows
        ->map(fn (ServiceRequestHistory $row) => array_key_first($row->new_values))
        ->sort()
        ->values()
        ->all();

    expect($changedFields)->toEqualCanonicalizing(['priority_id', 'status_id']);

    foreach ($fieldRows as $row) {
        expect($row->new_values)->toHaveCount(1)
            ->and($row->original_values)->toHaveCount(1);
    }
});

it('filters bookkeeping keys from field-change history rows', function () {
    actingAs(User::factory()->create());

    $serviceRequest = ServiceRequest::factory()->create();

    $newStatus = ServiceRequestStatus::factory()->create();

    $serviceRequest = $serviceRequest->fresh();

    $serviceRequest->update(['status_id' => $newStatus->getKey()]);

    $rowFields = $serviceRequest->histories()
        ->get()
        ->reject(fn (ServiceRequestHistory $row) => $row->isCreatedEvent())
        ->map(fn (ServiceRequestHistory $row) => array_key_first($row->new_values))
        ->all();

    expect($rowFields)->toContain('status_id')
        ->and($rowFields)->not->toContain('status_updated_at')
        ->and($rowFields)->not->toContain('time_to_resolution')
        ->and($rowFields)->not->toContain('service_request_form_submission_id')
        ->and($rowFields)->not->toContain('updated_at');
});

it('records the authenticated user as the actor on field-change rows', function () {
    $user = User::factory()->create();

    actingAs($user);

    $serviceRequest = ServiceRequest::factory()->create();

    $newPriority = ServiceRequestPriority::factory()
        ->for($serviceRequest->priority->type, 'type')
        ->create();

    $serviceRequest = $serviceRequest->fresh();

    $serviceRequest->update(['priority_id' => $newPriority->getKey()]);

    $fieldRow = $serviceRequest->histories()
        ->get()
        ->reject(fn (ServiceRequestHistory $row) => $row->isCreatedEvent())
        ->first();

    expect($fieldRow)->not->toBeNull()
        ->and($fieldRow->actor_id)->toBe($user->getKey())
        ->and($fieldRow->actor_type)->toBe($user->getMorphClass());
});

it('renders specific event titles for known field changes', function () {
    $cases = [
        'status_id' => 'Status Updated',
        'priority_id' => 'Priority Updated',
        'type_id' => 'Type Updated',
        'division_id' => 'Division Updated',
        'category' => 'Category Updated',
        'title' => 'Title Updated',
        'respondent_id' => 'Respondent Updated',
    ];

    foreach ($cases as $field => $expectedTitle) {
        $history = new ServiceRequestHistory([
            'original_values' => [$field => 'old'],
            'new_values' => [$field => 'new'],
        ]);

        expect($history->eventTitle())->toBe($expectedTitle);
    }
});

it('renders the Created event title for rows with empty original values and a snapshot in new values', function () {
    $created = new ServiceRequestHistory([
        'original_values' => [],
        'new_values' => ['status_id' => 'abc', 'priority_id' => 'def'],
    ]);

    expect($created->eventTitle())->toBe('Service Request Created')
        ->and($created->isCreatedEvent())->toBeTrue();
});

it('returns "System" as actorName when no actor is associated', function () {
    expect((new ServiceRequestHistory())->actorName())->toBe('System');
});

it('returns the User name as actorName when the actor is a User', function () {
    $user = User::factory()->state(['name' => 'Heather Sheridan'])->create();

    $history = new ServiceRequestHistory();
    $history->actor()->associate($user);

    expect($history->actorName())->toBe('Heather Sheridan');
});

it('returns the Contact full_name as actorName when the actor is a Contact', function () {
    $contact = Contact::factory()->create();

    $history = new ServiceRequestHistory();
    $history->actor()->associate($contact);

    expect($history->actorName())->toBe($contact->full_name);
});

it('resolves snapshot helpers from new_values for Created rows', function () {
    actingAs(User::factory()->create());

    $serviceRequest = ServiceRequest::factory()->create();

    $createdRow = $serviceRequest->histories
        ->first(fn (ServiceRequestHistory $row) => $row->isCreatedEvent());

    expect($createdRow)->not->toBeNull()
        ->and($createdRow->snapshotStatus()?->getKey())->toBe($serviceRequest->status_id)
        ->and($createdRow->snapshotPriority()?->getKey())->toBe($serviceRequest->priority_id)
        ->and($createdRow->snapshotType()?->getKey())->toBe($serviceRequest->priority->type_id);
});

it('returns empty getUpdates for the Created event', function () {
    $created = new ServiceRequestHistory([
        'original_values' => [],
        'new_values' => ['status_id' => 'abc', 'priority_id' => 'def'],
    ]);

    expect($created->getUpdates())->toBe([]);
});
