<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

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

use AidingApp\InAppCommunication\Enums\ConversationType;
use AidingApp\InAppCommunication\Events\ConversationCreated;
use AidingApp\InAppCommunication\Models\Conversation;
use AidingApp\InAppCommunication\Models\ConversationParticipant;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class CreateConversation
{
    /**
     * @param  array<string>  $participantIds
     */
    public function __invoke(
        User $creator,
        ConversationType $type,
        array $participantIds,
        ?string $name = null,
        bool $isPrivate = true,
    ): Conversation {
        if ($type === ConversationType::Direct && count($participantIds) === 1) {
            $existing = $this->findExistingDirect($creator, $participantIds[0]);

            if ($existing) {
                return $existing;
            }
        }

        $conversation = DB::transaction(function () use ($creator, $type, $participantIds, $name, $isPrivate) {
            $conversation = new Conversation();
            $conversation->type = $type;
            $conversation->name = $name;
            $conversation->is_private = $isPrivate;
            $conversation->created_by = $creator->getKey();
            $conversation->save();

            $creatorParticipant = new ConversationParticipant();
            $creatorParticipant->conversation_id = $conversation->getKey();
            $creatorParticipant->participant_type = app(User::class)->getMorphClass();
            $creatorParticipant->participant_id = $creator->getKey();
            $creatorParticipant->is_manager = $type === ConversationType::Channel;
            $creatorParticipant->last_activity_at = now();
            $creatorParticipant->save();

            foreach ($participantIds as $userId) {
                if ($userId !== $creator->getKey()) {
                    $participant = new ConversationParticipant();
                    $participant->conversation_id = $conversation->getKey();
                    $participant->participant_type = app(User::class)->getMorphClass();
                    $participant->participant_id = $userId;
                    $participant->last_activity_at = now();
                    $participant->save();
                }
            }

            return $conversation;
        });

        foreach ($participantIds as $userId) {
            if ($userId !== $creator->getKey()) {
                broadcast(new ConversationCreated($conversation, $userId));
            }
        }

        return $conversation;
    }

    protected function findExistingDirect(User $user1, string $user2Id): ?Conversation
    {
        return Conversation::query()
            ->where('type', ConversationType::Direct)
            ->whereHas(
                'conversationParticipants',
                fn (Builder $query) => $query->whereMorphedTo('participant', $user1),
            )
            ->whereHas(
                'conversationParticipants',
                fn (Builder $query) => $query
                    ->where('participant_type', app(User::class)->getMorphClass())
                    ->where('participant_id', $user2Id)
            )
            ->first();
    }
}
