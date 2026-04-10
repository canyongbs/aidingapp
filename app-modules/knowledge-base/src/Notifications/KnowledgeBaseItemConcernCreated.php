<?php

namespace AidingApp\KnowledgeBase\Notifications;

use AidingApp\KnowledgeBase\Filament\Resources\KnowledgeBaseItems\KnowledgeBaseItemResource;
use AidingApp\KnowledgeBase\Models\KnowledgeBaseItemConcern;
use AidingApp\Notification\Notifications\Messages\MailMessage;
use App\Models\EmailTemplate;
use App\Models\User;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class KnowledgeBaseItemConcernCreated extends Notification
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

        $url = KnowledgeBaseItemResource::getUrl('view', ['record' => $knowledgeBaseItem->getKey()]);

        $link = new HtmlString("<a href='{$url}' target='_blank' class='underline'>{$knowledgeBaseItem->title}</a>");

        return FilamentNotification::make()
            ->title("You have successfully raised a new concern on the resource hub article {$link}")
            ->success()
            ->getDatabaseMessage();
    }
}
