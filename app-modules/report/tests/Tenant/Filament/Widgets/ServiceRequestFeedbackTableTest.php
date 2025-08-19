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

    $type1 = ServiceRequestType::factory()->create();
    $type2 = ServiceRequestType::factory()->create();

    $priority1 = ServiceRequestPriority::factory()->state(['type_id' => $type1->id])->create();
    $priority2 = ServiceRequestPriority::factory()->state(['type_id' => $type2->id])->create();

    $request1 = ServiceRequest::factory()->state(['priority_id' => $priority1->id, 'created_at' => $startDate])->create();
    $request2 = ServiceRequest::factory()->state(['priority_id' => $priority2->id, 'created_at' => $endDate])->create();
    $request3 = ServiceRequest::factory()->state(['priority_id' => $priority1->id, 'created_at' => now()->subDays(20)])->create();

    $feedback1 = ServiceRequestFeedback::factory()->state([
        'service_request_id' => $request1->id,
        'created_at' => $startDate,
    ])->create();

    $feedback2 = ServiceRequestFeedback::factory()->state([
        'service_request_id' => $request2->id,
        'created_at' => $endDate,
    ])->create();

    $feedback3 = ServiceRequestFeedback::factory()->state([
        'service_request_id' => $request3->id,
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
            $feedback1,
            $feedback2,
        ]))
        ->assertCanNotSeeTableRecords(collect([
            $feedback3,
        ]));
});

it('filters service request feedbacks by date and type', function () {
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

    $feedback1 = ServiceRequestFeedback::factory()->state([
        'service_request_id' => $request1->id,
        'created_at' => $startDate,
    ])->create();

    $feedback2 = ServiceRequestFeedback::factory()->state([
        'service_request_id' => $request2->id,
        'created_at' => $endDate,
    ])->create();

    $feedback3 = ServiceRequestFeedback::factory()->state([
        'service_request_id' => $request3->id,
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
        ->assertSee($feedback1->serviceRequest->service_request_number)
        ->assertDontSee($feedback2->serviceRequest->service_request_number)
        ->assertDontSee($feedback3->serviceRequest->service_request_number);

    $filters = [
        'startDate' => $startDate->toDateString(),
        'endDate' => $endDate->toDateString(),
    ];

    livewire(ServiceRequestFeedbackTable::class, [
        'cacheTag' => 'report-service-request-feedback',
        'filters' => $filters,
    ])
        ->assertSee($feedback1->serviceRequest->service_request_number)
        ->assertSee($feedback2->serviceRequest->service_request_number)
        ->assertDontSee($feedback3->serviceRequest->service_request_number);

    livewire(ServiceRequestFeedbackTable::class, [
        'cacheTag' => 'report-service-request-feedback',
        'filters' => [],
    ])
        ->assertSee($feedback1->serviceRequest->service_request_number)
        ->assertSee($feedback2->serviceRequest->service_request_number)
        ->assertSee($feedback3->serviceRequest->service_request_number);
});
