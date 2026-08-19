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

use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestAssignment;
use AidingApp\ServiceManagement\Models\ServiceRequestUpdate;
use AidingApp\Timeline\Livewire\TimelineList;

use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

it('renders the timeline records for the given record', function () {
    asSuperAdmin();

    $serviceRequest = ServiceRequest::factory()->create();

    $update = ServiceRequestUpdate::factory()
        ->for($serviceRequest, 'serviceRequest')
        ->create(['update' => 'A distinctive timeline entry']);

    livewire(TimelineList::class, [
        'record' => $serviceRequest,
        'modelsToTimeline' => [ServiceRequestUpdate::class],
    ])
        ->assertSuccessful()
        ->assertSee($update->update);
});

it('shows the empty state message when there is nothing to timeline', function () {
    asSuperAdmin();

    $serviceRequest = ServiceRequest::factory()->create();

    livewire(TimelineList::class, [
        'record' => $serviceRequest,
        'modelsToTimeline' => [ServiceRequestUpdate::class],
        'emptyStateMessage' => 'Nothing has happened yet.',
    ])
        ->assertSuccessful()
        ->assertSee('Nothing has happened yet.');
});

it('loads one page of records at a time and flags that more remain', function () {
    asSuperAdmin();

    $serviceRequest = ServiceRequest::factory()->create();

    ServiceRequestUpdate::factory()
        ->for($serviceRequest, 'serviceRequest')
        ->count(7)
        ->create();

    $component = livewire(TimelineList::class, [
        'record' => $serviceRequest,
        'modelsToTimeline' => [ServiceRequestUpdate::class],
    ])->assertSuccessful();

    expect($component->get('timelineRecords'))->toHaveCount(5)
        ->and($component->get('hasMorePages'))->toBeTrue();

    $component->call('loadTimelineRecords')
        ->assertSuccessful();
});

it('refuses to open a record from another entity timeline', function () {
    asSuperAdmin();

    $serviceRequest = ServiceRequest::factory()->create();
    $otherServiceRequest = ServiceRequest::factory()->create();

    $otherUpdate = ServiceRequestUpdate::factory()
        ->for($otherServiceRequest, 'serviceRequest')
        ->create();

    livewire(TimelineList::class, [
        'record' => $serviceRequest,
        'modelsToTimeline' => [ServiceRequestUpdate::class],
    ])
        ->call('viewRecord', $otherUpdate->getKey(), $otherUpdate->getMorphClass())
        ->assertStatus(404);
});

it('refuses to open a record of a type this timeline does not display', function () {
    asSuperAdmin();

    $serviceRequest = ServiceRequest::factory()->create();

    $update = ServiceRequestUpdate::factory()
        ->for($serviceRequest, 'serviceRequest')
        ->create();

    livewire(TimelineList::class, [
        'record' => $serviceRequest,
        'modelsToTimeline' => [ServiceRequestAssignment::class],
    ])
        ->call('viewRecord', $update->getKey(), $update->getMorphClass())
        ->assertStatus(404);
});

it('returns a 404 when the view action is mounted without a record', function () {
    asSuperAdmin();

    $serviceRequest = ServiceRequest::factory()->create();

    ServiceRequestUpdate::factory()
        ->for($serviceRequest, 'serviceRequest')
        ->create();

    livewire(TimelineList::class, [
        'record' => $serviceRequest,
        'modelsToTimeline' => [ServiceRequestUpdate::class],
    ])
        ->call('mountAction', 'view')
        ->assertStatus(404);
});

it('keeps the opened record selected when more timeline records are loaded', function () {
    asSuperAdmin();

    $serviceRequest = ServiceRequest::factory()->create();

    $update = ServiceRequestUpdate::factory()
        ->for($serviceRequest, 'serviceRequest')
        ->create();

    livewire(TimelineList::class, [
        'record' => $serviceRequest,
        'modelsToTimeline' => [ServiceRequestUpdate::class],
    ])
        ->call('viewRecord', $update->getKey(), $update->getMorphClass())
        ->assertSuccessful()
        ->call('loadTimelineRecords')
        ->assertSuccessful()
        ->assertSet('currentRecordToView.id', $update->getKey());
});

it('opens the slide over for a timeline record', function () {
    asSuperAdmin();

    $serviceRequest = ServiceRequest::factory()->create();

    $update = ServiceRequestUpdate::factory()
        ->for($serviceRequest, 'serviceRequest')
        ->create();

    livewire(TimelineList::class, [
        'record' => $serviceRequest,
        'modelsToTimeline' => [ServiceRequestUpdate::class],
    ])
        ->call('viewRecord', $update->getKey(), $update->getMorphClass())
        ->assertSuccessful()
        ->assertSet('currentRecordToView.id', $update->getKey());
});
