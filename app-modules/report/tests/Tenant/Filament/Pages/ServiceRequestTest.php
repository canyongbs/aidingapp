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
use AidingApp\Department\Models\Department;
use AidingApp\Report\Enums\ReportAccessKey;
use AidingApp\Report\Filament\Pages\ServiceRequests;
use AidingApp\Report\Filament\Widgets\ServiceRequestCategoryDistributionDonutChart;
use AidingApp\Report\Filament\Widgets\ServiceRequestsOverTimeBarChart;
use AidingApp\Report\Filament\Widgets\ServiceRequestsStats;
use AidingApp\Report\Filament\Widgets\ServiceRequestsTable;
use AidingApp\Report\Filament\Widgets\ServiceRequestStatusDistributionDonutChart;
use AidingApp\Report\Filament\Widgets\ServiceRequestTypesTable;
use AidingApp\Report\Models\ReportDepartmentAccess;
use AidingApp\Report\Models\ReportUserAccess;
use AidingApp\ServiceManagement\Enums\ServiceRequestCategory;
use AidingApp\ServiceManagement\Enums\SystemServiceRequestClassification;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequests\Pages\ListServiceRequests;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestPriority;
use AidingApp\ServiceManagement\Models\ServiceRequestStatus;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use AidingApp\ServiceManagement\Models\ServiceRequestTypeCategory;
use App\Models\User;
use App\Settings\LicenseSettings;
use Filament\Actions\Testing\TestAction;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

/**
 * Enable the Service Management addon and grant the given user access to the
 * Service Requests report so the page can be mounted in tests.
 */
function grantServiceRequestsReportAccess(User $user): void
{
    $settings = app(LicenseSettings::class);
    $settings->data->addons->serviceManagement = true;
    $settings->save();

    ReportUserAccess::factory()->create([
        'report_key' => ReportAccessKey::ServiceRequests->value,
        'user_id' => $user->getKey(),
    ]);
}

it('is gated with proper access control', function () {
    $settings = app(LicenseSettings::class);
    $settings->data->addons->serviceManagement = false;
    $settings->save();

    $user = User::factory()->create(['timezone' => 'UTC']);

    actingAs($user);

    livewire(ServiceRequests::class)->assertForbidden();

    $settings->data->addons->serviceManagement = true;
    $settings->save();

    livewire(ServiceRequests::class)->assertForbidden();

    ReportUserAccess::factory()->create([
        'report_key' => ReportAccessKey::ServiceRequests->value,
        'user_id' => $user->getKey(),
    ]);

    livewire(ServiceRequests::class)->assertOk();
});

it('grants access to a user belonging to a department that has been granted access', function () {
    $settings = app(LicenseSettings::class);
    $settings->data->addons->serviceManagement = true;
    $settings->save();

    $department = Department::factory()->create();

    $user = User::factory()->create(['timezone' => 'UTC', 'department_id' => $department->getKey()]);

    actingAs($user);

    livewire(ServiceRequests::class)->assertForbidden();

    ReportDepartmentAccess::factory()->create([
        'report_key' => ReportAccessKey::ServiceRequests->value,
        'department_id' => $department->getKey(),
    ]);

    livewire(ServiceRequests::class)->assertOk();
});

it('renders the service request types filter and quick-load controls', function () {
    $user = User::factory()->create(['timezone' => 'UTC']);

    grantServiceRequestsReportAccess($user);

    actingAs($user);

    livewire(ServiceRequests::class)
        ->assertOk()
        ->assertSee('Service Request Types')
        ->assertSee('My Affiliated Types')
        ->assertSee('Clear');
});

