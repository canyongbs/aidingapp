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

use AidingApp\Report\Filament\Widgets\ServiceRequestsOverTimeBarChart;
use AidingApp\ServiceManagement\Enums\SystemServiceRequestClassification;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestPriority;
use AidingApp\ServiceManagement\Models\ServiceRequestStatus;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use CanyonGBS\Common\Enums\Color;

it('returns correct service request data within the given date range', function () {
    $startDate = now()->subMonths(3)->startOfMonth();
    $endDate = now()->subMonths(1)->endOfMonth();

    $type = ServiceRequestType::factory()->create();

    $priority = ServiceRequestPriority::factory()->state(['type_id' => $type->id])->create();

    $status = ServiceRequestStatus::factory()->state([
        'name' => SystemServiceRequestClassification::Open->getLabel(),
        'classification' => SystemServiceRequestClassification::Open,
    ])->create();

    ServiceRequest::factory()->count(3)->state([
        'priority_id' => $priority->id,
        'status_id' => $status->id,
        'created_at' => $startDate->copy()->addDays(5),
    ])->create();

    ServiceRequest::factory()->count(2)->state([
        'priority_id' => $priority->id,
        'status_id' => $status->id,
        'created_at' => $startDate->copy()->addMonths(1)->addDays(10),
    ])->create();

    ServiceRequest::factory()->count(4)->state([
        'priority_id' => $priority->id,
        'status_id' => $status->id,
        'created_at' => $startDate->copy()->addMonths(2)->addDays(15),
    ])->create();

    ServiceRequest::factory()->count(5)->state([
        'priority_id' => $priority->id,
        'status_id' => $status->id,
        'created_at' => now()->subYears(1),
    ])->create();

    $widget = new ServiceRequestsOverTimeBarChart();
    $widget->cacheTag = 'test-service-requests-over-time-bar-chart';
    $widget->filters = [
        'startDate' => $startDate->toDateString(),
        'endDate' => $endDate->toDateString(),
    ];

    $data = $widget->getData();

    expect($data)->toHaveKeys(['datasets', 'labels'])
        ->and($data['datasets'])->toHaveCount(1)
        ->and($data['datasets'][0])->toHaveKeys(['label', 'data'])
        ->and($data['datasets'][0]['label'])->toBe('Service requests');

    $labels = $data['labels'];
    $counts = $data['datasets'][0]['data'];

    expect($labels)->toBeArray()
        ->and($counts)->toBeArray()
        ->and(count($labels))->toBe(count($counts));

    $expectedMonth1 = $startDate->format('M Y');
    $expectedMonth2 = $startDate->copy()->addMonth()->format('M Y');
    $expectedMonth3 = $startDate->copy()->addMonths(2)->format('M Y');

    expect($labels)->toContain($expectedMonth1, $expectedMonth2, $expectedMonth3);

    $month1Index = array_search($expectedMonth1, $labels);
    $month2Index = array_search($expectedMonth2, $labels);
    $month3Index = array_search($expectedMonth3, $labels);

    expect($counts[$month1Index])->toBe(3)
        ->and($counts[$month2Index])->toBe(2)
        ->and($counts[$month3Index])->toBe(4);
});

it('returns correct data when no date filters are applied', function () {
    $type = ServiceRequestType::factory()->create();

    $priority = ServiceRequestPriority::factory()->state(['type_id' => $type->id])->create();

    $status = ServiceRequestStatus::factory()->state([
        'name' => SystemServiceRequestClassification::Open->getLabel(),
        'classification' => SystemServiceRequestClassification::Open,
        'color' => Color::Blue,
    ])->create();

    ServiceRequest::factory()->count(2)->state([
        'priority_id' => $priority->id,
        'status_id' => $status->id,
        'created_at' => now()->startOfMonth()->subMonths(2)->addDays(5),
    ])->create();

    ServiceRequest::factory()->count(3)->state([
        'priority_id' => $priority->id,
        'status_id' => $status->id,
        'created_at' => now()->startOfMonth()->subMonths(4)->addDays(10),
    ])->create();

    ServiceRequest::factory()->count(1)->state([
        'priority_id' => $priority->id,
        'status_id' => $status->id,
        'created_at' => now()->startOfMonth()->subMonths(6)->addDays(15),
    ])->create();

    ServiceRequest::factory()->count(10)->state([
        'priority_id' => $priority->id,
        'status_id' => $status->id,
        'created_at' => now()->subYears(2),
    ])->create();

    $widget = new ServiceRequestsOverTimeBarChart();
    $widget->cacheTag = 'test-service-requests-over-time-bar-chart-no-filters';
    $widget->filters = [];

    $data = $widget->getData();

    expect($data)->toHaveKeys(['datasets', 'labels'])
        ->and($data['datasets'])->toHaveCount(1)
        ->and($data['datasets'][0]['label'])->toBe('Service requests');

    $labels = $data['labels'];
    $counts = $data['datasets'][0]['data'];

    expect($labels)->toHaveCount(12)
        ->and($counts)->toHaveCount(12)
        ->and($labels)->each->toMatch('/^[A-Z][a-z]{2} \d{4}$/');

    $month2Ago = now()->subMonths(2)->format('M Y');
    $month4Ago = now()->subMonths(4)->format('M Y');
    $month6Ago = now()->subMonths(6)->format('M Y');

    $month2Index = array_search($month2Ago, $labels);
    $month4Index = array_search($month4Ago, $labels);
    $month6Index = array_search($month6Ago, $labels);

    if ($month2Index !== false) {
        expect($counts[$month2Index])->toBe(2);
    }

    if ($month4Index !== false) {
        expect($counts[$month4Index])->toBe(3);
    }

    if ($month6Index !== false) {
        expect($counts[$month6Index])->toBe(1);
    }

    // Total count should be 6 (excluding the requests from 2 years ago)
    expect(array_sum($counts))->toBe(6);
});
