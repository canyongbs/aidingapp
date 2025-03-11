<?php

namespace AidingApp\Engagement\Notifications;

use AidingApp\Engagement\Models\EngagementBatch;
use AidingApp\Notification\Enums\NotificationChannel;
use AidingApp\Notification\Notifications\Messages\MailMessage;
use App\Models\NotificationSetting;
use App\Models\User;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class EngagementBatchStartedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public EngagementBatch $engagementBatch,
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return MailMessage::make()
            ->settings($this->resolveNotificationSetting($notifiable))
            ->subject(match ($this->engagementBatch->channel) {
                NotificationChannel::Email => 'Bulk email started processing',
                default => 'Bulk engagement started processing',
            })
            ->line("We've started processing your bulk engagement of {$this->engagementBatch->total_engagements} messages, and we'll keep you updated on the progress.");
    }

    public function toDatabase(object $notifiable): array
    {
        return FilamentNotification::make()
            ->status('success')
            ->title(match ($this->engagementBatch->channel) {
                NotificationChannel::Email => 'Bulk email started processing',
                default => 'Bulk engagement started processing',
            })
            ->body("We've started processing your bulk engagement of {$this->engagementBatch->total_engagements} messages, and we'll keep you updated on the progress.")
            ->getDatabaseMessage();
    }

    private function resolveNotificationSetting(User $notifiable): ?NotificationSetting
    {
        return $this->engagementBatch->user->teams()->first()?->division?->notificationSetting?->setting;
    }
}
