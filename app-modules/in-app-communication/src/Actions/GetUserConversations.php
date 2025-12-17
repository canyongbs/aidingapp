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

namespace AidingApp\InAppCommunication\Actions;

use AidingApp\InAppCommunication\Models\Conversation;
use AidingApp\InAppCommunication\Models\Scopes\WithCurrentParticipant;
use App\Models\User;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;

class GetUserConversations
{
    /**
     * @return CursorPaginator<int, Conversation>
     */
    public function __invoke(
        User $user,
        int $limit = 25,
        ?string $cursor = null,
        bool $excludePinned = false,
    ): CursorPaginator {
        $userType = $user->getMorphClass();
        $userId = $user->getKey();

        return Conversation::query()
            ->select('conversations.*')
            ->selectRaw('conversation_participants.last_activity_at as participant_last_activity_at')
            ->selectRaw('conversation_participants.unread_count as unread_count')
            ->join('conversation_participants', function (JoinClause $join) use ($userType, $userId) {
                $join->on('conversation_participants.conversation_id', '=', 'conversations.id')
                    ->where('conversation_participants.participant_type', '=', $userType)
                    ->where('conversation_participants.participant_id', '=', $userId);
            })
            ->when($excludePinned, function (Builder $query) {
                $query->where('conversation_participants.is_pinned', false);
            })
            ->tap(new WithCurrentParticipant($user))
            ->withCount('conversationParticipants as participant_count')
            ->with(['latestMessage.author'])
            ->orderBy('participant_last_activity_at', 'desc')
            ->orderBy('conversations.id', 'desc')
            ->cursorPaginate($limit, ['*'], 'cursor', $cursor);
    }

    /**
     * @return Collection<int, Conversation>
     */
    public function pinned(User $user): Collection
    {
        $userType = $user->getMorphClass();
        $userId = $user->getKey();

        return Conversation::query()
            ->select('conversations.*')
            ->selectRaw('conversation_participants.last_activity_at as participant_last_activity_at')
            ->selectRaw('conversation_participants.unread_count as unread_count')
            ->join('conversation_participants', function (JoinClause $join) use ($userType, $userId) {
                $join->on('conversation_participants.conversation_id', '=', 'conversations.id')
                    ->where('conversation_participants.participant_type', '=', $userType)
                    ->where('conversation_participants.participant_id', '=', $userId);
            })
            ->where('conversation_participants.is_pinned', true)
            ->tap(new WithCurrentParticipant($user))
            ->withCount('conversationParticipants as participant_count')
            ->with(['latestMessage.author'])
            ->orderBy('participant_last_activity_at', 'desc')
            ->orderBy('conversations.id', 'desc')
            ->get();
    }
}
