<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

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

use AidingApp\Contact\Models\Contact;
use AidingApp\Report\Filament\Widgets\ServiceRequestsTable;
use AidingApp\ServiceManagement\Enums\SystemServiceRequestClassification;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestPriority;
use AidingApp\ServiceManagement\Models\ServiceRequestStatus;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use App\Models\User;
use Filament\Actions\ExportAction;
use Illuminate\Support\Facades\Storage;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

it('returns all service requests information created in given time range', function () {
    $startDate = now()->subDays(10);
    $endDate = now()->subDays(5);
    $otherDate = now()->subDays(15);

    $type = ServiceRequestType::factory()->create();
    $priority = ServiceRequestPriority::factory()->state(['type_id' => $type->id])->create();

    $openStatus = ServiceRequestStatus::factory()->state([
        'name' => SystemServiceRequestClassification::Open->getLabel(),
        'classification' => SystemServiceRequestClassification::Open,
    ])->create();

    $closedStatus = ServiceRequestStatus::factory()->state([
        'name' => SystemServiceRequestClassification::Closed->getLabel(),
        'classification' => SystemServiceRequestClassification::Closed,
    ])->create();

    $inProgressStatus = ServiceRequestStatus::factory()->state([
        'name' => SystemServiceRequestClassification::InProgress->getLabel(),
        'classification' => SystemServiceRequestClassification::InProgress,
    ])->create();

    $openRequest = ServiceRequest::factory()->state([
        'priority_id' => $priority->id,
        'status_id' => $openStatus->id,
        'created_at' => $startDate,
        'respondent_id' => Contact::factory(),
    ])->create();

    $closedRequest = ServiceRequest::factory()->state([
        'priority_id' => $priority->id,
        'status_id' => $closedStatus->id,
        'created_at' => $startDate,
        'respondent_id' => Contact::factory(),
    ])->create();

    $inProgressRequest = ServiceRequest::factory()->state([
        'priority_id' => $priority->id,
        'status_id' => $inProgressStatus->id,
        'created_at' => $endDate,
        'respondent_id' => Contact::factory(),
    ])->create();

    $otherRequest = ServiceRequest::factory()->state([
        'priority_id' => $priority->id,
        'status_id' => $openStatus->id,
        'created_at' => $otherDate,
        'respondent_id' => Contact::factory(),
    ])->create();

    $filters = [
        'startDate' => $startDate->toDateString(),
        'endDate' => $endDate->toDateString(),
    ];

    livewire(ServiceRequestsTable::class, [
        'cacheTag' => 'test-service-requests-table',
        'filters' => $filters,
    ])
        ->assertCanSeeTableRecords(collect([
            $openRequest,
            $closedRequest,
            $inProgressRequest,
        ]))
        ->assertCanNotSeeTableRecords(collect([$otherRequest]));
});

it('returns all service requests when no date filters are applied', function () {
    $type = ServiceRequestType::factory()->create();
    $priority = ServiceRequestPriority::factory()->state(['type_id' => $type->id])->create();

    $status = ServiceRequestStatus::factory()->state([
        'name' => SystemServiceRequestClassification::Open->getLabel(),
        'classification' => SystemServiceRequestClassification::Open,
    ])->create();

    $request1 = ServiceRequest::factory()->state([
        'priority_id' => $priority->id,
        'status_id' => $status->id,
        'created_at' => now()->subDays(5),
        'respondent_id' => Contact::factory(),
    ])->create();

    $request2 = ServiceRequest::factory()->state([
        'priority_id' => $priority->id,
        'status_id' => $status->id,
        'created_at' => now()->subDays(15),
        'respondent_id' => Contact::factory(),
    ])->create();

    $request3 = ServiceRequest::factory()->state([
        'priority_id' => $priority->id,
        'status_id' => $status->id,
        'created_at' => now()->subMonths(2),
        'respondent_id' => Contact::factory(),
    ])->create();

    livewire(ServiceRequestsTable::class, [
        'cacheTag' => 'test-service-requests-table-no-filters',
        'filters' => [],
    ])
        ->assertCanSeeTableRecords(collect([
            $request1,
            $request2,
            $request3,
        ]));
});

it('has table an export action', function () {
    livewire(ServiceRequestsTable::class, [
        'cacheTag' => 'test-service-requests-table-export',
        'filters' => [],
    ])->assertTableActionExists(ExportAction::class);
});

it('can start an export and send a notification', function () {
    Storage::fake('s3');

    actingAs(User::factory()->create());

    livewire(ServiceRequestsTable::class, [
        'cacheTag' => 'test-service-requests-table-export-notification',
        'filters' => [],
    ])
        ->callTableAction(ExportAction::class)
        ->assertNotified();
});
