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
use AidingApp\Portal\Actions\GenerateServiceRequestForm;
use AidingApp\Portal\Actions\ProcessServiceRequestSubmissionField;
use AidingApp\ServiceManagement\Actions\ResolveUploadsMediaCollectionForServiceRequest;
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

        $uploadsMediaCollection = app(ResolveUploadsMediaCollectionForServiceRequest::class)();
        $form = app(GenerateServiceRequestForm::class)->execute($type, $uploadsMediaCollection);

        // Validate field belongs to this form
        $fieldExists = false;
        $fieldConfig = null;
        foreach ($form->steps as $step) {
            foreach ($step->fields as $f) {
                if ($f->getKey() === $field->getKey()) {
                    $fieldExists = true;
                    $fieldConfig = $f;
                    break 2;
                }
            }
        }

        if (! $fieldExists) {
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

            foreach ($form->steps as $step) {
                foreach ($step->fields as $f) {
                    $fields->put($f->getKey(), $f->type);
                }
            }

            app(ProcessServiceRequestSubmissionField::class)->execute(
                $submission,
                $field->getKey(),
                $data['value'],
                $fields->all(),
            );
        }

        // Build helpful internal content based on field type
        $internalContent = $this->buildInternalContent($field, $fieldConfig, $data['value']);

        // Let AI respond to guide user to next step
        dispatch(new SendMessage(
            thread: $thread,
            content: $data['message'],
            internalContent: $internalContent,
        ));

        return response()->json([
            'message' => 'Field update processed.',
        ]);
    }

    protected function buildInternalContent(ServiceRequestFormField $field, $fieldConfig, mixed $value): string
    {
        $fieldType = $fieldConfig?->type ?? 'text';
        $fieldLabel = $field->label;

        // Handle different field types
        $valueDescription = match ($fieldType) {
            'upload' => is_array($value) && isset($value['name'])
                ? sprintf('uploaded file "%s"', $value['name'])
                : 'uploaded a file',
            'date' => sprintf('selected date "%s"', $value),
            'select', 'radio' => sprintf('selected "%s"', $value),
            'checkbox' => $value ? 'checked the box' : 'unchecked the box',
            'address' => is_array($value)
                ? sprintf('entered address: %s', implode(', ', array_filter($value)))
                : sprintf('entered "%s"', $value),
            'phone' => sprintf('entered phone "%s"', $value),
            'email' => sprintf('entered email "%s"', $value),
            'textarea' => strlen($value) > 50
                ? sprintf('entered text (%.50s...)', $value)
                : sprintf('entered "%s"', $value),
            default => strlen($value) > 50
                ? sprintf('entered "%.50s..."', $value)
                : sprintf('entered "%s"', $value),
        };

        return sprintf(
            '[System: User completed field "%s" - %s. Value saved. Call get_draft_status for next step.]',
            $fieldLabel,
            $valueDescription
        );
    }
}
