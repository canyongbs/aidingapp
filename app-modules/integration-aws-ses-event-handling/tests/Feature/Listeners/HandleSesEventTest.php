<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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

use AidingApp\Notification\Enums\EmailMessageEventType;
use AidingApp\Notification\Models\EmailMessage;
use AidingApp\Webhook\Http\Middleware\VerifyAwsSnsRequest;
use App\Models\Tenant;

use function Pest\Laravel\withHeaders;
use function Pest\Laravel\withoutMiddleware;
use function Tests\loadFixtureFromModule;

beforeEach(function () {
    withoutMiddleware(VerifyAwsSnsRequest::class);
});

it('correctly handles the incoming SES event', function (string $event, EmailMessageEventType $eventType) {
    /** @var Tenant $tenant */
    $tenant = Tenant::query()->first();

    $emailMessage = $tenant->execute(function () {
        return EmailMessage::factory()->create();
    });

    // And we receive some sort of SES event when attempting to deliver
    $snsData = loadFixtureFromModule('integration-aws-ses-event-handling', 'sns-notification');

    $messageContent = loadFixtureFromModule('integration-aws-ses-event-handling', $event);
    data_set($messageContent, 'mail.tags.app_message_id.0', $emailMessage->getKey());
    data_set($messageContent, 'mail.tags.tenant_id.0', $tenant->getKey());
    $snsData['Message'] = json_encode($messageContent);

    $tenant->execute(function () use ($emailMessage) {
        expect($emailMessage->events()->count())->toBe(0);
    });

    $response = withHeaders(
        [
            'x-amz-sns-message-type' => 'Notification',
            'x-amz-sns-message-id' => '22b80b92-fdea-4c2c-8f9d-bdfb0c7bf324',
            'x-amz-sns-topic-arn' => 'arn:aws:sns:us-west-2:123456789012:MyTopic',
            'x-amz-sns-subscription-arn' => 'arn:aws:sns:us-west-2:123456789012:MyTopic:c9135db0-26c4-47ec-8998-413945fb5a96',
            'Content-Length' => '773',
            'Content-Type' => 'text/plain; charset=UTF-8',
            'Host' => 'example.com',
            'Connection' => 'Keep-Alive',
            'User-Agent' => 'Amazon Simple Notification Service Agent',
        ]
    )->postJson(
        route('landlord.api.inbound.webhook.awsses'),
        $snsData,
    );

    $response->assertOk();

    $tenant->execute(function () use ($emailMessage, $eventType) {
        // The email message should have the apppriate email message event created based on the event
        $emailMessage->refresh();

        expect($emailMessage->events()->count())->toBe(1);

        $event = $emailMessage->events()->first();

        expect($event->type)->toBe($eventType);
    });
})->with([
    'HandleSesBounceEvent' => [
        'Bounce',
        EmailMessageEventType::Bounce,
    ],
    'HandleSesClickEvent' => [
        'Click',
        EmailMessageEventType::Click,
    ],
    'HandleSesComplaintEvent' => [
        'Complaint',
        EmailMessageEventType::Complaint,
    ],
    'HandleSesDeliveryEvent' => [
        'Delivery',
        EmailMessageEventType::Delivery,
    ],
    'HandleSesDeliveryDelayEvent' => [
        'DeliveryDelay',
        EmailMessageEventType::DeliveryDelay,
    ],
    'HandleSesOpenEvent' => [
        'Open',
        EmailMessageEventType::Open,
    ],
    'HandleSesRejectEvent' => [
        'Reject',
        EmailMessageEventType::Reject,
    ],
    'HandleSesRenderingFailureEvent' => [
        'RenderingFailure',
        EmailMessageEventType::RenderingFailure,
    ],
    'HandleSesSendEvent' => [
        'Send',
        EmailMessageEventType::Send,
    ],
    'HandleSesSubscriptionEvent' => [
        'Subscription',
        EmailMessageEventType::Subscription,
    ],
]);
