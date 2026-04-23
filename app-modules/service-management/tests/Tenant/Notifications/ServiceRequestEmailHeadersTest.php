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

use AidingApp\Notification\DataTransferObjects\EmailChannelResultData;
use AidingApp\Notification\DataTransferObjects\NotificationResultData;
use AidingApp\Notification\Models\EmailMessage;
use AidingApp\Notification\Models\OutboundEmailMessageId;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Notifications\SendEducatableServiceRequestOpenedNotification;
use App\Models\Tenant;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Support\Facades\Event;

describe('Unit-level customizeOutboundMail', function () {
    it('sets the from address to the plus-addressed SR address', function () {
        $tenant = Tenant::current();

        $serviceRequest = ServiceRequest::factory()->create();

        $notification = new SendEducatableServiceRequestOpenedNotification($serviceRequest, null);

        $mailMessage = $notification->toMail($serviceRequest->respondent);

        $emailMessage = EmailMessage::factory()->create();

        $notification->customizeOutboundMail($mailMessage, $emailMessage, $serviceRequest->respondent);

        $expectedAddress = sprintf(
            '%s-sr+%s@%s',
            $tenant->getSubdomain(),
            $serviceRequest->service_request_number,
            config('mail.from.root_domain'),
        );

        expect($mailMessage->from[0])->toBe($expectedAddress)
            ->and($mailMessage->from[1])->toBe(config('mail.from.name'));
    });

    it('sets the reply-to address with plus-addressed SR number', function () {
        $tenant = Tenant::current();

        $serviceRequest = ServiceRequest::factory()->create();

        $notification = new SendEducatableServiceRequestOpenedNotification($serviceRequest, null);

        $mailMessage = $notification->toMail($serviceRequest->respondent);

        $emailMessage = EmailMessage::factory()->create();

        $notification->customizeOutboundMail($mailMessage, $emailMessage, $serviceRequest->respondent);

        $expectedAddress = sprintf(
            '%s-sr+%s@%s',
            $tenant->getSubdomain(),
            $serviceRequest->service_request_number,
            config('mail.from.root_domain'),
        );

        expect($mailMessage->replyTo)->toHaveCount(1)
            ->and($mailMessage->replyTo[0][0])->toBe($expectedAddress);
    });

    it('includes a visible body reference with the SR number', function () {
        $serviceRequest = ServiceRequest::factory()->create();

        $notification = new SendEducatableServiceRequestOpenedNotification($serviceRequest, null);

        $mailMessage = $notification->toMail($serviceRequest->respondent);

        $emailMessage = EmailMessage::factory()->create();

        $notification->customizeOutboundMail($mailMessage, $emailMessage, $serviceRequest->respondent);

        $mailArray = $mailMessage->toArray();

        $bodyRef = '[REF:' . $serviceRequest->service_request_number . ']';

        expect(collect($mailArray['introLines'] ?? [])->merge($mailArray['outroLines'] ?? []))
            ->toContain($bodyRef);
    });

    it('creates an OutboundEmailMessageId record after successful send with messageId', function () {
        $serviceRequest = ServiceRequest::factory()->create();

        $notification = new SendEducatableServiceRequestOpenedNotification($serviceRequest, null);

        $emailMessage = EmailMessage::factory()->create();

        $result = new EmailChannelResultData(
            success: true,
            messageId: '0101019d9205135c-e21c7d7b-f8bc-44fa-9c51-46d5decf9c65-000000',
        );

        $notification->afterSend($serviceRequest->respondent, $emailMessage, $result);

        expect(OutboundEmailMessageId::count())->toBe(1);

        $outbound = OutboundEmailMessageId::first();

        expect($outbound->message_id)->toBe('0101019d9205135c-e21c7d7b-f8bc-44fa-9c51-46d5decf9c65-000000')
            ->and($outbound->trackable_id)->toBe($serviceRequest->getKey())
            ->and($outbound->trackable_type)->toBe($serviceRequest->getMorphClass());

        $emailMessage->refresh();

        expect($emailMessage->outbound_message_id)->toBe('0101019d9205135c-e21c7d7b-f8bc-44fa-9c51-46d5decf9c65-000000');
    });

    it('does not create an OutboundEmailMessageId record after failed send', function () {
        $serviceRequest = ServiceRequest::factory()->create();

        $notification = new SendEducatableServiceRequestOpenedNotification($serviceRequest, null);

        $emailMessage = EmailMessage::factory()->create();

        $result = new EmailChannelResultData(
            success: false,
            messageId: '0101019d9205135c-e21c7d7b-f8bc-44fa-9c51-46d5decf9c65-000000',
        );

        $notification->afterSend($serviceRequest->respondent, $emailMessage, $result);

        expect(OutboundEmailMessageId::count())->toBe(0);
    });

    it('does not create an OutboundEmailMessageId record when messageId is null', function () {
        $serviceRequest = ServiceRequest::factory()->create();

        $notification = new SendEducatableServiceRequestOpenedNotification($serviceRequest, null);

        $emailMessage = EmailMessage::factory()->create();

        $result = new NotificationResultData(success: true);

        $notification->afterSend($serviceRequest->respondent, $emailMessage, $result);

        expect(OutboundEmailMessageId::count())->toBe(0);
    });
});

describe('Full notification send flow', function () {
    it('sets the from address on the actual outgoing email', function () {
        Event::fake([MessageSending::class]);

        $tenant = Tenant::current();

        $serviceRequest = ServiceRequest::withoutEvents(fn () => ServiceRequest::factory()->create());

        $notification = new SendEducatableServiceRequestOpenedNotification($serviceRequest, null);

        $serviceRequest->respondent->notifyNow($notification);

        $expectedAddress = sprintf(
            '%s-sr+%s@%s',
            $tenant->getSubdomain(),
            $serviceRequest->service_request_number,
            config('mail.from.root_domain'),
        );

        Event::assertDispatched(MessageSending::class, function (MessageSending $event) use ($expectedAddress) {
            $from = $event->message->getFrom();

            return count($from) === 1
                && $from[0]->getAddress() === $expectedAddress;
        });
    });

    it('sets the reply-to address with plus-addressed SR number on the actual outgoing email', function () {
        Event::fake([MessageSending::class]);

        $tenant = Tenant::current();

        $serviceRequest = ServiceRequest::withoutEvents(fn () => ServiceRequest::factory()->create());

        $notification = new SendEducatableServiceRequestOpenedNotification($serviceRequest, null);

        $serviceRequest->respondent->notifyNow($notification);

        $expectedAddress = sprintf(
            '%s-sr+%s@%s',
            $tenant->getSubdomain(),
            $serviceRequest->service_request_number,
            config('mail.from.root_domain'),
        );

        Event::assertDispatched(MessageSending::class, function (MessageSending $event) use ($expectedAddress) {
            $replyTo = $event->message->getReplyTo();

            return count($replyTo) === 1
                && $replyTo[0]->getAddress() === $expectedAddress;
        });
    });

    it('includes the body reference in the actual outgoing email', function () {
        Event::fake([MessageSending::class]);

        $serviceRequest = ServiceRequest::withoutEvents(fn () => ServiceRequest::factory()->create());

        $notification = new SendEducatableServiceRequestOpenedNotification($serviceRequest, null);

        $serviceRequest->respondent->notifyNow($notification);

        $bodyRef = '[REF:' . $serviceRequest->service_request_number . ']';

        Event::assertDispatched(MessageSending::class, function (MessageSending $event) use ($bodyRef) {
            $body = $event->message->getHtmlBody() ?? $event->message->getTextBody() ?? '';

            return str_contains($body, $bodyRef);
        });
    });
});
