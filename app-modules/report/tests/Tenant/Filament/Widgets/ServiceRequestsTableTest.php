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
use AidingApp\Contact\Models\Organization;
use AidingApp\Report\Filament\Widgets\ServiceRequestsTable;
use AidingApp\ServiceManagement\Enums\ServiceRequestCategory;
use AidingApp\ServiceManagement\Enums\SystemServiceRequestClassification;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestAssignment;
use AidingApp\ServiceManagement\Models\ServiceRequestPriority;
use AidingApp\ServiceManagement\Models\ServiceRequestStatus;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use App\Models\User;
use Filament\Actions\ExportAction;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

it('returns all service requests information created in given time range', function (): void {
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

    Livewire::test(ServiceRequestsTable::class, [
        'cacheTag' => 'test-service-requests-table',
        'pageFilters' => $filters,
    ])
        ->assertCanSeeTableRecords(collect([
            $openRequest,
            $closedRequest,
            $inProgressRequest,
        ]))
        ->assertCanNotSeeTableRecords(collect([$otherRequest]));
});

it('returns all service requests when no date filters are applied', function (): void {
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

    Livewire::test(ServiceRequestsTable::class, [
        'cacheTag' => 'test-service-requests-table-no-filters',
        'pageFilters' => [],
    ])
        ->assertCanSeeTableRecords(collect([
            $request1,
            $request2,
            $request3,
        ]));
});

it('searches service requests by contact or organization name case insensitively', function (string $contactName, string $organizationName, string $search) {
    actingAs(User::factory()->state(['timezone' => 'UTC'])->create());

    $contact = Contact::factory()
        ->for(Organization::factory()->state(['name' => $organizationName]), 'organization')
        ->state([
            'first_name' => $contactName,
            'last_name' => 'Example',
            'full_name' => "{$contactName} Example",
        ])
        ->create();

    $matchingRequest = ServiceRequest::factory()
        ->for($contact, 'respondent')
        ->state(['title' => 'Matching request'])
        ->create();

    $otherContact = Contact::factory()
        ->for(Organization::factory()->state(['name' => 'Unrelated Company']), 'organization')
        ->state([
            'first_name' => 'Other',
            'last_name' => 'Person',
            'full_name' => 'Other Person',
        ])
        ->create();

    $otherRequest = ServiceRequest::factory()
        ->for($otherContact, 'respondent')
        ->state(['title' => 'Unrelated request'])
        ->create();

    livewire(ServiceRequestsTable::class, [
        'cacheTag' => 'test-service-requests-table-related-search',
        'pageFilters' => [],
    ])
        ->searchTable($search)
        ->assertCanSeeTableRecords(collect([$matchingRequest]))
        ->assertCanNotSeeTableRecords(collect([$otherRequest]));
})->with([
    'ASCII contact' => ['Alice', 'Other Company', 'ALICE'],
    'ASCII organization' => ['Other', 'Example University', 'UNIVERSITY'],
    'multibyte contact' => ["Ren\u{00E9}e", 'Other Company', "REN\u{00C9}E"],
    'multibyte organization' => ['Other', "\u{00E9}cole Example", "\u{00C9}COLE"],
]);

it('has table an export action', function (): void {
    Livewire::test(ServiceRequestsTable::class, [
        'cacheTag' => 'test-service-requests-table-export',
        'pageFilters' => [],
    ])->assertTableActionExists(ExportAction::class);
});

it('can start an export and send a notification', function (): void {
    Storage::fake('s3');

    actingAs(User::factory()->create());

    Livewire::test(ServiceRequestsTable::class, [
        'cacheTag' => 'test-service-requests-table-export-notification',
        'pageFilters' => [],
    ])
        ->callTableAction(ExportAction::class)
        ->assertNotified();
});

