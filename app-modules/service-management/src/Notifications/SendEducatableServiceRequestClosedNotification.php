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

namespace AidingApp\ServiceManagement\Notifications;

use AidingApp\Notification\Enums\NotificationChannel;
use AidingApp\Notification\Models\Contracts\CanBeNotified;
use AidingApp\Notification\Models\Contracts\Message;
use AidingApp\Notification\Notifications\Contracts\HasBeforeSendHook;
use AidingApp\Notification\Notifications\Messages\MailMessage;
use AidingApp\ServiceManagement\Enums\ServiceRequestTypeEmailTemplateRole;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestTypeEmailTemplate;
use AidingApp\ServiceManagement\Notifications\Concerns\HandlesServiceRequestTemplateContent;
use App\Models\NotificationSetting;
use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class SendEducatableServiceRequestClosedNotification extends Notification implements ShouldQueue, HasBeforeSendHook
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
        $name = $notifiable->first_name;

        $status = $this->serviceRequest->status;

        $template = $this->emailTemplate;

        if (! $template) {
            return MailMessage::make()
                ->settings($this->resolveNotificationSetting($notifiable))
                ->subject(__('[Ticket #:ticketno]: Your Issue Has Been Resolved', ['ticketno' => $this->serviceRequest->service_request_number]))
                ->greeting("Dear {$name},")
                ->line(__('We wanted to update you that the issue you reported in Ticket #:ticketNo regarding :shortDescription has been :status.', [
                    'ticketNo' => $this->serviceRequest->service_request_number,
                    'status' => $status->name,
                    'shortDescription' => Str::limit($this->serviceRequest->title, 10, '...'),
                ]))
                ->line('If you experience any further issues or have additional questions, please do not hesitate to open a new ticket.')
                ->salutation('Thank you for giving us a chance to help you with your issue.');
        }
        $timezone = Tenant::current()->getTimezone();
        $subject = $this->getSubject($template->subject, $timezone);
        $body = $this->getBody($template->body, ServiceRequestTypeEmailTemplateRole::Customer, $timezone);

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
