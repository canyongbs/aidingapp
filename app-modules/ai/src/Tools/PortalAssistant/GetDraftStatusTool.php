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
use AidingApp\Ai\Tools\PortalAssistant\Concerns\FindsDraftServiceRequest;
use AidingApp\Portal\Actions\GenerateServiceRequestForm;
use AidingApp\ServiceManagement\Actions\ResolveUploadsMediaCollectionForServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestFormField;
use Prism\Prism\Tool;

class GetDraftStatusTool extends Tool
{
    use FindsDraftServiceRequest;
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
        $draft = $this->findDraft();

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
            $result['optional_fields'] = $this->getOptionalFields($result['form_fields']);
            $result['can_submit'] = empty($result['missing_required']);
        } else {
            $result['type_id'] = null;
            $result['type_name'] = null;
            $result['priority_id'] = null;
            $result['priority_name'] = null;
            $result['priorities'] = [];
            $result['form_fields'] = [];
            $result['missing_required'] = ['type'];
            $result['optional_fields'] = [];
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
        $hasCustomFields = ! empty($formFields);
        $missing = [];

        // CORRECT flow: fields (if any) → description → title → priority

        // 1. Custom form fields first (if they exist)
        if ($hasCustomFields) {
            foreach ($formFields as $field) {
                if ($field['required'] && ! $field['filled']) {
                    $missing[] = $field['field_id'];
                }
            }
        }

        // 2. Description after form fields (or first if no fields)
        if (empty($draft->close_details)) {
            $missing[] = 'description';
        }

        // 3. Title after description
        if (empty($draft->title)) {
            $missing[] = 'title';
        }

        // 4. Priority after title
        if (empty($draft->priority_id)) {
            $missing[] = 'priority';
        }

        return $missing;
    }

    /**
     * @param array<int, array<string, mixed>> $formFields
     *
     * @return array<int, array<string, mixed>>
     */
    protected function getOptionalFields(array $formFields): array
    {
        $optional = [];

        foreach ($formFields as $field) {
            if (! $field['required'] && ! $field['filled']) {
                $optional[] = [
                    'field_id' => $field['field_id'],
                    'label' => $field['label'],
                    'type' => $field['type'],
                    'step' => $field['step'],
                ];
            }
        }

        return $optional;
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
            return 'Call check_ai_resolution_validity with your confidence score and proposed answer. DO NOT show the resolution to the user until the tool tells you to.';
        }

        return 'Call submit_service_request to submit for human review.';
    }

    /**
     * @param array<string, mixed> $result
     */
    protected function getDataCollectionInstruction(array $result): string
    {
        $missing = $result['missing_required'] ?? [];
        $optionalFields = $result['optional_fields'] ?? [];

        if (empty($missing)) {
            // All required fields filled - automatically transition to clarifying questions
            $draft = $this->findDraft();
            if ($draft && $draft->workflow_phase === 'data_collection') {
                $draft->workflow_phase = 'clarifying_questions';
                $draft->save();

                return 'All required information has been collected. Now ask the first of 3 clarifying questions to better understand the user\'s issue. Make the question specific to their situation based on the information provided.';
            }

            // Check if there are optional fields that might be worth collecting
            if (! empty($optionalFields)) {
                $optionalSummary = implode(', ', array_map(fn ($f) => $f['label'], $optionalFields));
                return sprintf(
                    'All required fields are filled. There are %d optional field(s) available: %s. Based on the conversation context, decide if any would be helpful to collect. If yes, ask for ONE optional field. If no optional fields seem relevant, call get_draft_status again to transition to questions.',
                    count($optionalFields),
                    $optionalSummary
                );
            }

            return 'All required fields are filled. Call get_draft_status again to transition to clarifying questions.';
        }

        $hasCustomFields = ! empty($result['form_fields'] ?? []);

        // Return instruction for ONLY the FIRST missing field
        $firstMissing = $missing[0];

        // CORRECT ORDER: fields (if any) → description → title → priority

        // Handle custom form fields first (if they exist and are missing)
        if ($hasCustomFields && $firstMissing !== 'title' && $firstMissing !== 'description' && $firstMissing !== 'priority') {
            foreach ($result['form_fields'] ?? [] as $field) {
                if ($field['field_id'] === $firstMissing) {
                    // Check if it's a complex field that needs a widget
                    if (in_array($field['type'], ['select', 'radio', 'checkbox', 'checkboxes', 'date', 'file_upload'])) {
                        return sprintf(
                            'Call show_field_input with field_id "%s" to display the %s input for "%s". Then STOP and wait for the user to complete the input.',
                            $field['field_id'],
                            $field['type'],
                            $field['label']
                        );
                    }

                    // Simple text field
                    return sprintf(
                        'Ask the user: "%s" Then STOP and wait for their response. Do NOT call update_form_field until they provide the value in their next message.',
                        $field['label']
                    );
                }
            }
        }

        if ($firstMissing === 'description') {
            if ($hasCustomFields) {
                $filledCount = count(array_filter($result['form_fields'] ?? [], fn ($f) => $f['filled']));
                if ($filledCount > 0) {
                    return 'The user has already provided information through the form fields. Ask if they have any additional details or context to add. If they indicate they have nothing to add, you may use update_description with a brief summary like "See form details above" or similar. Otherwise wait for their response and then call update_description.';
                }
            }

            return 'Ask the user to describe their issue or request in detail. Then STOP and wait for their response. Do NOT call update_description until they provide the description in their next message.';
        }

        if ($firstMissing === 'title') {
            return 'Suggest a concise, descriptive title based on the information collected. Present the suggestion to the user and ask them to confirm or edit it. Then STOP and wait for their response. When they provide/confirm the title, call update_title.';
        }

        if ($firstMissing === 'priority') {
            return 'Call show_priority_selector to display the priority options. Then STOP and wait for the user to select a priority.';
        }

        return 'Call get_draft_status to check what information is still needed.';
    }
}
