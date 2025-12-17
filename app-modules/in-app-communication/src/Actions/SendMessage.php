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

use AidingApp\InAppCommunication\Enums\ConversationNotificationPreference;
use AidingApp\InAppCommunication\Events\MessageSent;
use AidingApp\InAppCommunication\Events\UnreadCountUpdated;
use AidingApp\InAppCommunication\Models\Conversation;
use AidingApp\InAppCommunication\Models\ConversationParticipant;
use AidingApp\InAppCommunication\Models\Message;
use AidingApp\InAppCommunication\Services\ConversationPresence;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class SendMessage
{
    public function __construct(
        protected ConversationPresence $presence,
    ) {}

    /**
     * @param  array<string, mixed>  $content
     */
    public function __invoke(
        Conversation $conversation,
        User $author,
        array $content,
    ): Message {
        $presentUserIds = $this->presence->getPresentUserIds($conversation->getKey());

        return DB::transaction(function () use ($conversation, $author, $content, $presentUserIds) {
            $message = new Message();
            $message->conversation_id = $conversation->getKey();
            $message->author_type = app(User::class)->getMorphClass();
            $message->author_id = $author->getKey();
            $message->content = $content;
            $message->save();

            $userType = app(User::class)->getMorphClass();

            ConversationParticipant::query()
                ->where('conversation_id', $conversation->getKey())
                ->where('participant_type', $userType)
                ->where('participant_id', $author->getKey())
                ->update([
                    'last_read_at' => now(),
                    'last_activity_at' => now(),
                ]);

            ConversationParticipant::query()
                ->where('conversation_id', $conversation->getKey())
                ->where('participant_id', '!=', $author->getKey())
                ->update([
                    'last_activity_at' => now(),
                ]);

            $affectedAllParticipantIds = ConversationParticipant::query()
                ->where('conversation_id', $conversation->getKey())
                ->where('participant_id', '!=', $author->getKey())
                ->whereNotIn('participant_id', $presentUserIds)
                ->where('notification_preference', ConversationNotificationPreference::All)
                ->pluck('participant_id')
                ->all();

            if (count($affectedAllParticipantIds) > 0) {
                ConversationParticipant::query()
                    ->where('conversation_id', $conversation->getKey())
                    ->whereIn('participant_id', $affectedAllParticipantIds)
                    ->update([
                        'unread_count' => DB::raw('unread_count + 1'),
                    ]);
            }

            $mentionedUserIds = $message->getMentionedUserIds();
            $affectedMentionParticipantIds = [];

            if (count($mentionedUserIds) > 0) {
                $affectedMentionParticipantIds = ConversationParticipant::query()
                    ->where('conversation_id', $conversation->getKey())
                    ->where('participant_id', '!=', $author->getKey())
                    ->whereNotIn('participant_id', $presentUserIds)
                    ->where('notification_preference', ConversationNotificationPreference::Mentions)
                    ->whereIn('participant_id', $mentionedUserIds)
                    ->pluck('participant_id')
                    ->all();

                if (count($affectedMentionParticipantIds) > 0) {
                    ConversationParticipant::query()
                        ->where('conversation_id', $conversation->getKey())
                        ->whereIn('participant_id', $affectedMentionParticipantIds)
                        ->update([
                            'unread_count' => DB::raw('unread_count + 1'),
                        ]);
                }
            }

            broadcast(new MessageSent($message));

            $allAffectedParticipantIds = array_unique(array_merge(
                $affectedAllParticipantIds,
                $affectedMentionParticipantIds
            ));

            if (count($allAffectedParticipantIds) > 0) {
                $unreadCounts = ConversationParticipant::query()
                    ->where('conversation_id', $conversation->getKey())
                    ->whereIn('participant_id', $allAffectedParticipantIds)
                    ->pluck('unread_count', 'participant_id');

                foreach ($allAffectedParticipantIds as $participantId) {
                    broadcast(new UnreadCountUpdated(
                        userId: $participantId,
                        conversationId: $conversation->getKey(),
                        unreadCount: $unreadCounts->get($participantId, 0),
                    ));
                }
            }

            return $message;
        });
    }
}
