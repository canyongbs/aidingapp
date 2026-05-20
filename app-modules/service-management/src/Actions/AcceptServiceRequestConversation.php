<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Aiding App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Aiding App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    <https://www.canyongbs.com> or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AidingApp\ServiceManagement\Actions;

use AidingApp\Contact\Models\Contact;
use AidingApp\InAppCommunication\Enums\ConversationType;
use AidingApp\InAppCommunication\Models\Conversation;
use AidingApp\InAppCommunication\Models\ConversationParticipant;
use AidingApp\ServiceManagement\Events\ServiceRequestConversationAccepted;
use AidingApp\ServiceManagement\Models\ServiceRequestConversation;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AcceptServiceRequestConversation
{
    public function execute(ServiceRequestConversation $serviceRequestConversation): void
    {
        if (! $serviceRequestConversation->isPending()) {
            return;
        }

        DB::transaction(function () use ($serviceRequestConversation) {
            $agent = $serviceRequestConversation->user;
            $contact = $serviceRequestConversation->contact;

            $conversation = $this->createConversation($agent, $contact);

            $serviceRequestConversation->update([
                'accepted_at' => now(),
                'conversation_id' => $conversation->getKey(),
            ]);
        });

        broadcast(new ServiceRequestConversationAccepted($serviceRequestConversation->fresh()));
    }

    protected function createConversation(User $agent, Contact $contact): Conversation
    {
        $conversation = new Conversation();
        $conversation->type = ConversationType::Direct;
        $conversation->is_private = true;
        $conversation->creator()->associate($agent);
        $conversation->save();

        $agentParticipant = new ConversationParticipant();
        $agentParticipant->conversation()->associate($conversation);
        $agentParticipant->participant()->associate($agent);
        $agentParticipant->last_activity_at = now();
        $agentParticipant->save();

        $contactParticipant = new ConversationParticipant();
        $agentParticipant->conversation()->associate($conversation);
        $agentParticipant->participant()->associate($contact);
        $contactParticipant->last_activity_at = now();
        $contactParticipant->save();

        return $conversation;
    }
}
