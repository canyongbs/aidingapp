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
use AidingApp\Contact\Models\Contact;
use AidingApp\Portal\Actions\GenerateServiceRequestForm;
use AidingApp\ServiceManagement\Actions\ResolveUploadsMediaCollectionForServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SelectServiceRequestTypeController
{
    public function __invoke(Request $request): JsonResponse
    {
        $data = $request->validate([
            'type_id' => ['required', 'uuid'],
            'thread_id' => ['required', 'uuid'],
            'message' => ['required', 'string', 'max:500'],
        ]);

        $author = auth('contact')->user();

        $thread = PortalAssistantThread::query()
            ->whereKey($data['thread_id'])
            ->whereMorphedTo('author', $author)
            ->firstOrFail();

        $type = ServiceRequestType::whereHas('form')
            ->findOrFail($data['type_id']);

        // Check if a draft already exists for this type (user switching back)
        $existingDraft = ServiceRequest::withoutGlobalScope('excludeDrafts')
            ->where('portal_assistant_thread_id', $thread->getKey())
            ->where('is_draft', true)
            ->whereHas('priority', function ($query) use ($type) {
                $query->where('type_id', $type->getKey());
            })
            ->first();

        // If draft exists for this type, switch to it
        if ($existingDraft) {
            $thread->current_service_request_draft_id = $existingDraft->getKey();
            $thread->save();
        } else {
            // Create new draft for this type
            $attributes = [
                'is_draft' => true,
                'workflow_phase' => 'data_collection',
                'clarifying_questions' => [],
                'portal_assistant_thread_id' => $thread->getKey(),
            ];

            if ($author instanceof Contact) {
                $attributes['respondent_id'] = $author->getKey();
            }

            $draft = ServiceRequest::create($attributes);

            $defaultPriority = $type->priorities()->orderByDesc('order')->first();

            if ($defaultPriority) {
                $draft->priority()->associate($defaultPriority);
            }

            $uploadsMediaCollection = app(ResolveUploadsMediaCollectionForServiceRequest::class)();
            $form = app(GenerateServiceRequestForm::class)->execute($type, $uploadsMediaCollection);

            $submission = $form->submissions()->make([
                'submitted_at' => null,
            ]);

            if ($defaultPriority) {
                $submission->priority()->associate($defaultPriority);
            }

            $submission->save();

            $draft->serviceRequestFormSubmission()->associate($submission);
            $draft->save();

            // Set as current draft on thread
            $thread->current_service_request_draft_id = $draft->getKey();
            $thread->save();
        }

        // Let AI respond to guide user to next step
        dispatch(new SendMessage(
            thread: $thread,
            content: $data['message'],
            internalContent: sprintf(
                'User selected service request type "%s". The draft has been updated with this type and a default priority. Use get_draft_status to determine what information to collect next.',
                $type->name
            ),
        ));

        return response()->json([
            'message' => 'Message dispatched for processing via websockets.',
            'thread_id' => $thread->getKey(),
        ]);
    }
}
