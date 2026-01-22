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

namespace AidingApp\Ai\Tools\PortalAssistant;

use AidingApp\Ai\Actions\PortalAssistant\GetDraftStatus;
use AidingApp\Ai\Models\PortalAssistantThread;
use AidingApp\Ai\Tools\PortalAssistant\Concerns\FindsDraftServiceRequest;
use AidingApp\Portal\Actions\ProcessServiceRequestSubmissionField;
use Prism\Prism\Tool;

class SkipFormFieldTool extends Tool
{
    use FindsDraftServiceRequest;

    public function __construct(
        protected PortalAssistantThread $thread,
    ) {
        $this
            ->as('skip_form_field')
            ->for('Skips an optional form field. Only use when the user indicates they want to skip or says "N/A", "not applicable", "skip", "none", etc.')
            ->withStringParameter('field_id', 'The UUID of the optional field to skip')
            ->using($this);
    }

    /** @phpstan-ignore MeliorStan.parameterNameNotCamelCase (tool parameter names must match AI tool definition) */
    public function __invoke(string $field_id): string
    {
        $draft = $this->findDraft();

        if (! $draft) {
            return json_encode([
                'success' => false,
                'error' => 'No draft exists. Call get_service_request_types_for_suggestion first.',
            ]);
        }

        $draft->load(['priority.type', 'serviceRequestFormSubmission']);

        $type = $draft->priority?->type;

        if (! $type) {
            return json_encode([
                'success' => false,
                'error' => 'No type selected. User must select a type first.',
            ]);
        }

        $form = $type->form;

        if (! $form) {
            return json_encode([
                'success' => false,
                'error' => 'No form found for this service request type.',
            ]);
        }

        $field = $form->fields->firstWhere('id', $field_id);

        if (! $field) {
            return json_encode([
                'success' => false,
                'error' => 'Field not found for this service request type.',
            ]);
        }

        if ($field->is_required) {
            return json_encode([
                'success' => false,
                'error' => 'Cannot skip a required field. This field must be filled.',
            ]);
        }

        $submission = $draft->serviceRequestFormSubmission;

        if (! $submission) {
            return json_encode([
                'success' => false,
                'error' => 'Form submission not created. Please select a type first.',
            ]);
        }

        $existingField = $submission->fields()->where('service_request_form_field_id', $field_id)->first();

        if ($existingField) {
            $submission->fields()->updateExistingPivot($field_id, [
                'response' => '',
            ]);
        } else {
            $fields = collect();

            foreach ($form->fields as $formField) {
                $fields->put($formField->getKey(), $formField->type);
            }

            app(ProcessServiceRequestSubmissionField::class)->execute(
                $submission,
                $field_id,
                '',
                $fields->all(),
            );
        }

        $draftStatus = app(GetDraftStatus::class)->execute($draft);

        return json_encode([
            'success' => true,
            'message' => "Skipped optional field: {$field->label}",
            ...$draftStatus,
        ]);
    }
}
