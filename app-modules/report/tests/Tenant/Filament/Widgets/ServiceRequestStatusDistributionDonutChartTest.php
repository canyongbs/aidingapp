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

use AidingApp\Report\Filament\Widgets\ServiceRequestStatusDistributionDonutChart;
use AidingApp\ServiceManagement\Enums\SystemServiceRequestClassification;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestPriority;
use AidingApp\ServiceManagement\Models\ServiceRequestStatus;
use AidingApp\ServiceManagement\Models\ServiceRequestType;

it('returns correct service request status distribution within the given date range', function () {
    $startDate = now()->subDays(10);
    $endDate = now()->subDays(5);

    $type = ServiceRequestType::factory()->create();
    $priority = ServiceRequestPriority::factory()->state(['type_id' => $type->id])->create();

    $openStatus = ServiceRequestStatus::factory()->state([
        'name' => SystemServiceRequestClassification::Open->getLabel(),
        'classification' => SystemServiceRequestClassification::Open,
    ])->create();

    $inProgressStatus = ServiceRequestStatus::factory()->state([
        'name' => SystemServiceRequestClassification::InProgress->getLabel(),
        'classification' => SystemServiceRequestClassification::InProgress,
    ])->create();

    $closedStatus = ServiceRequestStatus::factory()->state([
        'name' => SystemServiceRequestClassification::Closed->getLabel(),
        'classification' => SystemServiceRequestClassification::Closed,
    ])->create();

    ServiceRequest::factory()->count(3)->state([
        'priority_id' => $priority->id,
        'status_id' => $openStatus->id,
        'created_at' => $startDate,
    ])->create();

    ServiceRequest::factory()->count(2)->state([
        'priority_id' => $priority->id,
        'status_id' => $inProgressStatus->id,
        'created_at' => $endDate,
    ])->create();

    ServiceRequest::factory()->count(1)->state([
        'priority_id' => $priority->id,
        'status_id' => $closedStatus->id,
        'created_at' => now()->subDays(7),
    ])->create();

    ServiceRequest::factory()->count(2)->state([
        'priority_id' => $priority->id,
        'status_id' => $openStatus->id,
        'created_at' => now()->subMonths(2),
    ])->create();

    $widget = new ServiceRequestStatusDistributionDonutChart();
    $widget->cacheTag = 'test-service-request-status-distribution';
    $widget->filters = [
        'startDate' => $startDate->toDateString(),
        'endDate' => $endDate->toDateString(),
    ];

    $data = $widget->getData();

    expect($data)->toHaveKeys(['labels', 'datasets'])
        ->and($data['datasets'])->toHaveCount(1)
        ->and($data['datasets'][0])->toHaveKeys(['label', 'data', 'backgroundColor', 'hoverOffset']);

    $labels = $data['labels']->toArray();
    $counts = $data['datasets'][0]['data']->toArray();

    expect($labels)->toContain(
        SystemServiceRequestClassification::Open->getLabel(),
        SystemServiceRequestClassification::InProgress->getLabel(),
        SystemServiceRequestClassification::Closed->getLabel()
    );

    $openIndex = array_search(SystemServiceRequestClassification::Open->getLabel(), $labels);
    $inProgressIndex = array_search(SystemServiceRequestClassification::InProgress->getLabel(), $labels);
    $closedIndex = array_search(SystemServiceRequestClassification::Closed->getLabel(), $labels);

    expect($counts[$openIndex])->toBe(3)
        ->and($counts[$inProgressIndex])->toBe(2)
        ->and($counts[$closedIndex])->toBe(1);
});

it('returns correct distribution when no date filters are applied', function () {
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

    ServiceRequest::factory()->count(5)->state([
        'priority_id' => $priority->id,
        'status_id' => $openStatus->id,
        'created_at' => now()->subDays(5),
    ])->create();

    ServiceRequest::factory()->count(3)->state([
        'priority_id' => $priority->id,
        'status_id' => $closedStatus->id,
        'created_at' => now()->subMonths(1),
    ])->create();

    $widget = new ServiceRequestStatusDistributionDonutChart();
    $widget->cacheTag = 'test-service-request-status-distribution-no-filters';
    $widget->filters = [];

    $data = $widget->getData();

    expect($data)->toHaveKeys(['labels', 'datasets']);

    $labels = $data['labels']->toArray();
    $counts = $data['datasets'][0]['data']->toArray();

    $openIndex = array_search(SystemServiceRequestClassification::Open->getLabel(), $labels);
    $closedIndex = array_search(SystemServiceRequestClassification::Closed->getLabel(), $labels);

    expect($counts[$openIndex])->toBe(5)
        ->and($counts[$closedIndex])->toBe(3);
});
