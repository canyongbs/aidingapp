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

use AidingApp\Contact\Models\Contact;
use AidingApp\Notification\Enums\NotificationChannel;
use AidingApp\Notification\Models\Contracts\CanBeNotified;
use AidingApp\Notification\Models\Contracts\Message;
use AidingApp\Notification\Notifications\Contracts\HasBeforeSendHook;
use AidingApp\Notification\Notifications\Messages\MailMessage;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestTypeEmailTemplate;
use AidingApp\ServiceManagement\Notifications\Concerns\HandlesServiceRequestTemplateContent;
use App\Models\Contracts\Educatable;
use App\Models\NotificationSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\Notification;

class SendClosedServiceFeedbackNotification extends Notification implements ShouldQueue, HasBeforeSendHook
{
    use Queueable;
    use HandlesServiceRequestTemplateContent;

    public function __construct(
        protected ServiceRequest $serviceRequest,
        public ?ServiceRequestTypeEmailTemplate $emailTemplate = null, // TODO: When the SurveyResponseTemplate feature flag is removed, then remove the `= null` default.
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
        $template = $this->emailTemplate;

        /** @var Educatable $educatable */
        $educatable = $notifiable;

        $name = match ($notifiable::class) {
            Contact::class => $educatable->first_name,
        };

        if (! $template) {
            return MailMessage::make()
                ->settings($this->resolveNotificationSetting($notifiable))
                ->subject("Feedback survey for {$this->serviceRequest->service_request_number}")
                ->greeting("Hi {$name},")
                ->line('To help us serve you better in the future, we’d love to hear about your experience with our support team.')
                ->action('Rate Service', route('feedback.service.request', $this->serviceRequest->id))
                ->line('We appreciate your time and we value your feedback!')
                ->salutation('Thank you.');
        }

        $subject = $this->getSubject($template->subject);

        $body = $this->getBody($template->body);

        $test = MailMessage::make()
            ->settings($this->resolveNotificationSetting($notifiable))
            ->subject(strip_tags($subject))
            ->content($body);

        return $test;
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
