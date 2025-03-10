<?php

namespace AidingApp\Engagement\Notifications;

use AidingApp\Engagement\Models\Engagement;
use AidingApp\Notification\Models\Contracts\CanBeNotified;
use AidingApp\Notification\Notifications\Messages\MailMessage;
use App\Models\NotificationSetting;
use App\Models\User;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class EngagementFailedNotification extends Notification implements ShouldQueue
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
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return MailMessage::make()
            ->settings($this->resolveNotificationSetting($notifiable))
            ->subject('An engagement failed to deliver')
            ->line("The engagement {$this->engagement->channel->getLabel()} failed to be delivered to {$this->engagement->recipient->display_name}:")
            ->line('Subject: ' . ($this->engagement->subject ?? 'n/a'))
            ->line('Body: ' . $this->engagement->getBody());
    }

    public function toDatabase(object $notifiable): array
    {
        return FilamentNotification::make()
            ->danger()
            ->title('An engagement failed to deliver')
            ->body("Your engagement {$this->engagement->channel->getLabel()} failed to be delivered to {$this->engagement->recipient->display_name}.")
            ->getDatabaseMessage();
    }

    private function resolveNotificationSetting(CanBeNotified $notifiable): ?NotificationSetting
    {
        return $notifiable instanceof User ? $this->engagement->createdBy->teams()->first()?->division?->notificationSetting?->setting : null;
    }
}