it('groups the type options by the service catalog hierarchy', function () {
    $user = User::factory()->create(['timezone' => 'UTC']);

    grantServiceRequestsReportAccess($user);

    actingAs($user);

    $parentCategory = ServiceRequestTypeCategory::factory()->create(['parent_id' => null, 'name' => 'Parent Category']);
    $childCategory = ServiceRequestTypeCategory::factory()->create(['parent_id' => $parentCategory->getKey(), 'name' => 'Child Category']);

    $categorisedType = ServiceRequestType::factory()->create(['name' => 'Nested Type']);
    $childCategory->types()->attach($categorisedType->getKey(), ['sort' => 1]);

    $uncategorisedType = ServiceRequestType::factory()->create(['name' => 'Loner Type']);

    $tree = ListServiceRequests::buildTypeTreeOptions();

    // Find the uncategorized type in the tree
    $uncategorizedNode = collect($tree)->first(fn (array $node): bool => $node['value'] === $uncategorisedType->getKey());
    expect($uncategorizedNode)
        ->toBeArray()
        ->toHaveKey('value', $uncategorisedType->getKey())
        ->toHaveKey('name', 'Loner Type');

    // Find the parent category and verify it has the child category with the type
    $parentNode = collect($tree)->first(fn (array $node): bool => $node['value'] === 'category_' . $parentCategory->getKey());
    expect($parentNode)->toBeArray();

    $childNode = collect($parentNode['children'] ?? [])->first(fn (array $node): bool => $node['value'] === 'category_' . $childCategory->getKey());
    expect($childNode)->toBeArray();

    $typeNode = collect($childNode['children'] ?? [])->first(fn (array $node): bool => $node['value'] === $categorisedType->getKey());
    expect($typeNode)
        ->toBeArray()
        ->toHaveKey('value', $categorisedType->getKey())
        ->toHaveKey('name', 'Nested Type');
});

it('orders grouped type options by category sort and pivot sort', function () {
    $user = User::factory()->create(['timezone' => 'UTC']);

    grantServiceRequestsReportAccess($user);

    actingAs($user);

    $laterRoot = ServiceRequestTypeCategory::factory()->create(['parent_id' => null, 'name' => 'Later Root', 'sort' => 20]);
    $earlierRoot = ServiceRequestTypeCategory::factory()->create(['parent_id' => null, 'name' => 'Earlier Root', 'sort' => 10]);
    $earlierChild = ServiceRequestTypeCategory::factory()->create(['parent_id' => $earlierRoot->getKey(), 'name' => 'Earlier Child', 'sort' => 5]);

    $earlierRootSecondType = ServiceRequestType::factory()->create(['name' => 'Second In Root']);
    $earlierRoot->types()->attach($earlierRootSecondType->getKey(), ['sort' => 20]);

    $earlierRootFirstType = ServiceRequestType::factory()->create(['name' => 'First In Root']);
    $earlierRoot->types()->attach($earlierRootFirstType->getKey(), ['sort' => 10]);

    $childType = ServiceRequestType::factory()->create(['name' => 'Child Type']);
    $earlierChild->types()->attach($childType->getKey(), ['sort' => 5]);

    $laterRootType = ServiceRequestType::factory()->create(['name' => 'Later Root Type']);
    $laterRoot->types()->attach($laterRootType->getKey(), ['sort' => 1]);

    $tree = ListServiceRequests::buildTypeTreeOptions();

    // Earlier Root should come before Later Root (based on sort)
    $earlierRootNode = collect($tree)->first(fn (array $node): bool => $node['value'] === 'category_' . $earlierRoot->getKey());
    $laterRootNode = collect($tree)->first(fn (array $node): bool => $node['value'] === 'category_' . $laterRoot->getKey());

    expect($earlierRootNode['name'])->toBe('Earlier Root');
    expect($laterRootNode['name'])->toBe('Later Root');

    // Earlier Root should have the child category and types in correct order
    $earlierRootChildren = $earlierRootNode['children'] ?? [];
    $childNode = collect($earlierRootChildren)->first(fn (array $node): bool => $node['value'] === 'category_' . $earlierChild->getKey());
    expect($childNode)->toBeArray();

    // Types within Earlier Root should include both types
    $typeNodes = collect($earlierRootChildren)->filter(fn (array $node): bool => ! str_starts_with($node['value'], 'category_'));
    $typeIds = $typeNodes->map(fn (array $node): string => $node['value'])->values()->all();
    
    expect($typeIds)->toContain($earlierRootFirstType->getKey());
    expect($typeIds)->toContain($earlierRootSecondType->getKey());
});

