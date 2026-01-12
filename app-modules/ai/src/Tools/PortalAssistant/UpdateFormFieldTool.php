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

namespace AidingApp\Ai\Tools\PortalAssistant;

use AidingApp\Ai\Models\PortalAssistantThread;
use AidingApp\Ai\Tools\PortalAssistant\Concerns\FindsDraftServiceRequest;
use AidingApp\Ai\Tools\PortalAssistant\Concerns\LogsToolExecution;
use AidingApp\Portal\Actions\GenerateServiceRequestForm;
use AidingApp\Portal\Actions\ProcessServiceRequestSubmissionField;
use AidingApp\ServiceManagement\Actions\ResolveUploadsMediaCollectionForServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestFormField;
use Illuminate\Support\Str;
use Prism\Prism\Tool;

class UpdateFormFieldTool extends Tool
{
    use FindsDraftServiceRequest;
    use LogsToolExecution;

    public function __construct(
        protected PortalAssistantThread $thread,
    ) {
        $this
            ->as('update_form_field')
            ->for('Updates a text-based custom form field value. Use this for simple text fields, text areas, numbers, and emails. For complex fields like selects, dates, or file uploads, use show_field_input instead. IMPORTANT: You MUST ask the user for this field value before calling this tool. Do NOT use information from earlier in the conversation - only use what the user provides in their current response. Once the user provides the value, save it immediately without asking for confirmation.')
            ->withStringParameter('field_id', 'The UUID of the form field')
            ->withStringParameter('value', 'The value to set for the field')
            ->using($this);
    }

    public function __invoke(string $field_id, string $value): string
    {
        $draft = $this->findDraft();

        if (! $draft) {
            $result = json_encode([
                'success' => false,
                'error' => 'No draft exists. Call fetch_service_request_types first.',
            ]);
            $this->logToolResult('update_form_field', $result, ['field_id' => $field_id, 'value' => $value]);
            return $result;
        }

        $draft->load(['priority.type', 'serviceRequestFormSubmission']);

        $type = $draft->priority?->type;

        if (! $type) {
            $result = json_encode([
                'success' => false,
                'error' => 'No type selected. User must select a type first.',
            ]);
            $this->logToolResult('update_form_field', $result, ['field_id' => $field_id, 'value' => $value]);
            return $result;
        }

        $uploadsMediaCollection = app(ResolveUploadsMediaCollectionForServiceRequest::class)();
        $form = app(GenerateServiceRequestForm::class)->execute($type, $uploadsMediaCollection);

        $field = null;

        foreach ($form->steps as $step) {
            foreach ($step->fields as $f) {
                if ($f->getKey() === $field_id) {
                    $field = $f;

                    break 2;
                }
            }
        }

        if (! $field) {
            $result = json_encode([
                'success' => false,
                'error' => 'Field not found for this service request type.',
            ]);
            $this->logToolResult('update_form_field', $result, ['field_id' => $field_id, 'value' => $value]);
            return $result;
        }

        $submission = $draft->serviceRequestFormSubmission;

        if (! $submission) {
            $result = json_encode([
                'success' => false,
                'error' => 'Form submission not created. Please select a type first.',
            ]);
            $this->logToolResult('update_form_field', $result, ['field_id' => $field_id, 'value' => $value]);
            return $result;
        }

        $existingField = $submission->fields()->where('service_request_form_field_id', $field_id)->first();

        if ($existingField) {
            $submission->fields()->updateExistingPivot($field_id, [
                'response' => $value,
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
                $field_id,
                $value,
                $fields->all(),
            );
        }

        $result = json_encode([
            'success' => true,
            'field_id' => $field_id,
            'label' => $field->label,
            'instruction' => 'Field value saved successfully.',
        ]);
        
        $this->logToolResult('update_form_field', $result, [
            'field_id' => $field_id,
            'field_label' => $field->label,
            'value' => $value,
        ]);
        
        return $result;
    }
}
