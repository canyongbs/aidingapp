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

use AidingApp\Engagement\Actions\GenerateEngagementBodyContent;
use AidingApp\Notification\Notifications\Channels\DatabaseChannel;
use AidingApp\Notification\Notifications\Channels\MailChannel;
use AidingApp\Notification\Notifications\Messages\MailMessage;
use AidingApp\ServiceManagement\Actions\GenerateServiceRequestTypeEmailTemplateContent;
use AidingApp\ServiceManagement\Enums\ServiceRequestEmailTemplateType;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequestResource;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestTypeEmailTemplate;
use App\Models\NotificationSetting;
use Filament\Notifications\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification as BaseNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\HtmlString;

class ServiceRequestCreated extends BaseNotification implements ShouldQueue
{
    use Queueable;

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
            MailChannel::class => 'mail',
        }];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $template = ServiceRequestTypeEmailTemplate::query()
            ->where('service_request_type_id', $this->serviceRequest->priority->type->id)
            ->where('type', ServiceRequestEmailTemplateType::Created)
            ->first();

        if (! $template) {
          return MailMessage::make()
              ->settings($this->resolveNotificationSetting($notifiable))
              ->subject("Service request {$this->serviceRequest->service_request_number} created")
              ->line("The service request {$this->serviceRequest->service_request_number} has been created.")
              ->action('View Service Request', ServiceRequestResource::getUrl('view', ['record' => $this->serviceRequest]));
        }

        $subject = $this->getSubject($template->subject);

        $body = $this->getBody($template->body);

        return MailMessage::make()
            ->settings($this->resolveNotificationSetting($notifiable))
            ->subject(strip_tags($subject))
            ->content($body);
    }

    protected function injectButtonUrlIntoTiptapContent(array $content): array
    {
        if (!isset($content['content']) || !is_array($content['content'])) {
            return $content;
        }

        $content['content'] = array_map(function ($block) {
            if ($block['type'] === 'tiptapBlock' &&
                ($block['attrs']['type'] ?? null) === 'serviceRequestTypeEmailTemplateButtonBlock') {
                
                $block['attrs']['data']['url'] = ServiceRequestResource::getUrl('view', [
                    'record' => $this->serviceRequest,
                ]);
            }

            if (isset($block['content']) && is_array($block['content'])) {
                $block = $this->injectButtonUrlIntoTiptapContent($block);
            }

            return $block;
        }, $content['content']);

        return $content;
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

    public function getBody($body): HtmlString
    {
        if (is_array($body)) {
            $body = $this->injectButtonUrlIntoTiptapContent($body);
        }

        return app(GenerateServiceRequestTypeEmailTemplateContent::class)(
            $body,
            $this->getMergeData(),
            $this->serviceRequest,
            'body',
        );
    }

    public function getSubject($subject): HtmlString
    {
        return app(GenerateServiceRequestTypeEmailTemplateContent::class)(
            $subject,
            $this->getMergeData(),
            $this->serviceRequest,
            'subject',
        );
    }

    public function getMergeData(): array
    {
        return [
            'service request number' => $this->serviceRequest->service_request_number,
            'created' => $this->serviceRequest->created_at,
            'updated' => $this->serviceRequest->updated_at,
            'assigned to' => $this->serviceRequest->respondent->full_name,
            'status' => $this->serviceRequest->status->name,
            'title' => $this->serviceRequest->title,
            'type' => $this->serviceRequest->priority->type->name,
        ];
    }
}