it('excludes archived types from the filter options', function () {
    $user = User::factory()->create(['timezone' => 'UTC']);

    grantServiceRequestsReportAccess($user);

    actingAs($user);

    $activeType = ServiceRequestType::factory()->create(['name' => 'Active Type']);
    $archivedType = ServiceRequestType::factory()->create(['name' => 'Archived Type']);
    $archivedType->delete();

    $tree = ListServiceRequests::buildTypeTreeOptions();

    // Flatten the tree to get all type IDs
    $flattenedIds = collect($tree)
        ->map(fn (array $node): array => [$node['value'], ...collect($node['children'] ?? [])->map(fn (array $child): string => $child['value'])->values()->all()])
        ->flatten()
        ->filter(fn (string $value): bool => ! str_starts_with($value, 'category_'))
        ->values()
        ->all();

    expect($flattenedIds)
        ->toContain($activeType->getKey())
        ->not->toContain($archivedType->getKey());
});

it('resolves every type the user manages or audits as an affiliated type', function () {
    $department = Department::factory()->create();

    $user = User::factory()->create(['timezone' => 'UTC', 'department_id' => $department->getKey()]);

    grantServiceRequestsReportAccess($user);

    actingAs($user);

    $managerUserType = ServiceRequestType::factory()->create();
    $managerUserType->managerUsers()->attach($user);

    $auditorUserType = ServiceRequestType::factory()->create();
    $auditorUserType->auditorUsers()->attach($user);

    $managerDepartmentType = ServiceRequestType::factory()->create();
    $managerDepartmentType->managerDepartments()->attach($department);

    $auditorDepartmentType = ServiceRequestType::factory()->create();
    $auditorDepartmentType->auditorDepartments()->attach($department);

    $archivedAffiliatedType = ServiceRequestType::factory()->create();
    $archivedAffiliatedType->managerUsers()->attach($user);
    $archivedAffiliatedType->delete();

    $unaffiliatedType = ServiceRequestType::factory()->create();

    $ids = livewire(ServiceRequests::class)->instance()->getAffiliatedServiceRequestTypeIds();

    expect($ids)
        ->toEqualCanonicalizing([
            $managerUserType->getKey(),
            $auditorUserType->getKey(),
            $managerDepartmentType->getKey(),
            $auditorDepartmentType->getKey(),
        ])
        ->not->toContain($archivedAffiliatedType->getKey())
        ->not->toContain($unaffiliatedType->getKey());
});

it('populates the filter with affiliated types via the My Affiliated Types action', function () {
    $user = User::factory()->create(['timezone' => 'UTC']);

    grantServiceRequestsReportAccess($user);

    actingAs($user);

    $affiliatedType = ServiceRequestType::factory()->create();
    $affiliatedType->managerUsers()->attach($user);

    $archivedAffiliatedType = ServiceRequestType::factory()->create();
    $archivedAffiliatedType->managerUsers()->attach($user);
    $archivedAffiliatedType->delete();

    ServiceRequestType::factory()->create();

    livewire(ServiceRequests::class)
        ->callAction(TestAction::make('loadAffiliatedServiceRequestTypes')->schemaComponent('serviceRequestTypeActions', 'filtersForm'))
        ->assertSet('filters.serviceRequestTypes', [$affiliatedType->getKey()]);
});

it('disables clear action until at least one type is selected', function () {
    $user = User::factory()->create(['timezone' => 'UTC']);

    grantServiceRequestsReportAccess($user);

    actingAs($user);

    $type = ServiceRequestType::factory()->create();

    livewire(ServiceRequests::class)
        ->assertActionDisabled(TestAction::make('clearServiceRequestTypes')->schemaComponent('serviceRequestTypeActions', 'filtersForm'))
        ->set('filters.serviceRequestTypes', [$type->getKey()])
        ->assertActionEnabled(TestAction::make('clearServiceRequestTypes')->schemaComponent('serviceRequestTypeActions', 'filtersForm'));
});

