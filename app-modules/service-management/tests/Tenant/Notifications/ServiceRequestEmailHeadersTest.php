<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

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

use AidingApp\Notification\DataTransferObjects\NotificationResultData;
use AidingApp\Notification\Models\EmailMessage;
use AidingApp\Notification\Models\OutboundEmailMessageId;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Notifications\SendEducatableServiceRequestOpenedNotification;
use App\Features\ServiceRequestEmailThreading;
use App\Models\Tenant;

it('sets the from address to the tenant SR address', function () {
    $tenant = Tenant::current();

    $serviceRequest = ServiceRequest::factory()->create();

    $notification = new SendEducatableServiceRequestOpenedNotification($serviceRequest, null);

    $mailMessage = $notification->toMail($serviceRequest->respondent);

    $emailMessage = EmailMessage::factory()->create();

    $notification->customizeOutboundMail($mailMessage, $emailMessage, $serviceRequest->respondent);

    $expectedAddress = $tenant->getServiceRequestFromAddress();

    expect($mailMessage->from[0])->toBe($expectedAddress)
        ->and($mailMessage->from[1])->toBe(config('mail.from.name'));
});

it('sets the reply-to address to the tenant SR address', function () {
    $tenant = Tenant::current();

    $serviceRequest = ServiceRequest::factory()->create();

    $notification = new SendEducatableServiceRequestOpenedNotification($serviceRequest, null);

    $mailMessage = $notification->toMail($serviceRequest->respondent);

    $emailMessage = EmailMessage::factory()->create();

    $notification->customizeOutboundMail($mailMessage, $emailMessage, $serviceRequest->respondent);

    $expectedAddress = $tenant->getServiceRequestFromAddress();

    expect($mailMessage->replyTo)->toHaveCount(1)
        ->and($mailMessage->replyTo[0][0])->toBe($expectedAddress);
});

it('generates a custom Message-ID and saves outbound_message_id', function () {
    $serviceRequest = ServiceRequest::factory()->create();

    $notification = new SendEducatableServiceRequestOpenedNotification($serviceRequest, null);

    $mailMessage = $notification->toMail($serviceRequest->respondent);

    $emailMessage = EmailMessage::factory()->create();

    $notification->customizeOutboundMail($mailMessage, $emailMessage, $serviceRequest->respondent);

    $emailMessage->refresh();

    expect($emailMessage->outbound_message_id)->not->toBeNull()
        ->and($emailMessage->outbound_message_id)->toContain($serviceRequest->service_request_number)
        ->and($emailMessage->outbound_message_id)->toContain(config('mail.from.root_domain'));
});

it('creates an OutboundEmailMessageId record after successful send', function () {
    $serviceRequest = ServiceRequest::factory()->create();

    $notification = new SendEducatableServiceRequestOpenedNotification($serviceRequest, null);

    $mailMessage = $notification->toMail($serviceRequest->respondent);

    $emailMessage = EmailMessage::factory()->create();

    $notification->customizeOutboundMail($mailMessage, $emailMessage, $serviceRequest->respondent);

    $result = new NotificationResultData(success: true);

    $notification->afterSend($serviceRequest->respondent, $emailMessage, $result);

    expect(OutboundEmailMessageId::count())->toBe(1);

    $outbound = OutboundEmailMessageId::first();

    expect($outbound->message_id)->toBe($emailMessage->outbound_message_id)
        ->and($outbound->trackable_id)->toBe($serviceRequest->getKey())
        ->and($outbound->trackable_type)->toBe($serviceRequest->getMorphClass());
});

it('does not create an OutboundEmailMessageId record after failed send', function () {
    $serviceRequest = ServiceRequest::factory()->create();

    $notification = new SendEducatableServiceRequestOpenedNotification($serviceRequest, null);

    $mailMessage = $notification->toMail($serviceRequest->respondent);

    $emailMessage = EmailMessage::factory()->create();

    $notification->customizeOutboundMail($mailMessage, $emailMessage, $serviceRequest->respondent);

    $result = new NotificationResultData(success: false);

    $notification->afterSend($serviceRequest->respondent, $emailMessage, $result);

    expect(OutboundEmailMessageId::count())->toBe(0);
});

it('builds a References chain from existing outbound message IDs', function () {
    $serviceRequest = ServiceRequest::factory()->create();

    // Create some existing outbound message IDs
    $serviceRequest->outboundEmailMessageIds()->create([
        'message_id' => 'SR-FIRST.1.1000@mail.aiding.app',
    ]);

    $serviceRequest->outboundEmailMessageIds()->create([
        'message_id' => 'SR-FIRST.2.2000@mail.aiding.app',
    ]);

    $notification = new SendEducatableServiceRequestOpenedNotification($serviceRequest, null);

    $mailMessage = $notification->toMail($serviceRequest->respondent);

    $emailMessage = EmailMessage::factory()->create();

    $notification->customizeOutboundMail($mailMessage, $emailMessage, $serviceRequest->respondent);

    // The Message-ID should be the 3rd in sequence
    expect($emailMessage->outbound_message_id)->toContain('.3.');
});

// TODO: FeatureFlag Cleanup - This test can be removed when ServiceRequestEmailThreading is removed
it('does not set custom Message-ID or References when feature flag is disabled', function () {
    ServiceRequestEmailThreading::deactivate();

    $serviceRequest = ServiceRequest::factory()->create();

    $notification = new SendEducatableServiceRequestOpenedNotification($serviceRequest, null);

    $mailMessage = $notification->toMail($serviceRequest->respondent);

    $emailMessage = EmailMessage::factory()->create();

    $notification->customizeOutboundMail($mailMessage, $emailMessage, $serviceRequest->respondent);

    $emailMessage->refresh();

    // From and reply-to should still be set
    $tenant = Tenant::current();
    $expectedAddress = $tenant->getServiceRequestFromAddress();

    expect($mailMessage->from[0])->toBe($expectedAddress)
        ->and($emailMessage->outbound_message_id)->toBeNull();

    // afterSend should not create record
    $result = new NotificationResultData(success: true);

    $notification->afterSend($serviceRequest->respondent, $emailMessage, $result);

    expect(OutboundEmailMessageId::count())->toBe(0);
});
