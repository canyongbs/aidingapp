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

namespace AidingApp\Engagement\Notifications;

use AidingApp\Engagement\Models\Engagement;
use AidingApp\Engagement\Models\EngagementBatch;
use AidingApp\Notification\DataTransferObjects\NotificationResultData;
use AidingApp\Notification\Enums\NotificationChannel;
use AidingApp\Notification\Models\Contracts\CanBeNotified;
use AidingApp\Notification\Models\Contracts\Message;
use AidingApp\Notification\Models\OutboundDeliverable;
use AidingApp\Notification\Notifications\Contracts\HasAfterSendHook;
use AidingApp\Notification\Notifications\Contracts\HasBeforeSendHook;
use AidingApp\Notification\Notifications\Messages\MailMessage;
use AidingApp\Notification\Notifications\Messages\TwilioMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Throwable;

class EngagementNotification extends Notification implements ShouldQueue, HasBeforeSendHook, HasAfterSendHook
{
    use Queueable;

    public function __construct(
        public Engagement $engagement
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return [match ($this->engagement->channel) {
            NotificationChannel::Email => 'mail',
        }];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return MailMessage::make()
            ->subject($this->engagement->subject)
            ->greeting("Hello {$this->engagement->recipient->display_name}!")
            ->content($this->engagement->getBody());
    }

    public function toSms(object $notifiable): TwilioMessage
    {
        return TwilioMessage::make($notifiable)
            ->content($this->engagement->getBodyMarkdown());
    }

    public function failed(?Throwable $exception): void
    {
        if (is_null($this->engagement->engagement_batch_id)) {
            $this->engagement->user->notify(new EngagementFailedNotification($this->engagement));
        }
    }

    public function beforeSend(AnonymousNotifiable|CanBeNotified $notifiable, OutboundDeliverable|Message $message, NotificationChannel $channel): void
    {
        $message->related()->associate($this->engagement);
    }

    public function afterSend(AnonymousNotifiable|CanBeNotified $notifiable, OutboundDeliverable|Message $message, NotificationResultData $result): void
    {
        if (! $this->engagement->engagementBatch) {
            return;
        }

        EngagementBatch::query()
            ->whereKey($this->engagement->engagementBatch)
            ->lockForUpdate()
            ->update([
                'processed_engagements' => DB::raw('processed_engagements + 1'),
                ...($result->success ? ['successful_engagements' => DB::raw('successful_engagements + 1')] : []),
            ]);
    }
}
