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

namespace AidingApp\Ai\Actions\PortalAssistant;

use AidingApp\Ai\Settings\AiResolutionSettings;
use AidingApp\Form\Filament\Blocks\TextAreaFormFieldBlock;
use AidingApp\Form\Filament\Blocks\TextInputFormFieldBlock;
use AidingApp\ServiceManagement\Enums\ServiceRequestDraftStage;
use AidingApp\ServiceManagement\Enums\ServiceRequestUpdateType;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestFormField;

class GetDraftStatus
{
    /**
     * @return array<string, mixed>
     */
    public function execute(ServiceRequest $draft): array
    {
        $draft->load(['priority', 'priority.type', 'serviceRequestFormSubmission']);

        $draftStage = ServiceRequestDraftStage::fromServiceRequest($draft);
        $type = $draft->priority?->type;

        $result = [
            'draft_stage' => $draftStage?->value,
            'type_name' => $type?->name,
        ];

        // Stage-specific data
        if ($draftStage === ServiceRequestDraftStage::DataCollection) {
            $result = array_merge($result, $this->getDataCollectionData($draft, $type));
        } else {
            // For clarifying_questions and resolution stages, include context info
            $result['title'] = $draft->title;
            $result['description'] = $draft->close_details;

            $questionsCompleted = $draft->serviceRequestUpdates()
                ->where('update_type', ServiceRequestUpdateType::ClarifyingQuestion)
                ->count();

            $result['questions_completed'] = $questionsCompleted;
        }

        $result['next_instruction'] = $this->getStageInstruction($draftStage, $draft, $result);

        return $result;
    }

