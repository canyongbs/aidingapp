<?php

use AidingApp\ServiceManagement\Models\ServiceRequestPriority;
use AidingApp\ServiceManagement\Models\ServiceRequestType;

it('cascade deletes priorities when a service request type is deleted', function () {
    $serviceRequestType = ServiceRequestType::factory()
        ->has(ServiceRequestPriority::factory()->count(3), 'priorities')
        ->create();

    expect($serviceRequestType->deleted_at)->toBeNull();

    foreach ($serviceRequestType->priorities as $priority) {
        expect($priority->deleted_at)->toBeNull();
    }

    $serviceRequestType->delete();

    $serviceRequestType->refresh();

    expect($serviceRequestType->deleted_at)->not()->toBeNull();

    foreach ($serviceRequestType->priorities as $priority) {
        expect($priority->deleted_at)->not()->toBeNull();
    }
});