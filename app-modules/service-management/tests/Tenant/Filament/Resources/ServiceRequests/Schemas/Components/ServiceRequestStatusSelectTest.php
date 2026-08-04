<?php

use AidingApp\ServiceManagement\Enums\SystemServiceRequestClassification;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequests\Schemas\Components\ServiceRequestStatusSelect;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestStatus;
use Filament\Forms\Components\Select;

it('builds a status_id select defaulting to the current service request status', function () {
    $status = ServiceRequestStatus::factory()->create([
        'classification' => SystemServiceRequestClassification::Open,
    ]);

    $serviceRequest = ServiceRequest::factory()->create([
        'status_id' => $status->getKey(),
    ]);

    $select = ServiceRequestStatusSelect::make($serviceRequest);

    expect($select)->toBeInstanceOf(Select::class)
        ->and($select->getName())->toBe('status_id')
        ->and($select->getDefaultState())->toBe($status->getKey());
});