    /**
     * @return array<string, mixed>
     */
    protected function getDataCollectionData(ServiceRequest $draft, mixed $type): array
    {
        $data = [
            'title' => $draft->title,
            'description' => $draft->close_details,
        ];

        if ($type) {
            $formFields = $this->getFormFields($draft, $type);
            $data['missing_required_fields'] = $this->getMissingRequiredFields($draft, $formFields);
            $data['missing_optional_fields'] = $this->getMissingOptionalFields($formFields);
            // Track if the form has any custom fields at all (for adjusting description prompt)
            $data['has_custom_form_fields'] = ! empty($formFields);
        } else {
            $data['missing_required_fields'] = [];
            $data['missing_optional_fields'] = [];
            $data['has_custom_form_fields'] = false;
        }

        return $data;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function getFormFields(ServiceRequest $draft, mixed $type): array
    {
        $form = $type->form;

        if (! $form) {
            return [];
        }

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

        foreach ($form->fields as $field) {
            $fields[] = $this->formatField($field, $filledFields);
        }

        return $fields;
    }

    /**
     * @param array<string, mixed> $filledFields
     *
     * @return array<string, mixed>
     */
    protected function formatField(ServiceRequestFormField $field, array $filledFields): array
    {
        $fieldId = $field->getKey();
        $value = $filledFields[$fieldId] ?? null;

        $isTextField = in_array($field->type, [
            TextInputFormFieldBlock::type(),
            TextAreaFormFieldBlock::type(),
        ]);

        $data = [
            'field_id' => $fieldId,
            'label' => $field->label,
            'type' => $field->type,
            'required' => (bool) $field->is_required,
            'value' => $value,
            'filled' => $value !== null && $value !== '',
            'collection_method' => $isTextField ? 'text' : 'show_field_input',
        ];

        if (isset($field->config['options'])) {
            $data['options'] = $field->config['options'];
        }

        return $data;
    }

    /**
     * Get missing required fields with full field info for actionable instructions.
     *
     * @param array<int, array<string, mixed>> $formFields
     *
     * @return array<int, array<string, mixed>>
     */
    protected function getMissingRequiredFields(ServiceRequest $draft, array $formFields): array
    {
        $missing = [];

        // Check custom form fields
        foreach ($formFields as $field) {
            if ($field['required'] && ! $field['filled']) {
                $fieldData = [
                    'field_id' => $field['field_id'],
                    'label' => $field['label'],
                    'type' => $field['type'],
                    'collection_method' => $field['collection_method'],
                ];

                if (isset($field['options'])) {
                    $fieldData['options'] = $field['options'];
                }

                $missing[] = $fieldData;
            }
        }

        // Check description
        if (empty($draft->close_details)) {
            $missing[] = [
                'field_id' => 'description',
                'label' => 'Description',
                'type' => 'description',
            ];
        }

        // Check title
        if (empty($draft->title)) {
            $missing[] = [
                'field_id' => 'title',
                'label' => 'Title',
                'type' => 'title',
            ];
        }

        return $missing;
    }

    /**
     * Get optional fields that haven't been filled yet.
     *
     * @param array<int, array<string, mixed>> $formFields
     *
     * @return array<int, array<string, mixed>>
     */
    protected function getMissingOptionalFields(array $formFields): array
    {
        $optional = [];

        foreach ($formFields as $field) {
            if (! $field['required'] && ! $field['filled']) {
                $fieldData = [
                    'field_id' => $field['field_id'],
                    'label' => $field['label'],
                    'type' => $field['type'],
                    'collection_method' => $field['collection_method'],
                ];

                if (isset($field['options'])) {
                    $fieldData['options'] = $field['options'];
                }

                $optional[] = $fieldData;
            }
        }

        return $optional;
    }

    /**
     * @param array<string, mixed> $result
     */
    protected function getStageInstruction(?ServiceRequestDraftStage $draftStage, ServiceRequest $draft, array $result): string
    {
        if (! $draftStage) {
            return 'Draft stage could not be determined.';
        }

        return match ($draftStage) {
            ServiceRequestDraftStage::DataCollection => $this->getDataCollectionInstruction($result),
            ServiceRequestDraftStage::ClarifyingQuestions => $this->getClarifyingQuestionsInstruction($result),
            ServiceRequestDraftStage::Resolution => $this->getResolutionInstruction(),
        };
    }

    protected function getResolutionInstruction(): string
    {
        $aiResolutionSettings = app(AiResolutionSettings::class);

        if ($aiResolutionSettings->is_enabled) {
            return 'Based on everything the user told you, formulate a helpful resolution. Call check_ai_resolution_validity(confidence_score=<0-100>, proposed_answer="<your detailed resolution>"). The tool will tell you whether to present it or auto-submit for human review.';
        }

        return 'AI resolution is disabled. Service request will be submitted for human review.';
    }

    /**
     * @param array<string, mixed> $result
     */
    protected function getClarifyingQuestionsInstruction(array $result): string
    {
        $completed = $result['questions_completed'] ?? 0;
        $remaining = 3 - $completed;

        if ($remaining === 0) {
            return 'All 3 clarifying questions complete. Proceed to resolution stage.';
        }

        return sprintf(
            'Question %d of 3 (%d remaining). Ask a clarifying question to better understand their issue. Do NOT attempt to resolve yet - you will have the opportunity to provide a resolution AFTER all 3 questions are saved. After they answer, you MUST call save_clarifying_question_answer(question="<your question>", answer="<their answer>") to record it.',
            $completed + 1,
            $remaining
        );
    }

    /**
     * @param array<string, mixed> $result
     */
    protected function getDataCollectionInstruction(array $result): string
    {
        $missingRequired = $result['missing_required_fields'] ?? [];
        $missingOptional = $result['missing_optional_fields'] ?? [];

        // Build optional fields note if any exist
        $optionalNote = '';

        if (! empty($missingOptional)) {
            $optionalLabels = array_map(fn ($f) => $f['label'], $missingOptional);
            $optionalNote = sprintf(
                ' Optional fields available if relevant: %s. Only ask about these if they seem useful based on context.',
                implode(', ', $optionalLabels)
            );
        }

        if (empty($missingRequired)) {
            // All required fields collected
            if (! empty($missingOptional)) {
                $optionalLabels = array_map(fn ($f) => strtolower($f['label']), $missingOptional);

                return sprintf(
                    'All required info collected. If any of these optional fields seem relevant based on the conversation, ask about them: %s. Use update_form_field(field_id="<id>", value="<response>") to save. Otherwise, transition to clarifying questions.',
                    implode(', ', $optionalLabels)
                );
            }

            return 'All required information collected. Transition to asking clarifying questions.';
        }

        $firstMissing = $missingRequired[0];
        $fieldId = $firstMissing['field_id'];
        $fieldLabel = $firstMissing['label'];
        $fieldType = $firstMissing['type'];

        // Handle description field
        if ($fieldId === 'description') {
            // Check if there were any form fields (required or optional) that we've already collected
            $hasFormFields = $this->hasCollectedFormFields($result);

            if ($hasFormFields) {
                return 'Call enable_file_attachments() first. Then ask: "Is there anything else you\'d like to add about this request? Feel free to attach any files if helpful." After they respond, call update_description(description="<their response>").' . $optionalNote;
            }

            return 'Call enable_file_attachments() first. Then ask: "Can you describe what\'s happening? Feel free to attach any screenshots if that helps." After they respond, call update_description(description="<their response>").' . $optionalNote;
        }

        // Handle title field
        if ($fieldId === 'title') {
            return 'Based on what they\'ve told you, suggest a short title. Say something like: "I\'ll title this \'[your suggested title]\' - does that work?" After they confirm (or suggest changes), call update_title(title="<final title>").' . $optionalNote;
        }

        // Handle custom form fields
        $isComplexField = ! in_array($fieldType, [TextInputFormFieldBlock::type(), TextAreaFormFieldBlock::type()]);

        if ($isComplexField) {
            return sprintf(
                'Call show_field_input(field_id="%s") to display the input, AND in the same response ask a natural question like "Which %s does this relate to?" or "Please select your %s."',
                $fieldId,
                strtolower($fieldLabel),
                strtolower($fieldLabel)
            );
        }

        return sprintf(
            'Ask naturally for their %s (e.g., "What\'s your %s?"). After they respond, call update_form_field(field_id="%s", value="<their response>").',
            strtolower($fieldLabel),
            strtolower($fieldLabel),
            $fieldId
        );
    }

    /**
     * Check if the form has custom fields (not counting description/title).
     *
     * @param array<string, mixed> $result
     */
    protected function hasCollectedFormFields(array $result): bool
    {
        return $result['has_custom_form_fields'] ?? false;
    }
}
