<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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

namespace AidingApp\ServiceManagement\Notifications;

use AidingApp\Notification\Models\OutboundDeliverable;
use AidingApp\Notification\Notifications\BaseNotification;
use AidingApp\Notification\Notifications\Concerns\EmailChannelTrait;
use AidingApp\Notification\Notifications\EmailNotification;
use AidingApp\Notification\Notifications\Messages\MailMessage;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use App\Models\NotificationSetting;
use Illuminate\Support\Str;

class SendEducatableServiceRequestClosedNotification extends BaseNotification implements EmailNotification
{
    use EmailChannelTrait;

    public function __construct(
        protected ServiceRequest $serviceRequest,
    ) {}

    public function toEmail(object $notifiable): MailMessage
    {
        $name = $notifiable->first_name;

        $status = $this->serviceRequest->status;

        return MailMessage::make()
            ->settings($this->resolveNotificationSetting($notifiable))
            ->subject(__('[Ticket #:ticketno]: Your Issue Has Been Resolved', ['ticketno' => $this->serviceRequest->service_request_number]))
            ->greeting("Dear {$name},")
            ->line(__('We wanted to update you that the issue you reported in Ticket #:ticketNo regarding :shortDescription has been :status.', [
                'ticketNo' => $this->serviceRequest->service_request_number,
                'status' => $status->name,
                'shortDescription' => Str::limit($this->serviceRequest->res_details, 10, '...'),
            ]))
            ->line('If you experience any further issues or have additional questions, please do not hesitate to open a new ticket.')
            ->salutation('Thank you for giving us a chance to help you with your issue.');
    }

    protected function beforeSendHook(object $notifiable, OutboundDeliverable $deliverable, string $channel): void
    {
        $deliverable->related()->associate($this->serviceRequest);
    }

    private function resolveNotificationSetting(object $notifiable): ?NotificationSetting
    {
        return $this->serviceRequest->division?->notificationSetting?->setting;
    }
}
