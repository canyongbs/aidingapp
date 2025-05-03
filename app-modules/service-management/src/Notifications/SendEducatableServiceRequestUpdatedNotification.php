<?php

namespace AidingApp\ServiceManagement\Notifications;

use AidingApp\Contact\Models\Contact;
use AidingApp\Notification\Enums\NotificationChannel;
use AidingApp\Notification\Models\Contracts\CanBeNotified;
use AidingApp\Notification\Models\Contracts\Message;
use AidingApp\Notification\Notifications\Contracts\HasBeforeSendHook;
use AidingApp\Notification\Notifications\Messages\MailMessage;
use AidingApp\ServiceManagement\Enums\ServiceRequestTypeEmailTemplateRole;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestTypeEmailTemplate;
use AidingApp\ServiceManagement\Notifications\Concerns\HandlesServiceRequestTemplateContent;
use App\Models\Contracts\Educatable;
use App\Models\NotificationSetting;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\Notification;

class SendEducatableServiceRequestUpdatedNotification extends Notification implements ShouldQueue, HasBeforeSendHook
{
    use Queueable;
    use HandlesServiceRequestTemplateContent;

    public function __construct(
        protected ServiceRequest $serviceRequest,
        protected ?ServiceRequestTypeEmailTemplate $emailTemplate,
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        // /** @var Educatable $educatable */
        // $educatable = $notifiable;

        // if ($educatable instanceof Contact) {
        //     $name = $educatable->first_name;
        // } else {
        //     throw new Exception('Unhandled notifiable type');
        // }

        $name = $notifiable->first_name;

        $template = $this->emailTemplate;

        if (! $template) {
            return MailMessage::make()
                ->settings($this->resolveNotificationSetting($notifiable))
                ->subject("There’s an update on your service request {$this->serviceRequest->service_request_number}")
                ->greeting("Hello {$name},")
                ->line("There’s been a new update to your service request {$this->serviceRequest->service_request_number}. Please check the latest details.")
                ->action('View Service Request', route('portal.service-request.show', $this->serviceRequest));
        }

        $subject = $this->getSubject($template->subject);

        $body = $this->getBody($template->body, ServiceRequestTypeEmailTemplateRole::Customer);

        return MailMessage::make()
            ->settings($this->resolveNotificationSetting($notifiable))
            ->subject(strip_tags($subject))
            ->content($body);
    }

    public function beforeSend(AnonymousNotifiable|CanBeNotified $notifiable, Message $message, NotificationChannel $channel): void
    {
        $message->related()->associate($this->serviceRequest);
    }

    private function resolveNotificationSetting(object $notifiable): ?NotificationSetting
    {
        return $this->serviceRequest->division?->notificationSetting?->setting;
    }
}
