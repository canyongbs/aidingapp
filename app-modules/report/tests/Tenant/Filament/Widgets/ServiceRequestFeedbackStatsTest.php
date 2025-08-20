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

use AidingApp\Report\Filament\Widgets\ServiceRequestFeedbackStats;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestFeedback;
use AidingApp\ServiceManagement\Models\ServiceRequestPriority;
use AidingApp\ServiceManagement\Models\ServiceRequestType;

it('returns correct service request statistics within the given date range', function () {
    $startDate = now()->subDays(10);
    $endDate = now()->subDays(5);

    $typeOne = ServiceRequestType::factory()->create();
    $typeTwo = ServiceRequestType::factory()->create();

    $priorityOne = ServiceRequestPriority::factory()->state(['type_id' => $typeOne->id])->create();
    $priorityTwo = ServiceRequestPriority::factory()->state(['type_id' => $typeTwo->id])->create();

    $requestOne = ServiceRequest::factory()->state(['priority_id' => $priorityOne->id, 'created_at' => $startDate])->create();
    $requestTwo = ServiceRequest::factory()->state(['priority_id' => $priorityTwo->id, 'created_at' => $endDate])->create();
    $requestThree = ServiceRequest::factory()->state(['priority_id' => $priorityOne->id, 'created_at' => now()->subDays(20)])->create();

    ServiceRequestFeedback::factory()->state([
        'service_request_id' => $requestOne->id,
        'created_at' => $startDate,
        'csat_answer' => 4,
        'nps_answer' => 8,
    ])->create();

    ServiceRequestFeedback::factory()->state([
        'service_request_id' => $requestTwo->id,
        'created_at' => $endDate,
        'csat_answer' => 5,
        'nps_answer' => 9,
    ])->create();

    ServiceRequestFeedback::factory()->state([
        'service_request_id' => $requestThree->id,
        'created_at' => now()->subDays(20),
        'csat_answer' => 3,
        'nps_answer' => 6,
    ])->create();

    $widget = new ServiceRequestFeedbackStats();
    $widget->cacheTag = 'report-service-request-feedback';
    $widget->filters = [
        'startDate' => $startDate->toDateString(),
        'endDate' => $endDate->toDateString(),
    ];

    $stats = $widget->getStats();

    expect($stats)->toHaveCount(4)
        ->and($stats[0]->getValue())->toEqual('2')
        ->and($stats[1]->getValue())->toEqual('2')
        ->and($stats[2]->getValue())->toEqual('4.5')
        ->and($stats[3]->getValue())->toEqual('8.5');
});

it('returns correct statistics filtered by date range and service request types', function () {
    $startDate = now()->subDays(10);
    $endDate = now()->subDays(5);

    $typeA = ServiceRequestType::factory()->create(['name' => 'Type A']);
    $typeB = ServiceRequestType::factory()->create(['name' => 'Type B']);

    $priorityA = ServiceRequestPriority::factory()->state(['type_id' => $typeA->id])->create();
    $priorityB = ServiceRequestPriority::factory()->state(['type_id' => $typeB->id])->create();

    $requestOne = ServiceRequest::factory()->state([
        'priority_id' => $priorityA->id,
        'created_at' => $startDate,
    ])->create();

    $requestTwo = ServiceRequest::factory()->state([
        'priority_id' => $priorityB->id,
        'created_at' => $endDate,
    ])->create();

    $requestThree = ServiceRequest::factory()->state([
        'priority_id' => $priorityA->id,
        'created_at' => now()->subDays(20),
    ])->create();

    ServiceRequestFeedback::factory()->state([
        'service_request_id' => $requestOne->id,
        'created_at' => $startDate,
        'csat_answer' => 4,
        'nps_answer' => 7,
    ])->create();

    ServiceRequestFeedback::factory()->state([
        'service_request_id' => $requestTwo->id,
        'created_at' => $endDate,
        'csat_answer' => 3,
        'nps_answer' => 6,
    ])->create();

    ServiceRequestFeedback::factory()->state([
        'service_request_id' => $requestThree->id,
        'created_at' => now()->subDays(20),
        'csat_answer' => 5,
        'nps_answer' => 9,
    ])->create();

    $widget = new ServiceRequestFeedbackStats();
    $widget->cacheTag = 'test-service-request-feedback-stats';
    $widget->filters = [
        'startDate' => $startDate->toDateString(),
        'endDate' => $endDate->toDateString(),
        'serviceRequestTypes' => [$typeA->id],
    ];

    $stats = $widget->getStats();

    expect($stats)->toHaveCount(4)
        ->and($stats[0]->getValue())->toEqual('1')
        ->and($stats[1]->getValue())->toEqual('1')
        ->and($stats[2]->getValue())->toEqual('4')
        ->and($stats[3]->getValue())->toEqual('7');

    $widgetTwo = new ServiceRequestFeedbackStats();
    $widgetTwo->cacheTag = 'test-service-request-feedback-stats-2';
    $widgetTwo->filters = [
        'startDate' => $startDate->toDateString(),
        'endDate' => $endDate->toDateString(),
    ];

    $stats2 = $widgetTwo->getStats();

    expect($stats2)->toHaveCount(4)
        ->and($stats2[0]->getValue())->toEqual('2')
        ->and($stats2[1]->getValue())->toEqual('2')
        ->and($stats2[2]->getValue())->toEqual('3.5')
        ->and($stats2[3]->getValue())->toEqual('6.5');

    $widgetThree = new ServiceRequestFeedbackStats();
    $widgetThree->cacheTag = 'test-service-request-feedback-stats-3';
    $widgetThree->filters = [];

    $stats3 = $widgetThree->getStats();

    expect($stats3)->toHaveCount(4)
        ->and($stats3[0]->getValue())->toEqual('3')
        ->and($stats3[1]->getValue())->toEqual('3')
        ->and($stats3[2]->getValue())->toEqual('4')
        ->and($stats3[3]->getValue())->toEqual('7.33');
});
