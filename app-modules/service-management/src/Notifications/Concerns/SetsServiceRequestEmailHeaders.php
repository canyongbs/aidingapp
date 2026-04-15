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

namespace AidingApp\ServiceManagement\Notifications\Concerns;

use AidingApp\Notification\DataTransferObjects\EmailChannelResultData;
use AidingApp\Notification\DataTransferObjects\NotificationResultData;
use AidingApp\Notification\Models\Contracts\CanBeNotified;
use AidingApp\Notification\Models\Contracts\Message;
use AidingApp\Notification\Models\EmailMessage;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use App\Models\Tenant;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\Messages\MailMessage;

trait SetsServiceRequestEmailHeaders
{
    public function customizeOutboundMail(
        MailMessage $mailMessage,
        EmailMessage $emailMessage,
        object $notifiable
    ): void {
        $tenant = Tenant::current();

        $serviceRequest = $this->getServiceRequest();

        $srPlusAddress = sprintf(
            '%s-sr+%s@%s',
            $tenant->getSubdomain(),
            $serviceRequest->service_request_number,
            config('mail.from.root_domain'),
        );

        $mailMessage->from($srPlusAddress, config('mail.from.name'));
        $mailMessage->replyTo($srPlusAddress);

        $mailMessage->line('[REF:' . $serviceRequest->service_request_number . ']');
    }

    public function afterSendServiceRequestEmailThreading(AnonymousNotifiable|CanBeNotified $notifiable, Message $emailMessage, NotificationResultData $result): void
    {
        if (! $result->success || ! $emailMessage instanceof EmailMessage) {
            return;
        }

        if (! $result instanceof EmailChannelResultData || ! filled($result->messageId)) {
            return;
        }

        $serviceRequest = $this->getServiceRequest();

        $emailMessage->outbound_message_id = $result->messageId;
        $emailMessage->save();

        $serviceRequest->outboundEmailMessageIds()->create([
            'message_id' => $result->messageId,
        ]);
    }

    abstract protected function getServiceRequest(): ServiceRequest;
}
