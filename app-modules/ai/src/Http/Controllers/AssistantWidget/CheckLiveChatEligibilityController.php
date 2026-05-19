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

namespace AidingApp\Ai\Http\Controllers\AssistantWidget;

use AidingApp\Contact\Models\Contact;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestConversation;
use App\Enums\Feature;
use App\Enums\PresenceStatus;
use App\Features\ServiceRequestTypeLiveChatSettingsFeature;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class CheckLiveChatEligibilityController extends Controller
{
    public function __invoke(Request $request, ServiceRequest $serviceRequest): JsonResponse
    {
        $contact = auth('contact')->user() ?? $request->user();

        abort_if(! ($contact instanceof Contact), Response::HTTP_UNAUTHORIZED);
        abort_if(! $serviceRequest->respondent()->is($contact), Response::HTTP_FORBIDDEN);

        if (! Gate::check(Feature::RealtimeChat->getGateName())) {
            return response()->json(['eligible' => false]);
        }

        if (! ServiceRequestTypeLiveChatSettingsFeature::active()) {
            return response()->json(['eligible' => false]);
        }

        $type = $serviceRequest->priority->type;

        if (! $type->is_live_chat_enabled) {
            return response()->json(['eligible' => false]);
        }

        $assignment = $serviceRequest->assignedTo;

        if (! $assignment) {
            return response()->json(['eligible' => false]);
        }

        $agent = $assignment->user;

        if ($agent->presenceStatus() !== PresenceStatus::Active) {
            return response()->json(['eligible' => false]);
        }

        if ($type->max_simultaneous_chats !== null) {
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
                return response()->json(['eligible' => false]);
            }
        }

        return response()->json([
            'eligible' => true,
            'agent_name' => $agent->name,
        ]);
    }
}
