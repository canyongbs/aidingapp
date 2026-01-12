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
      of the licensor in the software. Any use of the licensor's trademarks is subject
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

namespace AidingApp\Ai\Http\Controllers\PortalAssistant;

use AidingApp\Ai\Jobs\PortalAssistant\SendMessage;
use AidingApp\Ai\Models\PortalAssistantThread;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestPriority;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SelectServiceRequestPriorityController
{
    public function __invoke(Request $request): JsonResponse
    {
        $data = $request->validate([
            'priority_id' => ['required', 'uuid'],
            'thread_id' => ['required', 'uuid'],
            'message' => ['required', 'string', 'max:500'],
        ]);

        Log::info('[PortalAssistant] Widget priority selection', [
            'priority_id' => $data['priority_id'],
            'message' => $data['message'],
            'thread_id' => $data['thread_id'],
        ]);

        $author = auth('contact')->user();

        $thread = PortalAssistantThread::query()
            ->whereKey($data['thread_id'])
            ->whereMorphedTo('author', $author)
            ->firstOrFail();

        $priority = ServiceRequestPriority::findOrFail($data['priority_id']);

        if (! $thread->current_service_request_draft_id) {
            Log::warning('[PortalAssistant] Widget priority selection failed - no draft', [
                'thread_id' => $data['thread_id'],
            ]);
            
            return response()->json([
                'message' => 'No active draft found.',
            ], 400);
        }

        $draft = ServiceRequest::withoutGlobalScope('excludeDrafts')
            ->where('id', $thread->current_service_request_draft_id)
            ->where('is_draft', true)
            ->first();

        if (! $draft) {
            Log::warning('[PortalAssistant] Widget priority selection failed - draft not found', [
                'thread_id' => $data['thread_id'],
                'draft_id' => $thread->current_service_request_draft_id,
            ]);
            
            return response()->json([
                'message' => 'Draft not found.',
            ], 404);
        }

        $draft->load('serviceRequestFormSubmission.submissible.type');
        $currentType = $draft->serviceRequestFormSubmission?->submissible?->type;

        if (! $currentType) {
            Log::warning('[PortalAssistant] Widget priority selection failed - no type', [
                'thread_id' => $data['thread_id'],
                'draft_id' => $draft->getKey(),
            ]);
            
            return response()->json([
                'message' => 'Draft has no type.',
            ], 400);
        }

        // Validate priority belongs to the current type
        if ($priority->type_id !== $currentType->getKey()) {
            Log::warning('[PortalAssistant] Widget priority selection failed - wrong type', [
                'thread_id' => $data['thread_id'],
                'priority_id' => $data['priority_id'],
                'priority_type_id' => $priority->type_id,
                'draft_type_id' => $currentType->getKey(),
            ]);
            
            return response()->json([
                'message' => 'Priority does not belong to the current type.',
            ], 400);
        }

        $draft->priority()->associate($priority);
        $draft->save();

        // Let AI respond to guide user to next step
        dispatch(new SendMessage(
            thread: $thread,
            content: $data['message'],
            internalContent: sprintf(
                'User selected priority "%s" for their service request. The draft has been updated. Use get_draft_status to determine what information to collect next.',
                $priority->name
            ),
        ));

        Log::info('[PortalAssistant] Widget priority selection successful', [
            'thread_id' => $data['thread_id'],
            'priority_id' => $data['priority_id'],
            'priority_name' => $priority->name,
        ]);

        return response()->json([
            'message' => 'Message dispatched for processing via websockets.',
            'thread_id' => $thread->getKey(),
        ]);
    }
}
