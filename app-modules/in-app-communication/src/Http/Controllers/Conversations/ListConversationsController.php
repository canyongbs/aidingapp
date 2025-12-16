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
use AidingApp\InAppCommunication\Http\Resources\ConversationParticipantResource;
use AidingApp\InAppCommunication\Models\Conversation;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ListConversationsController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Conversation::class);

        $conversations = app(GetUserConversations::class)($request->user())
            ->map(function (Conversation $conversation) use ($request) {
                $currentParticipant = $conversation->conversationParticipants
                    ->where('participant_type', app(User::class)->getMorphClass())
                    ->where('participant_id', $request->user()->getKey())
                    ->first();

                $participants = ConversationParticipantResource::collection($conversation->conversationParticipants)->resolve();

                $isPinned = $currentParticipant !== null ? $currentParticipant->is_pinned : false;
                $notificationPreference = $currentParticipant !== null
                    ? $currentParticipant->notification_preference->value
                    : ConversationNotificationPreference::All->value;
                $unreadCount = $currentParticipant !== null ? $currentParticipant->unreadCount() : 0;
                $lastReadAt = $currentParticipant?->last_read_at?->toIso8601String();

                return [
                    'id' => $conversation->getKey(),
                    'type' => $conversation->type->value,
                    'name' => $conversation->name,
                    'is_private' => $conversation->is_private,
                    'is_pinned' => $isPinned,
                    'notification_preference' => $notificationPreference,
                    'unread_count' => $unreadCount,
                    'last_read_at' => $lastReadAt,
                    'last_message' => $conversation->latestMessage ? [
                        'id' => $conversation->latestMessage->getKey(),
                        'content' => $conversation->latestMessage->content,
                        'author_id' => $conversation->latestMessage->author_id,
                        'author_name' => $conversation->latestMessage->author instanceof User ? $conversation->latestMessage->author->name : null,
                        'created_at' => $conversation->latestMessage->created_at->toIso8601String(),
                    ] : null,
                    'participants' => $participants,
                    'participant_count' => $conversation->conversationParticipants->count(),
                    'created_at' => $conversation->created_at->toIso8601String(),
                ];
            })
            ->sortByDesc(function (array $conversation) {
                return $conversation['last_message']['created_at'] ?? $conversation['created_at'];
            })
            ->values();

        return response()->json(['data' => $conversations]);
    }
}