it('filters records by category using the table-level category filter', function (): void {
    $type = ServiceRequestType::factory()->create();
    $priority = ServiceRequestPriority::factory()->state(['type_id' => $type->id])->create();

    $status = ServiceRequestStatus::factory()->state([
        'classification' => SystemServiceRequestClassification::Open,
    ])->create();

    $incidentRequest = ServiceRequest::factory()->state([
        'priority_id' => $priority->id,
        'status_id' => $status->id,
        'category' => ServiceRequestCategory::Incident,
        'respondent_id' => Contact::factory(),
    ])->create();

    $requestRequest = ServiceRequest::factory()->state([
        'priority_id' => $priority->id,
        'status_id' => $status->id,
        'category' => ServiceRequestCategory::Request,
        'respondent_id' => Contact::factory(),
    ])->create();

    Livewire::test(ServiceRequestsTable::class, [
        'cacheTag' => 'test-service-requests-table-category-filter',
        'pageFilters' => [],
    ])
        ->filterTable('category', ServiceRequestCategory::Incident->value)
        ->assertCanSeeTableRecords(collect([$incidentRequest]))
        ->assertCanNotSeeTableRecords(collect([$requestRequest]));
});

it('searches the assigned to column by full name, including a quoted phrase', function (string $search): void {
    $openStatus = ServiceRequestStatus::factory()->state(['classification' => SystemServiceRequestClassification::Open])->create();

    $matching = ServiceRequest::factory()->state(['status_id' => $openStatus->getKey()])->create();
    $other = ServiceRequest::factory()->state(['status_id' => $openStatus->getKey()])->create();

    foreach ([[$matching, 'Avery', 'Smith'], [$other, 'Jordan', 'Lee']] as [$serviceRequest, $firstName, $lastName]) {
        $assignee = User::factory()->create(['first_name' => $firstName, 'last_name' => $lastName]);

        $serviceRequest->priority->type->managerUsers()->attach($assignee);

        ServiceRequestAssignment::factory()->active()->create([
            'service_request_id' => $serviceRequest->getKey(),
            'user_id' => $assignee->getKey(),
        ]);
    }

    Livewire::test(ServiceRequestsTable::class, [
        'cacheTag' => 'test-service-requests-table-assigned-to-search',
        'pageFilters' => [],
    ])
        ->searchTable($search)
        ->assertCountTableRecords(1)
        ->assertCanSeeTableRecords([$matching])
        ->assertCanNotSeeTableRecords([$other]);
})->with([
    'first name' => 'Avery',
    'last name' => 'Smith',
    'full name' => 'Avery Smith',
    'quoted full name' => '"Avery Smith"',
]);

it('searches the assigned to filter options by full name', function (string $search): void {
    $openStatus = ServiceRequestStatus::factory()->state(['classification' => SystemServiceRequestClassification::Open])->create();

    $assignees = collect([['Avery', 'Smith'], ['Jordan', 'Lee']])->map(function (array $name) use ($openStatus): User {
        $assignee = User::factory()->create(['first_name' => $name[0], 'last_name' => $name[1]]);

        $serviceRequest = ServiceRequest::factory()->state(['status_id' => $openStatus->getKey()])->create();
        $serviceRequest->priority->type->managerUsers()->attach($assignee);

        ServiceRequestAssignment::factory()->active()->create([
            'service_request_id' => $serviceRequest->getKey(),
            'user_id' => $assignee->getKey(),
        ]);

        return $assignee;
    });

    $component = Livewire::test(ServiceRequestsTable::class, [
        'cacheTag' => 'test-service-requests-table-assigned-to-filter-search',
        'pageFilters' => [],
    ]);

    $select = $component->instance()->getTableFiltersForm()->getComponentByStatePath('assigned_to.value');

    expect($select->getSearchResults($search))->toBe([$assignees[0]->getKey() => 'Avery Smith']);
})->with([
    'first name' => 'Avery',
    'last name' => 'Smith',
    'full name' => 'Avery Smith',
]);