it('clears the selected types via the Clear action', function () {
    $user = User::factory()->create(['timezone' => 'UTC']);

    grantServiceRequestsReportAccess($user);

    actingAs($user);

    $type = ServiceRequestType::factory()->create();

    livewire(ServiceRequests::class)
        ->set('filters.serviceRequestTypes', [$type->getKey()])
        ->callAction(TestAction::make('clearServiceRequestTypes')->schemaComponent('serviceRequestTypeActions', 'filtersForm'))
        ->assertSet('filters.serviceRequestTypes', []);
});

it('filters the service requests table by the selected types', function () {
    $typeA = ServiceRequestType::factory()->create(['name' => 'Type A']);
    $typeB = ServiceRequestType::factory()->create(['name' => 'Type B']);

    $priorityA = ServiceRequestPriority::factory()->state(['type_id' => $typeA->getKey()])->create();
    $priorityB = ServiceRequestPriority::factory()->state(['type_id' => $typeB->getKey()])->create();

    $status = ServiceRequestStatus::factory()->state([
        'classification' => SystemServiceRequestClassification::Open,
    ])->create();

    $requestA = ServiceRequest::factory()->state([
        'priority_id' => $priorityA->getKey(),
        'status_id' => $status->getKey(),
        'respondent_id' => Contact::factory(),
    ])->create();

    $requestB = ServiceRequest::factory()->state([
        'priority_id' => $priorityB->getKey(),
        'status_id' => $status->getKey(),
        'respondent_id' => Contact::factory(),
    ])->create();

    livewire(ServiceRequestsTable::class, [
        'cacheTag' => 'test-service-requests-table-type-filter',
        'pageFilters' => ['serviceRequestTypes' => [$typeA->getKey()]],
    ])
        ->assertCanSeeTableRecords(collect([$requestA]))
        ->assertCanNotSeeTableRecords(collect([$requestB]));

    livewire(ServiceRequestsTable::class, [
        'cacheTag' => 'test-service-requests-table-type-filter-all',
        'pageFilters' => [],
    ])
        ->assertCanSeeTableRecords(collect([$requestA, $requestB]));
});

it('filters the request types table by the selected types', function () {
    $typeA = ServiceRequestType::factory()->create(['name' => 'Type A']);
    $typeB = ServiceRequestType::factory()->create(['name' => 'Type B']);

    livewire(ServiceRequestTypesTable::class, [
        'cacheTag' => 'test-service-request-types-table-type-filter',
        'pageFilters' => ['serviceRequestTypes' => [$typeA->getKey()]],
    ])
        ->assertCanSeeTableRecords(collect([$typeA]))
        ->assertCanNotSeeTableRecords(collect([$typeB]));
});

it('respects the selected types in the service requests stats', function () {
    $typeA = ServiceRequestType::factory()->create();
    $typeB = ServiceRequestType::factory()->create();

    $priorityA = ServiceRequestPriority::factory()->state(['type_id' => $typeA->getKey()])->create();
    $priorityB = ServiceRequestPriority::factory()->state(['type_id' => $typeB->getKey()])->create();

    $status = ServiceRequestStatus::factory()->state([
        'classification' => SystemServiceRequestClassification::Open,
    ])->create();

    ServiceRequest::factory()->count(2)->state([
        'priority_id' => $priorityA->getKey(),
        'status_id' => $status->getKey(),
    ])->create();

    ServiceRequest::factory()->count(3)->state([
        'priority_id' => $priorityB->getKey(),
        'status_id' => $status->getKey(),
    ])->create();

    $widget = new ServiceRequestsStats();
    $widget->cacheTag = 'test-service-requests-stats-type-filter';
    $widget->pageFilters = ['serviceRequestTypes' => [$typeA->getKey()]];

    $stats = $widget->getStats();

    expect($stats[0]->getValue())->toEqual('2');
});

