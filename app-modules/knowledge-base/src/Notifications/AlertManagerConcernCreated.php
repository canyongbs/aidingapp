<?php

namespace AidingApp\KnowledgeBase\Notifications;

use AidingApp\KnowledgeBase\Filament\Resources\KnowledgeBaseItems\KnowledgeBaseItemResource;
use AidingApp\KnowledgeBase\Models\KnowledgeBaseItem;
use AidingApp\KnowledgeBase\Models\KnowledgeBaseItemConcern;
use AidingApp\Notification\Notifications\Messages\MailMessage;
use App\Models\EmailTemplate;
use App\Models\User;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class AlertManagerConcernCreated extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public KnowledgeBaseItemConcern $knowledgeBaseItemConcern) {}

    /**
     * @return array<int, string>
     */
    public function via(User $notifiable): array
    {
        return ['database'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toDatabase(User $notifiable): array
    {
        $knowledgeBaseItem = $this->knowledgeBaseItemConcern->knowledgeBaseItem;

        $user = $this->knowledgeBaseItemConcern->createdBy;

        $url = KnowledgeBaseItemResource::getUrl('view', ['record' => $knowledgeBaseItem->getKey(), 'tab' => 'concerns']);

        $link = new HtmlString("<a href='{$url}' target='_blnk' class='underline'>{$knowledgeBaseItem->title}</a>");

        return FilamentNotification::make()
            ->title("A concern has been raised by {$user->name}, {$user->job_title} about the knowledge base item {$link}.")
            ->getDatabaseMessage();
    }
}
