<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Aiding App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Aiding App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    <https://www.canyongbs.com> or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

use AidingApp\ServiceManagement\Actions\ReopenServiceRequestAction;
use AidingApp\ServiceManagement\Enums\SystemServiceRequestClassification;
use AidingApp\ServiceManagement\Exceptions\NoOpenServiceRequestStatusFoundException;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestStatus;
use Illuminate\Support\Facades\Exceptions;

it('reopens a closed service request to the first open status ordered by sort', function () {
    // A system-protected 'New' status (Open, sort 0) is seeded for every tenant, so use a lower sort to be unambiguously first.
    $expectedOpenStatus = ServiceRequestStatus::factory()->open()->create(['sort' => -1]);

    ServiceRequestStatus::factory()->create([
        'classification' => SystemServiceRequestClassification::Open,
        'sort' => 999,
    ]);

    $closedStatus = ServiceRequestStatus::factory()->closed()->create();

    $serviceRequest = ServiceRequest::factory()->for($closedStatus, 'status')->create();

    app(ReopenServiceRequestAction::class)->execute($serviceRequest);

    $reopened = $serviceRequest->fresh();

    expect($reopened->status_id)->toBe($expectedOpenStatus->getKey())
        ->and($reopened->status->classification)->toBe(SystemServiceRequestClassification::Open);
});

it('does not change the status when the service request is not closed', function () {
    $inProgressStatus = ServiceRequestStatus::factory()->inProgress()->create();

    $serviceRequest = ServiceRequest::factory()->for($inProgressStatus, 'status')->create();

    app(ReopenServiceRequestAction::class)->execute($serviceRequest);

    expect($serviceRequest->fresh()->status_id)->toBe($inProgressStatus->getKey());
});

it('reports an error and leaves the status unchanged when no open status exists', function () {
    Exceptions::fake();

    // The seeded "New" status is system protected, so bypass row-protection triggers to clear all statuses.
    $connection = (new ServiceRequestStatus())->getConnection();
    $connection->statement('SET session_replication_role = replica');

    try {
        ServiceRequestStatus::query()->forceDelete();
    } finally {
        $connection->statement('SET session_replication_role = DEFAULT');
    }

    $closedStatus = ServiceRequestStatus::factory()->closed()->create();

    $serviceRequest = ServiceRequest::factory()->for($closedStatus, 'status')->create();

    app(ReopenServiceRequestAction::class)->execute($serviceRequest);

    expect($serviceRequest->fresh()->status_id)->toBe($closedStatus->getKey());

    Exceptions::assertReported(NoOpenServiceRequestStatusFoundException::class);
});
