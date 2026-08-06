<?php

use AidingApp\ServiceManagement\Models\ServiceRequestStatus;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use App\Features\AutomatedStatusChangeOnAssignmentFeature;

it('persists the automated status change columns and loads the relation', function () {
    $status = ServiceRequestStatus::factory()->open()->create();

    $type = ServiceRequestType::factory()->create([
        'is_automated_status_change_enabled' => true,
        'automated_status_id' => $status->getKey(),
    ]);

    expect($type->fresh()->is_automated_status_change_enabled)->toBeTrue()
        ->and($type->automatedStatus->is($status))->toBeTrue();
});

it('has the automated status change feature active because the migration activated it', function () {
    expect(AutomatedStatusChangeOnAssignmentFeature::active())->toBeTrue();
});
