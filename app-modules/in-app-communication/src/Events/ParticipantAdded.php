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

namespace AidingApp\InAppCommunication\Events;

use AidingApp\InAppCommunication\Enums\ConversationType;
use AidingApp\InAppCommunication\Models\ConversationParticipant;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;

class ParticipantAdded implements ShouldBroadcastNow
{
    use Dispatchable;
    use InteractsWithSockets;

    public function __construct(
        public ConversationParticipant $participant,
    ) {}

    public function broadcastAs(): string
    {
        return 'participant.added';
    }

    /**
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        $participantModel = $this->participant->participant;
        $conversation = $this->participant->conversation;
        $conversation->load(['conversationParticipants.participant', 'latestMessage.author']);

        $participants = $conversation->conversationParticipants->map(function (ConversationParticipant $conversationParticipant) {
            $userModel = $conversationParticipant->participant;

            return [
                'id' => $conversationParticipant->getKey(),
                'participant_id' => $conversationParticipant->participant_id,
                'participant_type' => $conversationParticipant->participant_type,
                'participant' => $userModel instanceof User ? [
                    'id' => $userModel->getKey(),
                    'name' => $userModel->name,
                    'avatar_url' => Filament::getUserAvatarUrl($userModel),
                ] : null,
                'is_manager' => $conversationParticipant->is_manager,
            ];
        });

        if ($conversation->type === ConversationType::Channel) {
            $displayName = $conversation->name ?? 'Unnamed Channel';
            $avatarUrl = null;
        } else {
            $otherParticipant = $conversation->conversationParticipants
                ->first(fn (ConversationParticipant $conversationParticipant) => $conversationParticipant->participant_id !== $this->participant->participant_id);
            $otherUser = $otherParticipant?->participant;
            $displayName = $otherUser instanceof User ? $otherUser->name : 'Unknown User';
            $avatarUrl = $otherUser instanceof User ? Filament::getUserAvatarUrl($otherUser) : null;
        }

        $lastMessage = null;

        if ($conversation->latestMessage) {
            $lastMessage = [
                'id' => $conversation->latestMessage->getKey(),
                'content' => $conversation->latestMessage->content,
                'author_id' => $conversation->latestMessage->author_id,
                'author_name' => $conversation->latestMessage->author instanceof User
                    ? $conversation->latestMessage->author->name
                    : null,
                'created_at' => $conversation->latestMessage->created_at->toIso8601String(),
            ];
        }

        return [
            'conversation_id' => $this->participant->conversation_id,
            'id' => $this->participant->getKey(),
            'participant_id' => $this->participant->participant_id,
            'participant_type' => $this->participant->participant_type,
            'participant' => $participantModel instanceof User ? [
                'id' => $participantModel->getKey(),
                'name' => $participantModel->name,
                'avatar_url' => Filament::getUserAvatarUrl($participantModel),
            ] : null,
            'is_manager' => $this->participant->is_manager,
            'conversation' => [
                'id' => $conversation->getKey(),
                'type' => $conversation->type->value,
                'name' => $conversation->name,
                'display_name' => $displayName,
                'avatar_url' => $avatarUrl,
                'is_private' => $conversation->is_private,
                'participants' => $participants->values()->all(),
                'participant_count' => $conversation->conversationParticipants->count(),
                'last_message' => $lastMessage,
                'created_at' => $conversation->created_at->toIso8601String(),
            ],
        ];
    }

    /**
     * @return array<int, PrivateChannel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel("conversation.{$this->participant->conversation_id}"),
            new PrivateChannel("user.{$this->participant->participant_id}"),
        ];
    }
}