it('respects the selected types in the status distribution chart', function () {
    $typeA = ServiceRequestType::factory()->create();
    $typeB = ServiceRequestType::factory()->create();

    $priorityA = ServiceRequestPriority::factory()->state(['type_id' => $typeA->getKey()])->create();
    $priorityB = ServiceRequestPriority::factory()->state(['type_id' => $typeB->getKey()])->create();

    $status = ServiceRequestStatus::factory()->state([
        'name' => 'Open',
        'classification' => SystemServiceRequestClassification::Open,
    ])->create();

    ServiceRequest::factory()->count(2)->state([
        'priority_id' => $priorityA->getKey(),
        'status_id' => $status->getKey(),
    ])->create();

    ServiceRequest::factory()->count(3)->state([
        'priority_id' => $priorityB->getKey(),
        'status_id' => $status->getKey(),
    ])->create();

    $widget = new ServiceRequestStatusDistributionDonutChart();
    $widget->cacheTag = 'test-service-request-status-type-filter';
    $widget->pageFilters = ['serviceRequestTypes' => [$typeA->getKey()]];

    $data = $widget->getData();

    expect(array_sum($data['datasets'][0]['data']->all()))->toBe(2);
});

it('respects the selected types in the category distribution chart', function () {
    $typeA = ServiceRequestType::factory()->create();
    $typeB = ServiceRequestType::factory()->create();

    $priorityA = ServiceRequestPriority::factory()->state(['type_id' => $typeA->getKey()])->create();
    $priorityB = ServiceRequestPriority::factory()->state(['type_id' => $typeB->getKey()])->create();

    $status = ServiceRequestStatus::factory()->state([
        'classification' => SystemServiceRequestClassification::Open,
    ])->create();

    ServiceRequest::factory()->count(2)->state([
        'priority_id' => $priorityA->getKey(),
        'status_id' => $status->getKey(),
        'category' => ServiceRequestCategory::Incident,
    ])->create();

    ServiceRequest::factory()->count(3)->state([
        'priority_id' => $priorityB->getKey(),
        'status_id' => $status->getKey(),
        'category' => ServiceRequestCategory::Request,
    ])->create();

    $widget = new ServiceRequestCategoryDistributionDonutChart();
    $widget->cacheTag = 'test-service-request-category-type-filter';
    $widget->pageFilters = ['serviceRequestTypes' => [$typeA->getKey()]];

    $data = $widget->getData();

    expect($data['labels']->all())->toBe([(string) ServiceRequestCategory::Incident->getLabel()])
        ->and(array_sum($data['datasets'][0]['data']->all()))->toBe(2);
});

it('respects the selected types in the requests over time chart', function () {
    $typeA = ServiceRequestType::factory()->create();
    $typeB = ServiceRequestType::factory()->create();

    $priorityA = ServiceRequestPriority::factory()->state(['type_id' => $typeA->getKey()])->create();
    $priorityB = ServiceRequestPriority::factory()->state(['type_id' => $typeB->getKey()])->create();

    $status = ServiceRequestStatus::factory()->state([
        'classification' => SystemServiceRequestClassification::Open,
    ])->create();

    ServiceRequest::factory()->count(2)->state([
        'priority_id' => $priorityA->getKey(),
        'status_id' => $status->getKey(),
        'created_at' => now()->subMonth(),
    ])->create();

    ServiceRequest::factory()->count(3)->state([
        'priority_id' => $priorityB->getKey(),
        'status_id' => $status->getKey(),
        'created_at' => now()->subMonth(),
    ])->create();

    $widget = new ServiceRequestsOverTimeBarChart();
    $widget->cacheTag = 'test-service-requests-over-time-type-filter';
    $widget->pageFilters = ['serviceRequestTypes' => [$typeA->getKey()]];

    $data = $widget->getData();

    expect(array_sum($data['datasets'][0]['data']))->toBe(2);
});
