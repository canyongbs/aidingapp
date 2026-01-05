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

namespace AidingApp\InAppCommunication\Policies;

use AidingApp\InAppCommunication\Enums\ConversationType;
use AidingApp\InAppCommunication\Models\Conversation;
use AidingApp\InAppCommunication\Models\ConversationParticipant;
use App\Enums\Feature;
use App\Models\Authenticatable;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Gate;

class ConversationPolicy
{
    public function before(Authenticatable $authenticatable): ?Response
    {
        if (! Gate::check(Feature::RealtimeChat->getGateName())) {
            return Response::deny('The realtime chat feature is not enabled.');
        }

        return null;
    }

    public function viewAny(Authenticatable $authenticatable): Response
    {
        return Response::allow();
    }

    public function view(Authenticatable $authenticatable, Conversation $conversation): Response
    {
        if ($this->isParticipant($authenticatable, $conversation)) {
            return Response::allow();
        }

        if ($conversation->type === ConversationType::Channel && ! $conversation->is_private) {
            return Response::allow();
        }

        return Response::deny('You do not have permission to view this conversation.');
    }

    public function create(Authenticatable $authenticatable): Response
    {
        return Response::allow();
    }

    public function update(Authenticatable $authenticatable, Conversation $conversation): Response
    {
        if ($conversation->type === ConversationType::Channel) {
            if ($this->isManager($authenticatable, $conversation)) {
                return Response::allow();
            }

            return Response::deny('You do not have permission to update this conversation.');
        }

        return Response::deny('Direct message conversations cannot be updated.');
    }

    public function addParticipant(Authenticatable $authenticatable, Conversation $conversation): Response
    {
        if ($conversation->type !== ConversationType::Channel) {
            return Response::deny('Cannot add participants to direct message conversations.');
        }

        if ($this->isManager($authenticatable, $conversation)) {
            return Response::allow();
        }

        return Response::deny('You do not have permission to add participants to this conversation.');
    }

    public function removeParticipant(Authenticatable $authenticatable, Conversation $conversation): Response
    {
        if ($conversation->type !== ConversationType::Channel) {
            return Response::deny('Cannot remove participants from direct message conversations.');
        }

        if ($this->isManager($authenticatable, $conversation)) {
            return Response::allow();
        }

        return Response::deny('You do not have permission to remove participants from this conversation.');
    }

    public function updateParticipant(Authenticatable $authenticatable, Conversation $conversation): Response
    {
        if ($conversation->type !== ConversationType::Channel) {
            return Response::deny('Cannot update participants in direct message conversations.');
        }

        if ($this->isManager($authenticatable, $conversation)) {
            return Response::allow();
        }

        return Response::deny('You do not have permission to update participants in this conversation.');
    }

    public function leave(Authenticatable $authenticatable, Conversation $conversation): Response
    {
        if ($conversation->type !== ConversationType::Channel) {
            return Response::deny('Cannot leave direct message conversations.');
        }

        if ($this->isParticipant($authenticatable, $conversation)) {
            return Response::allow();
        }

        return Response::deny('You are not a participant in this conversation.');
    }

    public function sendMessage(Authenticatable $authenticatable, Conversation $conversation): Response
    {
        if ($this->isParticipant($authenticatable, $conversation)) {
            return Response::allow();
        }

        return Response::deny('You must be a participant to send messages in this conversation.');
    }

    public function join(Authenticatable $authenticatable, Conversation $conversation): Response
    {
        if ($conversation->type !== ConversationType::Channel) {
            return Response::deny('Cannot join direct message conversations.');
        }

        if ($conversation->is_private) {
            return Response::deny('Cannot join private channels without an invitation.');
        }

        if ($this->isParticipant($authenticatable, $conversation)) {
            return Response::deny('You are already a member of this channel.');
        }

        return Response::allow();
    }

    protected function isParticipant(Authenticatable $authenticatable, Conversation $conversation): bool
    {
        return ConversationParticipant::query()
            ->whereBelongsTo($conversation)
            ->whereMorphedTo('participant', $authenticatable)
            ->exists();
    }

    protected function isManager(Authenticatable $authenticatable, Conversation $conversation): bool
    {
        return ConversationParticipant::query()
            ->whereBelongsTo($conversation)
            ->whereMorphedTo('participant', $authenticatable)
            ->where('is_manager', true)
            ->exists();
    }
}
