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

namespace AidingApp\InAppCommunication\Http\Controllers\Conversations;

use AidingApp\InAppCommunication\Actions\CreateConversation;
use AidingApp\InAppCommunication\Enums\ConversationType;
use AidingApp\InAppCommunication\Http\Resources\ConversationParticipantResource;
use AidingApp\InAppCommunication\Models\Conversation;
use App\Http\Controllers\Controller;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CreateConversationController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $this->authorize('create', Conversation::class);

        $validated = $request->validate([
            'type' => ['required', 'string', 'in:direct,channel'],
            'name' => ['nullable', 'string', 'max:255', 'required_if:type,channel'],
            'participant_ids' => ['nullable', 'array'],
            'participant_ids.*' => ['required', 'uuid', 'exists:users,id'],
            'is_private' => ['nullable', 'boolean'],
        ]);

        if ($validated['type'] === 'direct') {
            if (empty($validated['participant_ids']) || count($validated['participant_ids']) !== 1) {
                abort(422, 'Direct messages require exactly one participant.');
            }
        }

        $conversation = app(CreateConversation::class)(
            creator: $request->user(),
            type: ConversationType::from($validated['type']),
            participantIds: $validated['participant_ids'] ?? [],
            name: $validated['name'] ?? null,
            isPrivate: $validated['is_private'] ?? true,
        );

        $conversation->load('conversationParticipants');

        $participants = ConversationParticipantResource::collection($conversation->conversationParticipants)->resolve();

        if ($conversation->type === ConversationType::Channel) {
            $displayName = $conversation->name ?? 'Unnamed Channel';
            $avatarUrl = null;
        } else {
            $otherParticipant = $conversation->conversationParticipants
                ->first(fn ($otherParticipant) => $otherParticipant->participant_id !== $request->user()->getKey());
            $otherUser = $otherParticipant?->participant;
            $displayName = $otherUser instanceof User ? $otherUser->name : 'Unknown User';
            $avatarUrl = $otherUser instanceof User ? Filament::getUserAvatarUrl($otherUser) : null;
        }

        return response()->json([
            'data' => [
                'id' => $conversation->getKey(),
                'type' => $conversation->type->value,
                'name' => $conversation->name,
                'display_name' => $displayName,
                'avatar_url' => $avatarUrl,
                'is_private' => $conversation->is_private,
                'participants' => $participants,
                'created_at' => $conversation->created_at->toIso8601String(),
            ],
        ], 201);
    }
}
