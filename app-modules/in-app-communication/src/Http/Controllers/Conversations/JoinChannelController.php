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

use AidingApp\InAppCommunication\Actions\JoinChannel;
use AidingApp\InAppCommunication\Enums\ConversationNotificationPreference;
use AidingApp\InAppCommunication\Models\Conversation;
use AidingApp\InAppCommunication\Models\ConversationParticipant;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JoinChannelController extends Controller
{
    public function __invoke(Request $request, Conversation $conversation): JsonResponse
    {
        $this->authorize('join', $conversation);

        $user = $request->user();
        assert($user instanceof User);

        app(JoinChannel::class)(
            conversation: $conversation,
            user: $user,
        );

        $conversation->load('conversationParticipants');

        $participants = $conversation->conversationParticipants->map(function (ConversationParticipant $conversationParticipant) {
            $participantModel = $conversationParticipant->participant;

            return [
                'id' => $conversationParticipant->getKey(),
                'participant_id' => $conversationParticipant->participant_id,
                'participant_type' => $conversationParticipant->participant_type,
                'participant' => $participantModel instanceof User ? [
                    'id' => $participantModel->getKey(),
                    'name' => $participantModel->name,
                    'avatar_url' => $participantModel->getFilamentAvatarUrl(),
                ] : null,
                'is_manager' => $conversationParticipant->is_manager,
            ];
        });

        return response()->json([
            'data' => [
                'id' => $conversation->getKey(),
                'type' => $conversation->type->value,
                'name' => $conversation->name,
                'is_private' => $conversation->is_private,
                'is_pinned' => false,
                'notification_preference' => ConversationNotificationPreference::All->value,
                'unread_count' => 0,
                'last_message' => null,
                'participants' => $participants->values()->all(),
                'participant_count' => $conversation->conversationParticipants->count(),
                'created_at' => $conversation->created_at->toIso8601String(),
            ],
        ], 201);
    }
}
