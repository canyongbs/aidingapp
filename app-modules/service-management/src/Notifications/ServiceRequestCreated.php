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

namespace AidingApp\ServiceManagement\Notifications;

use AidingApp\Notification\Notifications\BaseNotification;
use AidingApp\Notification\Notifications\Channels\DatabaseChannel;
use AidingApp\Notification\Notifications\Channels\EmailChannel;
use AidingApp\Notification\Notifications\Concerns\DatabaseChannelTrait;
use AidingApp\Notification\Notifications\Concerns\EmailChannelTrait;
use AidingApp\Notification\Notifications\DatabaseNotification;
use AidingApp\Notification\Notifications\EmailNotification;
use AidingApp\Notification\Notifications\Messages\MailMessage;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequestResource;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use App\Models\NotificationSetting;
use Filament\Notifications\Notification;

class ServiceRequestCreated extends BaseNotification implements EmailNotification, DatabaseNotification
{
    use EmailChannelTrait;
    use DatabaseChannelTrait;

    /**
     * @param class-string $channel
     */
    public function __construct(
        public ServiceRequest $serviceRequest,
        public string $channel,
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return [match ($this->channel) {
            DatabaseChannel::class => 'database',
            EmailChannel::class => 'mail',
        }];
    }

    public function toEmail(object $notifiable): MailMessage
    {
        return MailMessage::make()
            ->settings($this->resolveNotificationSetting($notifiable))
            ->subject("Service request {$this->serviceRequest->service_request_number} created")
            ->line("The service request {$this->serviceRequest->service_request_number} has been created.")
            ->action('View Service Request', ServiceRequestResource::getUrl('view', ['record' => $this->serviceRequest]));
    }

    public function toDatabase(object $notifiable): array
    {
        return Notification::make()
            ->success()
            ->title((string) str("[Service request {$this->serviceRequest->service_request_number}](" . ServiceRequestResource::getUrl('view', ['record' => $this->serviceRequest]) . ') created')->markdown())
            ->getDatabaseMessage();
    }

    private function resolveNotificationSetting(object $notifiable): ?NotificationSetting
    {
        return $this->serviceRequest->division?->notificationSetting?->setting;
    }
}