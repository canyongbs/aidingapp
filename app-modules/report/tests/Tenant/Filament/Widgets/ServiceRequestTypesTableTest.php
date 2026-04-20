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

use AidingApp\Report\Filament\Widgets\ServiceRequestTypesTable;
use AidingApp\ServiceManagement\Enums\ServiceRequestCategory;
use AidingApp\ServiceManagement\Enums\SystemServiceRequestClassification;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestPriority;
use AidingApp\ServiceManagement\Models\ServiceRequestStatus;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use App\Features\ServiceRequestCategoryRenameFeature;

use function Pest\Livewire\livewire;

// TODO: ServiceRequestCategoryRenameFeature Cleanup - Remove this beforeEach after the feature flag is removed.
beforeEach(function () {
    ServiceRequestCategoryRenameFeature::activate();
});

it('returns all service request types information created in given time range', function () {
    $startDate = now()->subDays(10);
    $endDate = now()->subDays(5);
    $otherDate = now()->subDays(15);

    $type1 = ServiceRequestType::factory()->create(['name' => 'Technical Support']);
    $type2 = ServiceRequestType::factory()->create(['name' => 'Billing Inquiry']);
    $type3 = ServiceRequestType::factory()->create(['name' => 'General Question']);

    $priority1 = ServiceRequestPriority::factory()->state(['type_id' => $type1->id])->create();
    $priority2 = ServiceRequestPriority::factory()->state(['type_id' => $type2->id])->create();
    $priority3 = ServiceRequestPriority::factory()->state(['type_id' => $type3->id])->create();

    $status = ServiceRequestStatus::factory()->state([
        'name' => SystemServiceRequestClassification::Open->getLabel(),
        'classification' => SystemServiceRequestClassification::Open,
    ])->create();

    ServiceRequest::factory()->state([
        'priority_id' => $priority1->id,
        'status_id' => $status->id,
        'created_at' => $startDate,
    ])->create();

    ServiceRequest::factory()->state([
        'priority_id' => $priority2->id,
        'status_id' => $status->id,
        'created_at' => $startDate,
    ])->create();

    ServiceRequest::factory()->state([
        'priority_id' => $priority3->id,
        'status_id' => $status->id,
        'created_at' => $endDate,
    ])->create();

    ServiceRequest::factory()->state([
        'priority_id' => $priority1->id,
        'status_id' => $status->id,
        'created_at' => $otherDate,
    ])->create();

    $filters = [
        'startDate' => $startDate->toDateString(),
        'endDate' => $endDate->toDateString(),
    ];

    livewire(ServiceRequestTypesTable::class, [
        'cacheTag' => 'test-service-request-types-table',
        'pageFilters' => $filters,
    ])
        ->assertCanSeeTableRecords(collect([
            $type1,
            $type2,
            $type3,
        ]));
});

it('returns all service request types when no date filters are applied', function () {
    $type1 = ServiceRequestType::factory()->create(['name' => 'Account Issue']);
    $type2 = ServiceRequestType::factory()->create(['name' => 'Feature Request']);

    $priority1 = ServiceRequestPriority::factory()->state(['type_id' => $type1->id])->create();
    $priority2 = ServiceRequestPriority::factory()->state(['type_id' => $type2->id])->create();

    $status = ServiceRequestStatus::factory()->state([
        'name' => SystemServiceRequestClassification::InProgress->getLabel(),
        'classification' => SystemServiceRequestClassification::InProgress,
    ])->create();

    ServiceRequest::factory()->count(3)->state([
        'priority_id' => $priority1->id,
        'status_id' => $status->id,
        'created_at' => now()->subMonths(1),
    ])->create();

    ServiceRequest::factory()->count(5)->state([
        'priority_id' => $priority2->id,
        'status_id' => $status->id,
        'created_at' => now()->subMonths(2),
    ])->create();

    livewire(ServiceRequestTypesTable::class, [
        'cacheTag' => 'test-service-request-types-table-no-filters',
        'pageFilters' => [],
    ])
        ->assertCanSeeTableRecords(collect([
            $type1,
            $type2,
        ]));
});

it('shows correct incident and request counts per type', function () {
    $type = ServiceRequestType::factory()->create(['name' => 'Access Issues']);
    $priority = ServiceRequestPriority::factory()->state(['type_id' => $type->id])->create();

    $status = ServiceRequestStatus::factory()->state([
        'classification' => SystemServiceRequestClassification::Open,
    ])->create();

    ServiceRequest::factory()->count(3)->state([
        'priority_id' => $priority->id,
        'status_id' => $status->id,
        (ServiceRequestCategoryRenameFeature::active() ? 'category' : 'issue_category') => ServiceRequestCategory::Incident,
    ])->create();

    ServiceRequest::factory()->count(5)->state([
        'priority_id' => $priority->id,
        'status_id' => $status->id,
        (ServiceRequestCategoryRenameFeature::active() ? 'category' : 'issue_category') => ServiceRequestCategory::Request,
    ])->create();

    livewire(ServiceRequestTypesTable::class, [
        'cacheTag' => 'test-service-request-types-incident-request-counts',
        'pageFilters' => [],
    ])
        ->assertTableColumnFormattedStateSet('service_requests_count', '8', record: $type)
        ->assertTableColumnFormattedStateSet('incident_count', '3', record: $type)
        ->assertTableColumnFormattedStateSet('request_count', '5', record: $type);
});

it('applies date filters consistently to total, incident, and request counts', function () {
    $startDate = now()->subDays(10);
    $endDate = now()->subDays(5);

    $type = ServiceRequestType::factory()->create(['name' => 'Network Issues']);
    $priority = ServiceRequestPriority::factory()->state(['type_id' => $type->id])->create();

    $status = ServiceRequestStatus::factory()->state([
        'classification' => SystemServiceRequestClassification::Open,
    ])->create();

    ServiceRequest::factory()->count(2)->state([
        'priority_id' => $priority->id,
        'status_id' => $status->id,
        (ServiceRequestCategoryRenameFeature::active() ? 'category' : 'issue_category') => ServiceRequestCategory::Incident,
        'created_at' => now()->subDays(7),
    ])->create();

    ServiceRequest::factory()->count(3)->state([
        'priority_id' => $priority->id,
        'status_id' => $status->id,
        (ServiceRequestCategoryRenameFeature::active() ? 'category' : 'issue_category') => ServiceRequestCategory::Request,
        'created_at' => now()->subDays(6),
    ])->create();

    ServiceRequest::factory()->count(4)->state([
        'priority_id' => $priority->id,
        'status_id' => $status->id,
        (ServiceRequestCategoryRenameFeature::active() ? 'category' : 'issue_category') => ServiceRequestCategory::Incident,
        'created_at' => now()->subDays(20),
    ])->create();

    ServiceRequest::factory()->count(1)->state([
        'priority_id' => $priority->id,
        'status_id' => $status->id,
        (ServiceRequestCategoryRenameFeature::active() ? 'category' : 'issue_category') => ServiceRequestCategory::Request,
        'created_at' => now()->subDays(30),
    ])->create();

    livewire(ServiceRequestTypesTable::class, [
        'cacheTag' => 'test-service-request-types-date-filter-invariant',
        'pageFilters' => [
            'startDate' => $startDate->toDateString(),
            'endDate' => $endDate->toDateString(),
        ],
    ])
        ->assertTableColumnFormattedStateSet('service_requests_count', '5', record: $type)
        ->assertTableColumnFormattedStateSet('incident_count', '2', record: $type)
        ->assertTableColumnFormattedStateSet('request_count', '3', record: $type);
});
