<?php

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

class ServiceRequestClosed extends BaseNotification implements EmailNotification, DatabaseNotification
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
            ->subject("Service request {$this->serviceRequest->service_request_number} closed")
            ->line("The service request {$this->serviceRequest->service_request_number} has been closed.")
            ->action('View Service Request', ServiceRequestResource::getUrl('view', ['record' => $this->serviceRequest]));
    }

    public function toDatabase(object $notifiable): array
    {
        return Notification::make()
            ->success()
            ->title((string) str("[Service request {$this->serviceRequest->service_request_number}](" . ServiceRequestResource::getUrl('view', ['record' => $this->serviceRequest]) . ') closed')->markdown())
            ->getDatabaseMessage();
    }

    private function resolveNotificationSetting(object $notifiable): ?NotificationSetting
    {
        return $this->serviceRequest->division?->notificationSetting?->setting;
    }
}
