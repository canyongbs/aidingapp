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

namespace AidingApp\InAppCommunication\Livewire;

use AidingApp\InAppCommunication\Enums\ConversationType;
use AidingApp\InAppCommunication\Models\Conversation;
use AidingApp\InAppCommunication\Models\ConversationParticipant;
use AidingApp\InAppCommunication\Models\Scopes\WhereHasUnread;
use AidingApp\InAppCommunication\Models\Scopes\WithUnreadCount;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy]
class ChatNotifications extends Component
{
    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function getNotifications(): Collection
    {
        $user = auth()->user();

        assert($user instanceof User);

        /** @var Collection<int, array<string, mixed>> */
        return Conversation::query()
            ->whereHas('conversationParticipants', function (Builder $query) use ($user) {
                $query->whereMorphedTo('participant', $user);
            })
            ->tap(new WithUnreadCount($user))
            ->tap(new WhereHasUnread($user))
            ->with(['latestMessage.author', 'conversationParticipants.participant'])
            ->get()
            ->sortByDesc(fn (Conversation $conversation) => $conversation->latestMessage?->created_at)
            ->map(fn (Conversation $conversation) => $this->formatNotification($conversation, $user))
            ->values();
    }

    public function getTotalUnreadCount(): int
    {
        $user = auth()->user();

        assert($user instanceof User);

        return Conversation::query()
            ->whereHas('conversationParticipants', function (Builder $query) use ($user) {
                $query->whereMorphedTo('participant', $user);
            })
            ->tap(new WithUnreadCount($user))
            ->get()
            ->sum('unread_count');
    }

    public function render(): View
    {
        return view('in-app-communication::livewire.chat-notifications');
    }

    /**
     * @return array{
     *     id: string,
     *     url: string,
     *     type: ConversationType,
     *     display_name: string,
     *     unread_count: int,
     *     avatar_url: ?string,
     *     initials: string,
     *     message_preview: ?string,
     *     author_name: ?string,
     *     created_at: ?string,
     * }
     */
    protected function formatNotification(Conversation $conversation, User $currentUser): array
    {
        $otherParticipant = null;
        $avatarUrl = null;

        if ($conversation->type === ConversationType::Channel) {
            $displayName = $conversation->name ?? 'Unnamed Channel';
        } else {
            $otherParticipant = $conversation->conversationParticipants
                ->first(fn (ConversationParticipant $participant) => $participant->participant_id !== $currentUser->getKey());
            $otherUser = $otherParticipant?->participant;
            $displayName = $otherUser instanceof User ? $otherUser->name : 'Unknown User';
            $avatarUrl = $otherUser instanceof User ? $otherUser->getFilamentAvatarUrl() : null;
        }

        $latestMessage = $conversation->latestMessage;
        $messagePreview = null;

        if ($latestMessage) {
            /** @var array<mixed>|string $content */
            $content = $latestMessage->content;

            if (is_array($content)) {
                /** @var array<int, mixed> $contentArray */
                $contentArray = data_get($content, 'content', []);
                $messagePreview = collect($contentArray)
                    ->pluck('content')
                    ->flatten()
                    ->pluck('text')
                    ->filter()
                    ->implode(' ');
            } else {
                $messagePreview = strip_tags($content);
            }

            $messagePreview = Str::limit($messagePreview, 50);
        }

        /** @var int $unreadCount */
        $unreadCount = $conversation->unread_count ?? 0;

        return [
            'id' => $conversation->getKey(),
            'url' => route('filament.admin.pages.user-chat', ['conversation' => $conversation->getKey()]),
            'type' => $conversation->type,
            'display_name' => $displayName,
            'unread_count' => $unreadCount,
            'avatar_url' => $avatarUrl,
            'initials' => $this->getInitials($displayName),
            'message_preview' => $messagePreview,
            'author_name' => $latestMessage?->author instanceof User ? $latestMessage->author->name : null,
            'created_at' => $latestMessage?->created_at?->diffForHumans(),
        ];
    }

    protected function getInitials(string $name): string
    {
        return collect(explode(' ', $name))
            ->map(fn ($word) => mb_substr($word, 0, 1))
            ->take(2)
            ->implode('');
    }
}
