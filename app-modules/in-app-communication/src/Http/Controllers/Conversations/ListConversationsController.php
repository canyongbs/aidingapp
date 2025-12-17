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

namespace AidingApp\InAppCommunication\Http\Controllers\Conversations;

use AidingApp\InAppCommunication\Actions\GetUserConversations;
use AidingApp\InAppCommunication\Enums\ConversationNotificationPreference;
use AidingApp\InAppCommunication\Enums\ConversationType;
use AidingApp\InAppCommunication\Models\Conversation;
use AidingApp\InAppCommunication\Models\ConversationParticipant;
use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Filament\Facades\Filament;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class ListConversationsController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Conversation::class);

        $cursor = $request->query('cursor');
        $limit = min((int) $request->query('limit', 25), 50);
        $currentUserId = $request->user()->getKey();

        $getUserConversations = app(GetUserConversations::class);

        $pinnedConversations = [];

        if (! $cursor) {
            $pinnedItems = $getUserConversations->pinned($request->user());
            $pinnedConversations = $this->formatConversations($pinnedItems, $currentUserId);
        }

        $paginator = $getUserConversations(
            user: $request->user(),
            limit: $limit,
            cursor: $cursor,
            excludePinned: true,
        );

        /** @var array<int, Conversation> $items */
        $items = $paginator->items();
        $conversations = $this->formatConversations(collect($items), $currentUserId);

        $response = [
            'data' => $conversations,
            'next_cursor' => $paginator->nextCursor()?->encode(),
            'has_more' => $paginator->hasMorePages(),
        ];

        if (! $cursor) {
            $response['pinned'] = $pinnedConversations;
        }

        return response()->json($response);
    }

    /**
     * @param  Collection<int, Conversation>  $items
     *
     * @return array<int, array{
     *     id: mixed,
     *     type: string,
     *     name: ?string,
     *     display_name: ?string,
     *     avatar_url: ?string,
     *     is_private: bool,
     *     is_pinned: bool,
     *     notification_preference: string,
     *     unread_count: int,
     *     last_read_at: ?string,
     *     last_message: ?array{
     *         id: mixed,
     *         content: mixed,
     *         author_id: string,
     *         author_name: ?string,
     *         created_at: string,
     *     },
     *     participant_count: int,
     *     created_at: string,
     * }>
     */
    protected function formatConversations(Collection $items, string $currentUserId): array
    {
        $dmConversationIds = $items
            ->filter(fn (Conversation $conversation) => $conversation->type === ConversationType::Direct)
            ->pluck('id')
            ->all();

        $otherParticipants = ConversationParticipant::query()
            ->whereIn('conversation_id', $dmConversationIds)
            ->where('participant_id', '!=', $currentUserId)
            ->with('participant')
            ->get()
            ->keyBy('conversation_id');

        return $items->map(function (Conversation $conversation) use ($otherParticipants): array {
            /** @var bool $isPinned */
            $isPinned = $conversation->getAttribute('current_participant_is_pinned') ?? false;

            /** @var ?string $notificationPreference */
            $notificationPreference = $conversation->getAttribute('current_participant_notification_preference');

            /** @var ?string $lastReadAt */
            $lastReadAt = $conversation->getAttribute('current_participant_last_read_at');

            /** @var int $unreadCount */
            $unreadCount = $conversation->unread_count ?? 0;

            /** @var int $participantCount */
            $participantCount = $conversation->participant_count ?? 0;

            if ($conversation->type === ConversationType::Channel) {
                $displayName = $conversation->name ?? 'Unnamed Channel';
                $avatarUrl = null;
            } else {
                $otherUser = $otherParticipants->get($conversation->getKey())?->participant;
                $displayName = $otherUser instanceof User ? $otherUser->name : 'Unknown User';
                $avatarUrl = $otherUser instanceof User ? Filament::getUserAvatarUrl($otherUser) : null;
            }

            return [
                'id' => $conversation->getKey(),
                'type' => $conversation->type->value,
                'name' => $conversation->name,
                'display_name' => $displayName,
                'avatar_url' => $avatarUrl,
                'is_private' => $conversation->is_private,
                'is_pinned' => $isPinned,
                'notification_preference' => $notificationPreference ?? ConversationNotificationPreference::All->value,
                'unread_count' => $unreadCount,
                'last_read_at' => $lastReadAt ? Carbon::parse($lastReadAt)->toIso8601String() : null,
                'last_message' => $conversation->latestMessage ? [
                    'id' => $conversation->latestMessage->getKey(),
                    'content' => $conversation->latestMessage->content,
                    'author_id' => $conversation->latestMessage->author_id,
                    'author_name' => $conversation->latestMessage->author instanceof User ? $conversation->latestMessage->author->name : null,
                    'created_at' => $conversation->latestMessage->created_at->toIso8601String(),
                ] : null,
                'participant_count' => $participantCount,
                'created_at' => $conversation->created_at->toIso8601String(),
            ];
        })->all();
    }
}
