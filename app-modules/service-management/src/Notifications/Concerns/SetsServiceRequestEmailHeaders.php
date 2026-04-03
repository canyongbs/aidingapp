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

namespace AidingApp\ServiceManagement\Notifications\Concerns;

use AidingApp\Notification\DataTransferObjects\NotificationResultData;
use AidingApp\Notification\Models\Contracts\CanBeNotified;
use AidingApp\Notification\Models\Contracts\Message;
use AidingApp\Notification\Models\EmailMessage;
use AidingApp\Notification\Models\OutboundEmailMessageId;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use App\Features\ServiceRequestEmailThreading;
use App\Models\Tenant;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\Messages\MailMessage;
use Symfony\Component\Mime\Email;

trait SetsServiceRequestEmailHeaders
{
    public function customizeOutboundMail(
        MailMessage $mailMessage,
        EmailMessage $emailMessage,
        object $notifiable
    ): void {
        $tenant = Tenant::current();
        $srFromAddress = $tenant->getServiceRequestFromAddress();

        $mailMessage->from($srFromAddress, config('mail.from.name'));
        $mailMessage->replyTo($srFromAddress);

        // TODO: FeatureFlag Cleanup - Remove this check after ServiceRequestEmailThreading is removed
        if (! ServiceRequestEmailThreading::active()) {
            return;
        }

        $serviceRequest = $this->getServiceRequest();

        $messageId = $this->generateServiceRequestMessageId($serviceRequest);
        $references = $this->buildServiceRequestReferencesChain($serviceRequest);

        $mailMessage->withSymfonyMessage(function (Email $email) use ($messageId, $references) {
            $email->getHeaders()->remove('Message-ID');
            $email->getHeaders()->addIdHeader('Message-ID', $messageId);

            if ($references) {
                $email->getHeaders()->addTextHeader('References', $references);
            }
        });

        $emailMessage->outbound_message_id = $messageId;
        $emailMessage->save();
    }

    public function afterSendServiceRequestEmailThreading(AnonymousNotifiable|CanBeNotified $notifiable, Message $emailMessage, NotificationResultData $result): void
    {
        // TODO: FeatureFlag Cleanup - Remove this check after ServiceRequestEmailThreading is removed
        if (! ServiceRequestEmailThreading::active()) {
            return;
        }

        if ($result->success && $emailMessage instanceof EmailMessage && $emailMessage->outbound_message_id) {
            $serviceRequest = $this->getServiceRequest();

            $serviceRequest->outboundEmailMessageIds()->create([
                'message_id' => $emailMessage->outbound_message_id,
            ]);
        }
    }

    abstract protected function getServiceRequest(): ServiceRequest;

    protected function generateServiceRequestMessageId(ServiceRequest $serviceRequest): string
    {
        $sequence = OutboundEmailMessageId::query()
            ->where('trackable_id', $serviceRequest->getKey())
            ->where('trackable_type', $serviceRequest->getMorphClass())
            ->count() + 1;

        return sprintf(
            '%s.%d.%d@%s',
            $serviceRequest->service_request_number,
            $sequence,
            now()->getTimestampMs(),
            config('mail.from.root_domain'),
        );
    }

    protected function buildServiceRequestReferencesChain(ServiceRequest $serviceRequest): ?string
    {
        $messageIds = OutboundEmailMessageId::query()
            ->where('trackable_id', $serviceRequest->getKey())
            ->where('trackable_type', $serviceRequest->getMorphClass())
            ->orderBy('id')
            ->pluck('message_id')
            ->map(fn (string $id) => "<{$id}>")
            ->implode(' ');

        return $messageIds ?: null;
    }
}
