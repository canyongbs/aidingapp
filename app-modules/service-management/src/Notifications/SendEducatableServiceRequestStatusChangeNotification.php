<?php

namespace AidingApp\ServiceManagement\Notifications;

use AidingApp\Contact\Models\Contact;
use AidingApp\Notification\Enums\NotificationChannel;
use AidingApp\Notification\Models\Contracts\CanBeNotified;
use AidingApp\Notification\Models\Contracts\Message;
use AidingApp\Notification\Notifications\Contracts\HasBeforeSendHook;
use AidingApp\Notification\Notifications\Messages\MailMessage;
use AidingApp\ServiceManagement\Enums\ServiceRequestEmailTemplateType;
use AidingApp\ServiceManagement\Enums\ServiceRequestTypeEmailTemplateRole;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequestResource;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestTypeEmailTemplate;
use AidingApp\ServiceManagement\Notifications\Concerns\HandlesServiceRequestTemplateContent;
use App\Models\Contracts\Educatable;
use App\Models\NotificationSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\Notification;

class SendEducatableServiceRequestStatusChangeNotification extends Notification implements ShouldQueue, HasBeforeSendHook
{
    use Queueable;
    use HandlesServiceRequestTemplateContent;

    public function __construct(
        protected ServiceRequest $serviceRequest,
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

        // $name = match ($notifiable::class) {
        //     Contact::class => $educatable->first_name,
        // };

        $template = ServiceRequestTypeEmailTemplate::query()
            ->where('service_request_type_id', $this->serviceRequest->priority->type->id)
            ->where('type', ServiceRequestEmailTemplateType::StatusChange)
            ->where('role', ServiceRequestTypeEmailTemplateRole::Customer->value)
            ->first();

        // if (! $template) {
        //     return MailMessage::make()
        //         ->settings($this->resolveNotificationSetting($notifiable))
        //         ->subject("Status update: Service request {$this->serviceRequest->service_request_number} is now {$this->serviceRequest->status?->name}")
        //         ->greeting("Hello {$name},")
        //         ->line("The status of your service request {$this->serviceRequest->service_request_number} has been updated to: {$this->serviceRequest->status?->name}.")
        //         ->action('View Service Request', ServiceRequestResource::getUrl('view', ['record' => $this->serviceRequest]));
        // }

        $subject = $this->getSubject($template->subject);

        $body = $this->getBody($template->body);

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
