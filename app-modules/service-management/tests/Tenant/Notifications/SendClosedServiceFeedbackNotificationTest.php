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

use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestFeedback;
use AidingApp\ServiceManagement\Models\ServiceRequestStatus;
use AidingApp\ServiceManagement\Notifications\SendClosedServiceFeedbackNotification;
use Illuminate\Support\Facades\Notification;

it('is delayed by 120 seconds', function () {
    $notification = new SendClosedServiceFeedbackNotification(ServiceRequest::factory()->make(), null);

    expect($notification->delay)->toBe(120);
});

describe('shouldSend', function () {
    it('sends when the service request is still closed', function () {
        Notification::fake();

        $serviceRequest = ServiceRequest::factory()
            ->for(ServiceRequestStatus::factory()->closed(), 'status')
            ->create();

        $notification = new SendClosedServiceFeedbackNotification($serviceRequest, null);

        expect($notification->shouldSend($serviceRequest->respondent, 'mail'))->toBeTrue();
    });

    it('does not send when the service request was reopened after the notification was queued', function () {
        Notification::fake();

        $serviceRequest = ServiceRequest::factory()
            ->for(ServiceRequestStatus::factory()->closed(), 'status')
            ->create();

        $notification = new SendClosedServiceFeedbackNotification($serviceRequest, null);

        ServiceRequest::query()
            ->whereKey($serviceRequest->getKey())
            ->update(['status_id' => ServiceRequestStatus::factory()->open()->create()->getKey()]);

        expect($notification->shouldSend($serviceRequest->respondent, 'mail'))->toBeFalse();
    });

    it('does not send when feedback was already submitted', function () {
        Notification::fake();

        $serviceRequest = ServiceRequest::factory()
            ->for(ServiceRequestStatus::factory()->closed(), 'status')
            ->create();

        $notification = new SendClosedServiceFeedbackNotification($serviceRequest, null);

        ServiceRequestFeedback::factory()->for($serviceRequest, 'serviceRequest')->create();

        expect($notification->shouldSend($serviceRequest->respondent, 'mail'))->toBeFalse();
    });
});
