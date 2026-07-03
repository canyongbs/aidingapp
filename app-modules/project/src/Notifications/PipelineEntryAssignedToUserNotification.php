<?php

namespace AidingApp\Project\Notifications;

use AidingApp\Notification\Notifications\Messages\MailMessage;
use AidingApp\Project\Filament\Resources\Pipelines\Pages\ManagePipelineEntries;
use AidingApp\Project\Models\PipelineEntry;
use App\Models\NotificationSetting;
use App\Models\User;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class PipelineEntryAssignedToUserNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public PipelineEntry $pipelineEntry
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(User $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(User $notifiable): MailMessage
    {
        return MailMessage::make()
            ->settings($this->resolveNotificationSetting($notifiable))
            ->subject('You have been assigned a new Pipeline Entry')
            ->line('You have been assigned the task: ')
            ->line("\"{$this->pipelineEntry->name}\"");
    }

    /**
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        $url = ManagePipelineEntries::getUrl(['record' => $this->pipelineEntry->pipeline, 'tableActionRecord' => $this->pipelineEntry, 'tableAction' => 'view']);

        $title = str($this->pipelineEntry->name)->limit();

        $message = filled($url)
            ? "You have been assigned a new Pipeline Entry: <a href='{$url}' target='_blank' class='underline'>{$title}</a>"
            : "You have been assigned a new Pipeline Entry: {$title}";

        return FilamentNotification::make()
            ->success()
            ->title($message)
            ->getDatabaseMessage();
    }

    private function resolveNotificationSetting(User $notifiable): ?NotificationSetting
    {
        return $this->pipelineEntry->createdBy->department?->division?->notificationSetting?->setting;
    }
}
