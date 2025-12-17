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
use AidingApp\InAppCommunication\Filament\Pages\UserChat;
use AidingApp\InAppCommunication\Models\Conversation;
use AidingApp\InAppCommunication\Models\ConversationParticipant;
use AidingApp\InAppCommunication\Models\Message;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Query\JoinClause;
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

        $userType = $user->getMorphClass();
        $userId = $user->getKey();

        /** @var Collection<int, array<string, mixed>> */
        return Conversation::query()
            ->select('conversations.*')
            ->selectRaw('conversation_participants.unread_count as unread_count')
            ->join('conversation_participants', function (JoinClause $join) use ($userType, $userId) {
                $join->on('conversation_participants.conversation_id', '=', 'conversations.id')
                    ->where('conversation_participants.participant_type', '=', $userType)
                    ->where('conversation_participants.participant_id', '=', $userId);
            })
            ->where('conversation_participants.unread_count', '>', 0)
            ->with([ // @phpstan-ignore argument.type
                'latestMessage.author',
                'conversationParticipants' => fn (HasMany $query) => $query
                    ->whereNot(fn (Builder $subQuery) => $subQuery->whereMorphedTo('participant', $user))
                    ->with('participant'),
            ])
            ->orderByDesc(
                Message::query()
                    ->select('created_at')
                    ->whereColumn('conversation_id', 'conversations.id')
                    ->latest()
                    ->limit(1)
            )
            ->get()
            ->map(fn (Conversation $conversation) => $this->formatNotification($conversation, $user));
    }

    public function getTotalUnreadCount(): int
    {
        $user = auth()->user();

        assert($user instanceof User);

        return (int) ConversationParticipant::query()
            ->whereMorphedTo('participant', $user)
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
            $avatarUrl = $otherUser instanceof User ? Filament::getUserAvatarUrl($otherUser) : null;
        }

        $latestMessage = $conversation->latestMessage;
        $messagePreview = null;

        if ($latestMessage) {
            /** @var array<mixed>|string $content */
            $content = $latestMessage->content;

            if (is_array($content)) {
                $messagePreview = $this->extractTextFromTipTap($content);
            } else {
                $messagePreview = strip_tags($content);
            }

            $messagePreview = Str::limit($messagePreview, 50);
        }

        /** @var int $unreadCount */
        $unreadCount = $conversation->unread_count ?? 0;

        return [
            'id' => $conversation->getKey(),
            'url' => UserChat::getUrl(['conversation' => $conversation]),
            'type' => $conversation->type,
            'display_name' => $displayName,
            'unread_count' => $unreadCount,
            'avatar_url' => $avatarUrl,
            'message_preview' => $messagePreview,
            'author_name' => $latestMessage?->author instanceof User ? $latestMessage->author->name : null,
            'created_at' => $latestMessage?->created_at?->diffForHumans(),
        ];
    }

    /**
     * @param  array<string, mixed>  $node
     */
    protected function extractTextFromTipTap(array $node): string
    {
        $text = '';

        $type = $node['type'] ?? null;

        if ($type === 'text') {
            $text .= $node['text'] ?? '';
        } elseif ($type === 'mention') {
            $text .= ' @' . ($node['attrs']['label'] ?? $node['attrs']['id'] ?? '') . ' ';
        } elseif ($type === 'hardBreak') {
            $text .= ' ';
        }

        if (isset($node['content']) && is_array($node['content'])) {
            foreach ($node['content'] as $child) {
                if (is_array($child)) {
                    $text .= $this->extractTextFromTipTap($child);
                }
            }
        }

        return $text;
    }
}
