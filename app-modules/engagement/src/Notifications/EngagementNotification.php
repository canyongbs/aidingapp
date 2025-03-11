<?php

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
