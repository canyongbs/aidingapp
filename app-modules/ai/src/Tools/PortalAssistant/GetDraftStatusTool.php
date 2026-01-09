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
use AidingApp\Ai\Settings\AiResolutionSettings;
use AidingApp\Portal\Actions\GenerateServiceRequestForm;
use AidingApp\ServiceManagement\Actions\ResolveUploadsMediaCollectionForServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestFormField;
use Prism\Prism\Tool;

class GetDraftStatusTool extends Tool
{
    public function __construct(
        protected PortalAssistantThread $thread,
    ) {
        $this
            ->as('get_draft_status')
            ->for('Returns the current state of the service request draft, including form fields. Call this after type selection to get form structure, or anytime to check what information has been collected.')
            ->using($this);
    }

    public function __invoke(): string
    {
        $draft = $this->thread->draftServiceRequest;

        if (! $draft) {
            return json_encode([
                'has_draft' => false,
            ]);
        }

        $draft->load(['priority', 'priority.type', 'serviceRequestFormSubmission']);

        $result = [
            'has_draft' => true,
            'workflow_phase' => $draft->workflow_phase,
            'title' => $draft->title,
            'description' => $draft->close_details,
        ];

        $type = $draft->priority?->type;

        if ($type) {
            $result['type_id'] = $type->getKey();
            $result['type_name'] = $type->name;
            $result['priority_id'] = $draft->priority->getKey();
            $result['priority_name'] = $draft->priority->name;

            $result['priorities'] = $type->priorities()
                ->orderByDesc('order')
                ->get(['id', 'name'])
                ->map(fn ($p) => ['id' => $p->id, 'name' => $p->name])
                ->values()
                ->all();

            $result['form_fields'] = $this->getFormFields($draft, $type);
            $result['missing_required'] = $this->getMissingRequired($draft, $result['form_fields']);
            $result['can_submit'] = empty($result['missing_required']);
        } else {
            $result['type_id'] = null;
            $result['type_name'] = null;
            $result['priority_id'] = null;
            $result['priority_name'] = null;
            $result['priorities'] = [];
            $result['form_fields'] = [];
            $result['missing_required'] = ['type'];
            $result['can_submit'] = false;
        }

        $clarifyingQuestions = $draft->clarifying_questions ?? [];
        $result['clarifying_questions'] = [
            'completed' => count($clarifyingQuestions),
            'required' => 3,
        ];

        $result['instruction'] = $this->getPhaseInstruction($draft, $result);

        return json_encode($result);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function getFormFields(ServiceRequest $draft, mixed $type): array
    {
        $uploadsMediaCollection = app(ResolveUploadsMediaCollectionForServiceRequest::class)();
        $form = app(GenerateServiceRequestForm::class)->execute($type, $uploadsMediaCollection);

        $submission = $draft->serviceRequestFormSubmission;
        $filledFields = [];

        if ($submission) {
            $filledFields = $submission->fields()
                ->get()
                ->keyBy('id')
                ->map(fn ($field) => $field->pivot->response)
                ->all();
        }

        $fields = [];

        foreach ($form->steps as $step) {
            if ($step->label === 'Main' || $step->label === 'Questions') {
                continue;
            }

            foreach ($step->fields as $field) {
                $fields[] = $this->formatField($field, $step->label, $filledFields);
            }
        }

        return $fields;
    }

    /**
     * @param array<string, mixed> $filledFields
     *
     * @return array<string, mixed>
     */
    protected function formatField(ServiceRequestFormField $field, string $stepLabel, array $filledFields): array
    {
        $fieldId = $field->getKey();
        $value = $filledFields[$fieldId] ?? null;

        $data = [
            'field_id' => $fieldId,
            'step' => $stepLabel,
            'label' => $field->label,
            'type' => $field->type,
            'required' => (bool) $field->is_required,
            'value' => $value,
            'filled' => $value !== null && $value !== '',
        ];

        if (isset($field->config['options'])) {
            $data['options'] = $field->config['options'];
        }

        if (isset($field->config['placeholder'])) {
            $data['placeholder'] = $field->config['placeholder'];
        }

        return $data;
    }

    /**
     * @param array<int, array<string, mixed>> $formFields
     *
     * @return array<int, string>
     */
    protected function getMissingRequired(ServiceRequest $draft, array $formFields): array
    {
        $missing = [];

        if (empty($draft->title)) {
            $missing[] = 'title';
        }

        if (empty($draft->close_details)) {
            $missing[] = 'description';
        }

        foreach ($formFields as $field) {
            if ($field['required'] && ! $field['filled']) {
                $missing[] = $field['field_id'];
            }
        }

        return $missing;
    }

    /**
     * @param array<string, mixed> $result
     */
    protected function getPhaseInstruction(ServiceRequest $draft, array $result): string
    {
        return match ($draft->workflow_phase) {
            'type_selection' => 'User needs to select a type. Use show_type_selector to display the type selection UI.',
            'data_collection' => $this->getDataCollectionInstruction($result),
            'clarifying_questions' => sprintf(
                'Ask clarifying question %d of 3. After user answers, save with save_clarifying_question.',
                ($result['clarifying_questions']['completed'] ?? 0) + 1
            ),
            'resolution' => $this->getResolutionInstruction(),
            default => 'Check the workflow_phase and proceed accordingly.',
        };
    }

    protected function getResolutionInstruction(): string
    {
        $aiResolutionSettings = app(AiResolutionSettings::class);

        if ($aiResolutionSettings->is_enabled) {
            return 'Attempt to resolve using submit_ai_resolution with your confidence score and proposed answer.';
        }

        return 'Call finalize_service_request to submit for human review.';
    }

    /**
     * @param array<string, mixed> $result
     */
    protected function getDataCollectionInstruction(array $result): string
    {
        $missing = $result['missing_required'] ?? [];

        if (empty($missing)) {
            return 'All required fields are filled. Call submit_service_request to validate and proceed to clarifying questions.';
        }

        $missingLabels = [];

        if (in_array('title', $missing)) {
            $missingLabels[] = 'title';
        }

        if (in_array('description', $missing)) {
            $missingLabels[] = 'description';
        }

        foreach ($result['form_fields'] ?? [] as $field) {
            if (in_array($field['field_id'], $missing)) {
                $missingLabels[] = $field['label'];
            }
        }

        return 'Collect missing required fields: ' . implode(', ', $missingLabels) . '. Use update_title, update_description, update_form_field for text fields, or show_field_input for complex fields.';
    }
}
