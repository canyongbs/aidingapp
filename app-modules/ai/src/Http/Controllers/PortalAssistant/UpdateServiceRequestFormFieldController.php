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

namespace AidingApp\Ai\Http\Controllers\PortalAssistant;

use AidingApp\Ai\Actions\PortalAssistant\GetDraftStatus;
use AidingApp\Ai\Jobs\PortalAssistant\SendMessage;
use AidingApp\Ai\Models\PortalAssistantThread;
use AidingApp\Portal\Actions\ProcessServiceRequestSubmissionField;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestFormField;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UpdateServiceRequestFormFieldController
{
    public function __invoke(Request $request): JsonResponse
    {
        $data = $request->validate([
            'field_id' => ['required', 'uuid'],
            'value' => ['required'],
            'thread_id' => ['required', 'uuid'],
            'message' => ['required', 'string', 'max:500'],
        ]);

        $author = auth('contact')->user();

        $thread = PortalAssistantThread::query()
            ->whereKey($data['thread_id'])
            ->whereMorphedTo('author', $author)
            ->firstOrFail();

        $field = ServiceRequestFormField::findOrFail($data['field_id']);

        $draft = ServiceRequest::withoutGlobalScope('excludeDrafts')
            ->where('portal_assistant_thread_id', $thread->getKey())
            ->where('is_draft', true)
            ->with(['priority.type', 'serviceRequestFormSubmission'])
            ->latest()
            ->first();

        if (! $draft || ! $draft->serviceRequestFormSubmission) {
            return response()->json([
                'message' => 'No active draft found.',
            ], 400);
        }

        $type = $draft->priority?->type;

        if (! $type) {
            return response()->json([
                'message' => 'Draft has no type.',
            ], 400);
        }

        $form = $type->form;

        if (! $form) {
            return response()->json([
                'message' => 'No form found for this service request type.',
            ], 400);
        }

        // Validate field belongs to this form
        $fieldConfig = $form->fields->firstWhere('id', $field->getKey());

        if (! $fieldConfig) {
            return response()->json([
                'message' => 'Field does not belong to this form.',
            ], 400);
        }

        $submission = $draft->serviceRequestFormSubmission;
        $existingField = $submission->fields()->where('service_request_form_field_id', $field->getKey())->first();

        if ($existingField) {
            $submission->fields()->updateExistingPivot($field->getKey(), [
                'response' => $data['value'],
            ]);
        } else {
            $fields = collect();

            foreach ($form->fields as $formField) {
                $fields->put($formField->getKey(), $formField->type);
            }

            app(ProcessServiceRequestSubmissionField::class)->execute(
                $submission,
                $field->getKey(),
                $data['value'],
                $fields->all(),
            );
        }

        // Get updated draft status
        $draft->refresh();
        $draftStatus = app(GetDraftStatus::class)->execute($draft);

        // Let AI respond to guide user to next step
        dispatch(new SendMessage(
            thread: $thread,
            content: $data['message'],
            internalContent: json_encode([
                'event' => 'field_updated',
                'note' => "The \"{$field->label}\" field was saved via widget. Do NOT call update_form_field for it. Proceed to the next field.",
                ...$draftStatus,
            ]),
        ));

        return response()->json([
            'message' => 'Message dispatched for processing via websockets.',
            'thread_id' => $thread->getKey(),
        ]);
    }
}
