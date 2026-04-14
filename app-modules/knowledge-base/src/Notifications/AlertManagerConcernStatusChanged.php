<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Aiding App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Aiding App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    <https://www.canyongbs.com> or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AidingApp\KnowledgeBase\Notifications;

use AidingApp\KnowledgeBase\Enums\ConcernStatus;
use AidingApp\KnowledgeBase\Filament\Resources\KnowledgeBaseItems\KnowledgeBaseItemResource;
use AidingApp\KnowledgeBase\Models\KnowledgeBaseItemConcern;
use App\Models\User;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class AlertManagerConcernStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public KnowledgeBaseItemConcern $knowledgeBaseItemConcern, public ConcernStatus $oldStatus) {}

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

        $url = KnowledgeBaseItemResource::getUrl('view', ['record' => $knowledgeBaseItem->getKey(), 'tab' => 'concerns']);

        $link = new HtmlString("<a href='{$url}' target='_blnk' class='underline'>{$knowledgeBaseItem->title}</a>");

        return FilamentNotification::make()
            ->title("The status of a concern has changed from {$this->oldStatus->getLabel()} to {$this->knowledgeBaseItemConcern->status->getLabel()} by {$this->knowledgeBaseItemConcern->lastUpdatedBy->name} on the resource hub article {$link}.")
            ->getDatabaseMessage();
    }
}
