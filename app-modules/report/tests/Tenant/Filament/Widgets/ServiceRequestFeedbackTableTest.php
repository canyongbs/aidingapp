<?php

use AidingApp\Report\Filament\Widgets\ServiceRequestFeedbackTable;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestFeedback;
use AidingApp\ServiceManagement\Models\ServiceRequestPriority;
use AidingApp\ServiceManagement\Models\ServiceRequestType;

use function Pest\Livewire\livewire;

it('returns service request feedbacks within the given date range', function () {
    $startDate = now()->subDays(10);
    $endDate = now()->subDays(5);

    $typeOne = ServiceRequestType::factory()->create();
    $typeTwo = ServiceRequestType::factory()->create();

    $priorityOne = ServiceRequestPriority::factory()->state(['type_id' => $typeOne->id])->create();
    $priorityTwo = ServiceRequestPriority::factory()->state(['type_id' => $typeTwo->id])->create();

    $requestOne = ServiceRequest::factory()->state(['priority_id' => $priorityOne->id, 'created_at' => $startDate])->create();
    $requestTwo = ServiceRequest::factory()->state(['priority_id' => $priorityTwo->id, 'created_at' => $endDate])->create();
    $requestThree = ServiceRequest::factory()->state(['priority_id' => $priorityOne->id, 'created_at' => now()->subDays(20)])->create();

    $feedbackOne = ServiceRequestFeedback::factory()->state([
        'service_request_id' => $requestOne->id,
        'created_at' => $startDate,
    ])->create();

    $feedbackTwo = ServiceRequestFeedback::factory()->state([
        'service_request_id' => $requestTwo->id,
        'created_at' => $endDate,
    ])->create();

    $feedbackThree = ServiceRequestFeedback::factory()->state([
        'service_request_id' => $requestThree->id,
        'created_at' => now()->subDays(20),
    ])->create();

    $filters = [
        'startDate' => $startDate->toDateString(),
        'endDate' => $endDate->toDateString(),
    ];

    livewire(ServiceRequestFeedbackTable::class, [
        'cacheTag' => 'report-service-request-feedback',
        'filters' => $filters,
    ])
        ->assertCanSeeTableRecords(collect([
            $feedbackOne,
            $feedbackTwo,
        ]))
        ->assertCanNotSeeTableRecords(collect([
            $feedbackThree,
        ]));
});

it('filters service request feedbacks by date and type', function () {
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

    $feedbackOne = ServiceRequestFeedback::factory()->state([
        'service_request_id' => $requestOne->id,
        'created_at' => $startDate,
    ])->create();

    $feedbackTwo = ServiceRequestFeedback::factory()->state([
        'service_request_id' => $requestTwo->id,
        'created_at' => $endDate,
    ])->create();

    $feedbackThree = ServiceRequestFeedback::factory()->state([
        'service_request_id' => $requestThree->id,
        'created_at' => now()->subDays(20),
    ])->create();

    $filters = [
        'startDate' => $startDate->toDateString(),
        'endDate' => $endDate->toDateString(),
        'serviceRequestTypes' => [$typeA->id],
    ];

    livewire(ServiceRequestFeedbackTable::class, [
        'cacheTag' => 'report-service-request-feedback',
        'filters' => $filters,
    ])
        ->assertSee($feedbackOne->serviceRequest->service_request_number)
        ->assertDontSee($feedbackTwo->serviceRequest->service_request_number)
        ->assertDontSee($feedbackThree->serviceRequest->service_request_number);

    $filters = [
        'startDate' => $startDate->toDateString(),
        'endDate' => $endDate->toDateString(),
    ];

    livewire(ServiceRequestFeedbackTable::class, [
        'cacheTag' => 'report-service-request-feedback',
        'filters' => $filters,
    ])
        ->assertSee($feedbackOne->serviceRequest->service_request_number)
        ->assertSee($feedbackTwo->serviceRequest->service_request_number)
        ->assertDontSee($feedbackThree->serviceRequest->service_request_number);

    livewire(ServiceRequestFeedbackTable::class, [
        'cacheTag' => 'report-service-request-feedback',
        'filters' => [],
    ])
        ->assertSee($feedbackOne->serviceRequest->service_request_number)
        ->assertSee($feedbackTwo->serviceRequest->service_request_number)
        ->assertSee($feedbackThree->serviceRequest->service_request_number);
});
