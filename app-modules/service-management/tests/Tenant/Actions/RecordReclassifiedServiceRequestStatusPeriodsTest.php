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

use AidingApp\ServiceManagement\Actions\RecordReclassifiedServiceRequestStatusPeriods;
use AidingApp\ServiceManagement\Actions\RecordServiceRequestStatusPeriod;
use AidingApp\ServiceManagement\Enums\SystemServiceRequestClassification;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestStatus;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Notification;

it('appends a status period for every request currently in the reclassified status', function () {
    Notification::fake();

    $start = CarbonImmutable::parse('2026-01-01 00:00:00');

    $this->travelTo($start);

    $status = ServiceRequestStatus::factory()->inProgress()->create();

    $serviceRequests = ServiceRequest::factory()->count(2)->create(['status_id' => $status->getKey()]);

    // Simulate the reclassification: the status now resolves to a different classification.
    $status->classification = SystemServiceRequestClassification::Waiting;

    $reclassifiedAt = $start->addSeconds(100);

    (new RecordReclassifiedServiceRequestStatusPeriods($status, $reclassifiedAt))
        ->handle(app(RecordServiceRequestStatusPeriod::class));

    foreach ($serviceRequests as $serviceRequest) {
        $latestPeriod = $serviceRequest->statusPeriods()
            ->orderByDesc('started_at')
            ->orderByDesc('created_at')
            ->first();

        expect($latestPeriod->service_request_status_id)->toBe($status->getKey())
            ->and($latestPeriod->classification)->toBe(SystemServiceRequestClassification::Waiting)
            ->and($latestPeriod->started_at->toDateTimeString())->toBe($reclassifiedAt->toDateTimeString());
    }
});
