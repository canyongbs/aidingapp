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

use AidingApp\Report\Filament\Widgets\ServiceRequestCategoryDistributionDonutChart;
use AidingApp\ServiceManagement\Enums\ServiceRequestIssueCategory;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestPriority;
use AidingApp\ServiceManagement\Models\ServiceRequestStatus;
use AidingApp\ServiceManagement\Models\ServiceRequestType;

it('returns correct category distribution within the given date range', function () {
    $startDate = now()->subDays(10);
    $endDate = now()->subDays(5);

    $type = ServiceRequestType::factory()->create();
    $priority = ServiceRequestPriority::factory()->state(['type_id' => $type->id])->create();
    $status = ServiceRequestStatus::factory()->create();

    ServiceRequest::factory()->count(3)->state([
        'priority_id' => $priority->id,
        'status_id' => $status->id,
        'issue_category' => ServiceRequestIssueCategory::Incident,
        'created_at' => now()->subDays(7),
    ])->create();

    ServiceRequest::factory()->count(5)->state([
        'priority_id' => $priority->id,
        'status_id' => $status->id,
        'issue_category' => ServiceRequestIssueCategory::Request,
        'created_at' => now()->subDays(6),
    ])->create();

    // Outside date range — should not appear
    ServiceRequest::factory()->count(2)->state([
        'priority_id' => $priority->id,
        'status_id' => $status->id,
        'issue_category' => ServiceRequestIssueCategory::Incident,
        'created_at' => now()->subDays(20),
    ])->create();

    $widget = new ServiceRequestCategoryDistributionDonutChart();
    $widget->cacheTag = 'test-category-distribution-date-range';
    $widget->pageFilters = [
        'startDate' => $startDate->toDateString(),
        'endDate' => $endDate->toDateString(),
    ];

    $data = $widget->getData();

    expect($data)->toHaveKeys(['labels', 'datasets'])
        ->and($data['datasets'])->toHaveCount(1)
        ->and($data['datasets'][0])->toHaveKeys(['label', 'data', 'backgroundColor', 'hoverOffset']);

    $labels = $data['labels']->toArray();
    $counts = $data['datasets'][0]['data']->toArray();

    $incidentIndex = array_search(ServiceRequestIssueCategory::Incident->getLabel(), $labels);
    $requestIndex = array_search(ServiceRequestIssueCategory::Request->getLabel(), $labels);

    expect($incidentIndex)->not->toBeFalse()
        ->and($requestIndex)->not->toBeFalse()
        ->and($counts[$incidentIndex])->toBe(3)
        ->and($counts[$requestIndex])->toBe(5);
});

it('returns correct category distribution when no date filters are applied', function () {
    $type = ServiceRequestType::factory()->create();
    $priority = ServiceRequestPriority::factory()->state(['type_id' => $type->id])->create();
    $status = ServiceRequestStatus::factory()->create();

    ServiceRequest::factory()->count(4)->state([
        'priority_id' => $priority->id,
        'status_id' => $status->id,
        'issue_category' => ServiceRequestIssueCategory::Incident,
        'created_at' => now()->subDays(3),
    ])->create();

    ServiceRequest::factory()->count(7)->state([
        'priority_id' => $priority->id,
        'status_id' => $status->id,
        'issue_category' => ServiceRequestIssueCategory::Request,
        'created_at' => now()->subMonths(1),
    ])->create();

    $widget = new ServiceRequestCategoryDistributionDonutChart();
    $widget->cacheTag = 'test-category-distribution-no-filters';
    $widget->pageFilters = [];

    $data = $widget->getData();

    $labels = $data['labels']->toArray();
    $counts = $data['datasets'][0]['data']->toArray();

    $incidentIndex = array_search(ServiceRequestIssueCategory::Incident->getLabel(), $labels);
    $requestIndex = array_search(ServiceRequestIssueCategory::Request->getLabel(), $labels);

    expect($counts[$incidentIndex])->toBe(4)
        ->and($counts[$requestIndex])->toBe(7);
});

it('category page filter does not affect the chart — it always shows all categories', function () {
    $type = ServiceRequestType::factory()->create();
    $priority = ServiceRequestPriority::factory()->state(['type_id' => $type->id])->create();
    $status = ServiceRequestStatus::factory()->create();

    ServiceRequest::factory()->count(5)->state([
        'priority_id' => $priority->id,
        'status_id' => $status->id,
        'issue_category' => ServiceRequestIssueCategory::Incident,
        'created_at' => now()->subDays(2),
    ])->create();

    ServiceRequest::factory()->count(3)->state([
        'priority_id' => $priority->id,
        'status_id' => $status->id,
        'issue_category' => ServiceRequestIssueCategory::Request,
        'created_at' => now()->subDays(2),
    ])->create();

    $widget = new ServiceRequestCategoryDistributionDonutChart();
    $widget->cacheTag = 'test-category-distribution-category-filter';
    // Even with a stale category in pageFilters, the chart ignores it
    $widget->pageFilters = ['category' => ServiceRequestIssueCategory::Incident->value];

    $data = $widget->getData();

    $labels = $data['labels']->toArray();
    $counts = $data['datasets'][0]['data']->toArray();

    // Both categories must appear — chart is not filtered by category
    expect($labels)->toContain(ServiceRequestIssueCategory::Incident->getLabel())
        ->and($labels)->toContain(ServiceRequestIssueCategory::Request->getLabel());

    $incidentIndex = array_search(ServiceRequestIssueCategory::Incident->getLabel(), $labels);
    $requestIndex = array_search(ServiceRequestIssueCategory::Request->getLabel(), $labels);
    expect($counts[$incidentIndex])->toBe(5)
        ->and($counts[$requestIndex])->toBe(3);
});

it('excludes zero-count categories from the chart', function () {
    $type = ServiceRequestType::factory()->create();
    $priority = ServiceRequestPriority::factory()->state(['type_id' => $type->id])->create();
    $status = ServiceRequestStatus::factory()->create();

    // Only incidents — requests should not appear in chart
    ServiceRequest::factory()->count(2)->state([
        'priority_id' => $priority->id,
        'status_id' => $status->id,
        'issue_category' => ServiceRequestIssueCategory::Incident,
        'created_at' => now()->subDays(1),
    ])->create();

    $widget = new ServiceRequestCategoryDistributionDonutChart();
    $widget->cacheTag = 'test-category-distribution-zero-exclusion';
    $widget->pageFilters = [];

    $data = $widget->getData();

    $labels = $data['labels']->toArray();

    expect($labels)->toContain(ServiceRequestIssueCategory::Incident->getLabel())
        ->and($labels)->not->toContain(ServiceRequestIssueCategory::Request->getLabel());
});
