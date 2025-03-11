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

class EngagementBatchFinishedNotification extends Notification implements ShouldQueue
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
        $message = MailMessage::make()
            ->settings($this->resolveNotificationSetting($notifiable));

        if ($this->engagementBatch->successful_engagements < $this->engagementBatch->total_engagements) {
            return $message
                ->subject(match ($this->engagementBatch->channel) {
                    NotificationChannel::Email => 'Bulk email has been processed with failures',
                    default => 'Bulk engagement has been processed with failures',
                })
                ->line(($this->engagementBatch->total_engagements - $this->engagementBatch->successful_engagements) . " engagements failed out of {$this->engagementBatch->total_engagements}.");
        }

        return $message
            ->subject(match ($this->engagementBatch->channel) {
                NotificationChannel::Email => 'Bulk email has been processed',
                default => 'Bulk engagement has been processed',
            })
            ->line("{$this->engagementBatch->total_engagements} engagements sent successfully.");
    }

    public function toDatabase(object $notifiable): array
    {
        if ($this->engagementBatch->successful_engagements < $this->engagementBatch->total_engagements) {
            return FilamentNotification::make()
                ->warning()
                ->title(match ($this->engagementBatch->channel) {
                    NotificationChannel::Email => 'Bulk email has been processed with failures',
                    default => 'Bulk engagement has been processed with failures',
                })
                ->body(($this->engagementBatch->total_engagements - $this->engagementBatch->successful_engagements) . " engagements failed out of {$this->engagementBatch->total_engagements}.")
                ->getDatabaseMessage();
        }

        return FilamentNotification::make()
            ->success()
            ->title(match ($this->engagementBatch->channel) {
                NotificationChannel::Email => 'Bulk email has been processed',
                default => 'Bulk engagement has been processed',
            })
            ->body("{$this->engagementBatch->total_engagements} engagements sent successfully.")
            ->getDatabaseMessage();
    }

    private function resolveNotificationSetting(User $notifiable): ?NotificationSetting
    {
        return $this->engagementBatch->user->teams()->first()?->division?->notificationSetting?->setting;
    }
}
