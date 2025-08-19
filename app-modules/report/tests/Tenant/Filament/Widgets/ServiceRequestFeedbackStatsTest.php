<?php

use AidingApp\Report\Filament\Widgets\ServiceRequestFeedbackStats;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestFeedback;
use AidingApp\ServiceManagement\Models\ServiceRequestPriority;
use AidingApp\ServiceManagement\Models\ServiceRequestType;

it('returns correct service request statistics within the given date range', function () {
    $startDate = now()->subDays(10);
    $endDate = now()->subDays(5);

    $type1 = ServiceRequestType::factory()->create();
    $type2 = ServiceRequestType::factory()->create();

    $priority1 = ServiceRequestPriority::factory()->state(['type_id' => $type1->id])->create();
    $priority2 = ServiceRequestPriority::factory()->state(['type_id' => $type2->id])->create();

    $request1 = ServiceRequest::factory()->state(['priority_id' => $priority1->id, 'created_at' => $startDate])->create();
    $request2 = ServiceRequest::factory()->state(['priority_id' => $priority2->id, 'created_at' => $endDate])->create();
    $request3 = ServiceRequest::factory()->state(['priority_id' => $priority1->id, 'created_at' => now()->subDays(20)])->create();

    ServiceRequestFeedback::factory()->state([
        'service_request_id' => $request1->id,
        'created_at' => $startDate,
        'csat_answer' => 4,
        'nps_answer' => 8,
    ])->create();

    ServiceRequestFeedback::factory()->state([
        'service_request_id' => $request2->id,
        'created_at' => $endDate,
        'csat_answer' => 5,
        'nps_answer' => 9,
    ])->create();

    ServiceRequestFeedback::factory()->state([
        'service_request_id' => $request3->id,
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

    $request1 = ServiceRequest::factory()->state([
        'priority_id' => $priorityA->id,
        'created_at' => $startDate,
    ])->create();

    $request2 = ServiceRequest::factory()->state([
        'priority_id' => $priorityB->id,
        'created_at' => $endDate,
    ])->create();

    $request3 = ServiceRequest::factory()->state([
        'priority_id' => $priorityA->id,
        'created_at' => now()->subDays(20),
    ])->create();

    ServiceRequestFeedback::factory()->state([
        'service_request_id' => $request1->id,
        'created_at' => $startDate,
        'csat_answer' => 4,
        'nps_answer' => 7,
    ])->create();

    ServiceRequestFeedback::factory()->state([
        'service_request_id' => $request2->id,
        'created_at' => $endDate,
        'csat_answer' => 3,
        'nps_answer' => 6,
    ])->create();

    ServiceRequestFeedback::factory()->state([
        'service_request_id' => $request3->id,
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

    $widget2 = new ServiceRequestFeedbackStats();
    $widget2->cacheTag = 'test-service-request-feedback-stats-2';
    $widget2->filters = [
        'startDate' => $startDate->toDateString(),
        'endDate' => $endDate->toDateString(),
    ];

    $stats2 = $widget2->getStats();

    expect($stats2)->toHaveCount(4)
        ->and($stats2[0]->getValue())->toEqual('2')
        ->and($stats2[1]->getValue())->toEqual('2')
        ->and($stats2[2]->getValue())->toEqual('3.5')
        ->and($stats2[3]->getValue())->toEqual('6.5');

    $widget3 = new ServiceRequestFeedbackStats();
    $widget3->cacheTag = 'test-service-request-feedback-stats-3';
    $widget3->filters = [];

    $stats3 = $widget3->getStats();

    expect($stats3)->toHaveCount(4)
        ->and($stats3[0]->getValue())->toEqual('3')
        ->and($stats3[1]->getValue())->toEqual('3')
        ->and($stats3[2]->getValue())->toEqual('4')
        ->and($stats3[3]->getValue())->toEqual('7.33');
});
