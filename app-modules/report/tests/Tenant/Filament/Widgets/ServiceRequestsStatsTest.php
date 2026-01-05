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

use AidingApp\Report\Filament\Widgets\ServiceRequestsStats;
use AidingApp\ServiceManagement\Enums\SystemServiceRequestClassification;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestPriority;
use AidingApp\ServiceManagement\Models\ServiceRequestStatus;
use AidingApp\ServiceManagement\Models\ServiceRequestType;

it('returns correct service request statistics within the given date range', function () {
    $startDate = now()->subDays(10);
    $endDate = now()->subDays(5);

    $type = ServiceRequestType::factory()->create();
    $priority = ServiceRequestPriority::factory()->state(['type_id' => $type->id])->create();

    $openStatus = ServiceRequestStatus::factory()->state([
        'classification' => SystemServiceRequestClassification::Open,
    ])->create();

    $closedStatus = ServiceRequestStatus::factory()->state([
        'classification' => SystemServiceRequestClassification::Closed,
    ])->create();

    $requestOne = ServiceRequest::factory()->state([
        'priority_id' => $priority->id,
        'status_id' => $openStatus->id,
        'created_at' => $startDate,
        'time_to_resolution' => 3600, // 1 hour
    ])->create();

    $requestTwo = ServiceRequest::factory()->state([
        'priority_id' => $priority->id,
        'status_id' => $closedStatus->id,
        'created_at' => $endDate,
        'time_to_resolution' => 7200, // 2 hours
    ])->create();

    $requestThree = ServiceRequest::factory()->state([
        'priority_id' => $priority->id,
        'status_id' => $openStatus->id,
        'created_at' => now()->subMonths(2),
        'time_to_resolution' => 10800, // 3 hours
    ])->create();

    $widget = new ServiceRequestsStats();
    $widget->cacheTag = 'test-service-requests-stats';
    $widget->filters = [
        'startDate' => $startDate->toDateString(),
        'endDate' => $endDate->toDateString(),
    ];

    $stats = $widget->getStats();

    $expectedTotal = 2;
    $expectedAvgResolution = (3600 + 7200) / 2; // 5400 seconds
    $expectedOpenRequests = 1;

    $hours = floor($expectedAvgResolution / 3600);
    $minutes = floor(($expectedAvgResolution % 3600) / 60);
    $expectedAvgFormat = "0d {$hours}h {$minutes}m";

    expect($stats)->toHaveCount(3)
        ->and($stats[0]->getValue())->toEqual((string) $expectedTotal) // Total Service Requests
        ->and($stats[1]->getValue())->toEqual($expectedAvgFormat) // Average Resolution Time
        ->and($stats[2]->getValue())->toEqual((string) $expectedOpenRequests); // Total Open Requests
});

it('returns correct statistics when no date filters are applied', function () {
    $type = ServiceRequestType::factory()->create();
    $priority = ServiceRequestPriority::factory()->state(['type_id' => $type->id])->create();

    $openStatus = ServiceRequestStatus::factory()->state([
        'classification' => SystemServiceRequestClassification::Open,
    ])->create();

    $closedStatus = ServiceRequestStatus::factory()->state([
        'classification' => SystemServiceRequestClassification::Closed,
    ])->create();

    ServiceRequest::factory()->count(3)->state([
        'priority_id' => $priority->id,
        'status_id' => $openStatus->id,
        'created_at' => now()->subDays(5),
        'time_to_resolution' => 3600, // 1 hour
    ])->create();

    ServiceRequest::factory()->count(2)->state([
        'priority_id' => $priority->id,
        'status_id' => $closedStatus->id,
        'created_at' => now()->subDays(2),
        'time_to_resolution' => 7200, // 2 hours
    ])->create();

    $widget = new ServiceRequestsStats();
    $widget->cacheTag = 'test-service-requests-stats-no-filters';
    $widget->filters = [];

    $stats = $widget->getStats();

    $expectedTotal = 5; // 3 open + 2 closed
    $expectedAvgResolution = (3 * 3600 + 2 * 7200) / 5; // 5040 seconds
    $expectedOpenRequests = 3; // 3 open requests

    $hours = floor($expectedAvgResolution / 3600);
    $minutes = floor(($expectedAvgResolution % 3600) / 60);
    $expectedAvgFormat = "0d {$hours}h {$minutes}m";

    expect($stats)->toHaveCount(3)
        ->and($stats[0]->getValue())->toEqual((string) $expectedTotal) // Total Service Requests
        ->and($stats[1]->getValue())->toEqual($expectedAvgFormat) // Average Resolution Time
        ->and($stats[2]->getValue())->toEqual((string) $expectedOpenRequests); // Total Open Requests
});
