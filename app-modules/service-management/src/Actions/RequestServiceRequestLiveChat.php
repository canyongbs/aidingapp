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
      of the licensor in the software. Any use of the licensor's trademarks is subject
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
use AidingApp\ServiceManagement\Events\ServiceRequestChatQueued;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestConversation;
use AidingApp\ServiceManagement\Notifications\LiveChatConversationQueued;
use App\Enums\Feature;
use App\Enums\PresenceStatus;
use App\Features\ServiceRequestTypeLiveChatSettingsFeature;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

class RequestServiceRequestLiveChat
{
    public function execute(ServiceRequest $serviceRequest, Contact $contact): ServiceRequestConversation
    {
        $assignment = $serviceRequest->assignedTo;

        if (! $assignment) {
            throw ValidationException::withMessages(['live_chat' => 'No agent is assigned to this service request.']);
        }

        $agent = $assignment->user;

        $this->validateEligibility($serviceRequest, $agent);
        $this->validateCapacity($serviceRequest, $agent);

        $conversation = ServiceRequestConversation::create([
            'service_request_id' => $serviceRequest->getKey(),
            'contact_id' => $contact->getKey(),
            'user_id' => $agent->getKey(),
            'queued_at' => now(),
        ]);

        $agent->notify(new LiveChatConversationQueued($conversation));

        broadcast(new ServiceRequestChatQueued($conversation));

        return $conversation;
    }

    protected function validateEligibility(ServiceRequest $serviceRequest, User $agent): void
    {
        if (! Gate::check(Feature::RealtimeChat->getGateName())) {
            throw ValidationException::withMessages(['live_chat' => 'Live chat is not available.']);
        }

        if (! ServiceRequestTypeLiveChatSettingsFeature::active()) {
            throw ValidationException::withMessages(['live_chat' => 'Live chat is not available.']);
        }

        $type = $serviceRequest->priority->type;

        if (! $type->is_live_chat_enabled) {
            throw ValidationException::withMessages(['live_chat' => 'Live chat is not enabled for this service request type.']);
        }

        if ($agent->presenceStatus() !== PresenceStatus::Active) {
            throw ValidationException::withMessages(['live_chat' => 'The assigned agent is not currently available.']);
        }
    }

    protected function validateCapacity(ServiceRequest $serviceRequest, User $agent): void
    {
        $type = $serviceRequest->priority->type;

        if ($type->max_simultaneous_chats === null) {
            return;
        }

        $count = ServiceRequestConversation::query()
            ->whereBelongsTo($agent)
            ->whereHas('serviceRequest.priority', fn (Builder $query) => $query->whereBelongsTo($type, 'type'))
            ->where(
                fn (Builder $query) => $query
                    ->whereNotNull('queued_at')
                    ->whereNull('accepted_at')
                    ->whereNull('finished_at')
                    ->orWhere(fn (Builder $query) => $query
                        ->whereNotNull('accepted_at')
                        ->whereNull('finished_at'))
            )
            ->count();

        if ($count >= $type->max_simultaneous_chats) {
            throw ValidationException::withMessages(['live_chat' => 'The agent has reached their maximum number of simultaneous chats.']);
        }
    }
}
