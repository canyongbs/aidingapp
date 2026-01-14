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
use AidingApp\Form\Filament\Blocks\CheckboxFormFieldBlock;
use AidingApp\Form\Filament\Blocks\SignatureFormFieldBlock;
use AidingApp\Form\Filament\Blocks\TextAreaFormFieldBlock;
use AidingApp\Form\Filament\Blocks\TextInputFormFieldBlock;
use AidingApp\ServiceManagement\Enums\ServiceRequestDraftStage;
use AidingApp\ServiceManagement\Enums\ServiceRequestUpdateType;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestFormField;
use AidingApp\ServiceManagement\Models\ServiceRequestFormStep;
use Illuminate\Support\Str;

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

            // Include filled form fields so AI has full context
            $result['filled_form_fields'] = $this->getFilledFormFields($draft, $type);

            $questionsCompleted = $draft->serviceRequestUpdates()
                ->where('update_type', ServiceRequestUpdateType::ClarifyingQuestion)
                ->count();

            $result['questions_completed'] = $questionsCompleted;
        }

        $result['next_instruction'] = $this->getStageInstruction($draftStage, $draft, $result);

        // Remove internal-only fields before returning to AI
        unset($result['form_fields']);

        // Strip position from field arrays (used internally for optional field logic)
        if (isset($result['missing_required_fields'])) {
            $result['missing_required_fields'] = array_map(
                fn ($field) => collect($field)->except('position')->all(),
                $result['missing_required_fields']
            );
        }

        if (isset($result['missing_optional_fields'])) {
            $result['missing_optional_fields'] = array_map(
                fn ($field) => collect($field)->except('position')->all(),
                $result['missing_optional_fields']
            );
        }

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
            $data['form_fields'] = $formFields;
            $data['missing_required_fields'] = $this->getMissingRequiredFields($draft, $formFields);
            $data['missing_optional_fields'] = $this->getMissingOptionalFields($formFields);
            // Track if the form has any custom fields at all (for adjusting description prompt)
            $data['has_custom_form_fields'] = ! empty($formFields);

            // Only include filled fields once all custom form fields are done (for title generation context)
            $onlyTitleDescriptionRemaining = collect($data['missing_required_fields'])
                ->every(fn ($field) => in_array($field['type'], ['title', 'description']));

            if ($onlyTitleDescriptionRemaining) {
                $filledFields = $this->getFilledFormFields($draft, $type);

                if (! empty($filledFields)) {
                    $data['filled_form_fields'] = $filledFields;
                }
            }
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
        $position = 0;

        // Get steps ordered by sort, with their fields
        $steps = $form->steps()->orderBy('sort')->with('fields')->get();

        foreach ($steps as $step) {
            foreach ($step->fields as $field) {
                $fields[] = $this->formatField($field, $filledFields, $position, $step);
                $position++;
            }
        }

        // Also include any fields without a step (orphaned fields)
        foreach ($form->fields()->whereNull('service_request_form_step_id')->get() as $field) {
            $fields[] = $this->formatField($field, $filledFields, $position, null);
            $position++;
        }

        return $fields;
    }

    /**
     * @param array<string, mixed> $filledFields
     *
     * @return array<string, mixed>
     */
    protected function formatField(ServiceRequestFormField $field, array $filledFields, int $position = 0, ?ServiceRequestFormStep $step = null): array
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
            'position' => $position,
            'step_id' => $step?->getKey(),
            'step_label' => $step?->label,
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
                    'position' => $field['position'],
                ];

                if (isset($field['options'])) {
                    $fieldData['options'] = $field['options'];
                }

                $missing[] = $fieldData;
            }
        }

        // Get max position from form fields for description/title positioning
        $maxPosition = count($formFields);

        // Check description
        if (empty($draft->close_details)) {
            $missing[] = [
                'field_id' => 'description',
                'label' => 'Description',
                'type' => 'description',
                'position' => $maxPosition,
            ];
        }

        // Check title
        if (empty($draft->title)) {
            $missing[] = [
                'field_id' => 'title',
                'label' => 'Title',
                'type' => 'title',
                'position' => $maxPosition + 1,
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
                    'position' => $field['position'],
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
     * Get optional fields that were skipped over (between last filled field and next required field).
     *
     * @param array<int, array<string, mixed>> $formFields      All form fields with position
     * @param array<int, array<string, mixed>> $missingOptional Missing optional fields with position
     * @param int                              $nextRequiredPosition Position of the next required field
     *
     * @return array<int, array<string, mixed>>
     */
    protected function getSkippedOptionalFields(array $formFields, array $missingOptional, int $nextRequiredPosition): array
    {
        // Find the position of the last filled field
        $lastFilledPosition = -1;

        foreach ($formFields as $field) {
            if ($field['filled'] && $field['position'] > $lastFilledPosition) {
                $lastFilledPosition = $field['position'];
            }
        }

        // Find optional fields between last filled and next required
        $skipped = [];

        foreach ($missingOptional as $field) {
            if ($field['position'] > $lastFilledPosition && $field['position'] < $nextRequiredPosition) {
                $skipped[] = $field;
            }
        }

        return $skipped;
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
            ServiceRequestDraftStage::Resolution => $this->getResolutionInstruction($draft),
        };
    }

    protected function getResolutionInstruction(ServiceRequest $draft): string
    {
        $aiResolutionSettings = app(AiResolutionSettings::class);

        if (! $aiResolutionSettings->is_enabled) {
            return 'Service request has been submitted for review. Thank the user and let them know a team member will follow up to help resolve this.';
        }

        // Check if resolution has already been proposed
        $hasResolutionBeenProposed = $draft->serviceRequestUpdates()
            ->where('update_type', ServiceRequestUpdateType::AiResolutionProposed)
            ->exists();

        if ($hasResolutionBeenProposed) {
            return 'You already presented a resolution. Wait for yes/no feedback, then call record_resolution_response IMMEDIATELY. If they say no, the request auto-submits for human review with ALL details already collected - do NOT say they need to provide more details.';
        }

        return 'Formulate a helpful resolution based on everything collected. Call check_ai_resolution_validity(confidence_score=<0-100>, proposed_answer="<your resolution>"). Do NOT mention escalation or what happens if it doesn\'t work.';
    }

    /**
     * @param array<string, mixed> $result
     */
    protected function getClarifyingQuestionsInstruction(array $result): string
    {
        $completed = $result['questions_completed'] ?? 0;
        $remaining = 3 - $completed;

        if ($remaining === 0) {
            return 'All 3 clarifying questions complete. Request will be submitted for review.';
        }

        // Front-load the SAVE instruction since that's what the AI tends to forget
        if ($completed === 0) {
            return sprintf(
                'Tell the user you\'ll ask a few quick questions. Ask your first question naturally. Good topics: when it started, what they\'ve tried, error messages, urgency. AFTER they answer: call save_clarifying_question_answer IMMEDIATELY before saying anything else. (%d/3 saved)',
                $completed
            );
        }

        return sprintf(
            'SAVE FIRST: Call save_clarifying_question_answer(question="<your last question>", answer="<user\'s response>") NOW. Then ask your next clarifying question. Good topics: when it started, what they\'ve tried, error messages, urgency. (%d/3 saved)',
            $completed
        );
    }

    /**
     * @param array<string, mixed> $result
     */
    protected function getDataCollectionInstruction(array $result): string
    {
        $missingRequired = $result['missing_required_fields'] ?? [];
        $missingOptional = $result['missing_optional_fields'] ?? [];
        $formFields = $result['form_fields'] ?? [];

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
        $nextRequiredPosition = $firstMissing['position'] ?? PHP_INT_MAX;

        // Build skipped optional fields note (only optional fields between last filled and next required)
        $skippedOptionalNote = '';
        $skippedOptional = $this->getSkippedOptionalFields($formFields, $missingOptional, $nextRequiredPosition);

        if (! empty($skippedOptional)) {
            $skippedLabels = array_map(fn ($f) => $f['label'], $skippedOptional);
            $skippedOptionalNote = sprintf(
                ' You skipped these optional fields: %s - ask about them if they seem relevant based on the conversation.',
                implode(', ', $skippedLabels)
            );
        }

        // Handle description field - show ALL remaining optional fields since we're done with custom form fields
        if ($fieldId === 'description') {
            $remainingOptionalNote = '';

            if (! empty($missingOptional)) {
                $optionalLabels = array_map(fn ($f) => $f['label'], $missingOptional);
                $remainingOptionalNote = sprintf(
                    ' Before moving on, these optional fields are still available: %s - ask about them if they seem relevant based on the conversation.',
                    implode(', ', $optionalLabels)
                );
            }

            // Check if there were any form fields (required or optional) that we've already collected
            $hasFormFields = $this->hasCollectedFormFields($result);

            if ($hasFormFields) {
                return 'Call enable_file_attachments() first. Then ask: "Is there anything else you\'d like to add about this request? Feel free to attach any files if helpful." IMMEDIATELY after they respond with ANY text, you MUST call update_description(description="<their response>") before doing anything else.' . $remainingOptionalNote;
            }

            return 'Call enable_file_attachments() first. Then ask: "Can you describe what\'s happening? Feel free to attach any screenshots if that helps." IMMEDIATELY after they respond with ANY description, you MUST call update_description(description="<their response>") before doing anything else.' . $remainingOptionalNote;
        }

        // Handle title field
        if ($fieldId === 'title') {
            return 'Based on what they\'ve told you, suggest a short title. Say something like: "I\'ll title this \'[your suggested title]\' - does that work?" After they confirm (or suggest changes), call update_title(title="<final title>").';
        }

        // Handle custom form fields
        $isComplexField = ! in_array($fieldType, [TextInputFormFieldBlock::type(), TextAreaFormFieldBlock::type()]);

        if ($isComplexField) {
            return sprintf(
                'Call show_field_input(field_id="%s") to display the "%s" input. In the same response, ask ONE natural question (e.g., "What\'s your %s?" or "Could you select the %s?"). Do NOT mention the widget or list the options - the user sees them.%s',
                $fieldId,
                $fieldLabel,
                $fieldLabel,
                $fieldLabel,
                $skippedOptionalNote
            );
        }

        return sprintf(
            'Ask naturally for their %s (e.g., "What\'s your %s?"). After they respond, call update_form_field(field_id="%s", value="<their response>").%s',
            strtolower($fieldLabel),
            strtolower($fieldLabel),
            $fieldId,
            $skippedOptionalNote
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

    /**
     * Get all filled form fields with their labels and values for AI context.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function getFilledFormFields(ServiceRequest $draft, mixed $type): array
    {
        if (! $type) {
            return [];
        }

        $form = $type->form;

        if (! $form) {
            return [];
        }

        $submission = $draft->serviceRequestFormSubmission;

        if (! $submission) {
            return [];
        }

        // Build a map of field IDs to their types
        $fieldTypes = $form->fields->keyBy('id')->map(fn ($field) => $field->type);

        $filledFields = $submission->fields()
            ->get()
            ->keyBy('id')
            ->map(function ($field) use ($fieldTypes) {
                $rawValue = $field->pivot->response;
                $displayValue = $this->getDisplayValueForField($field->type ?? $fieldTypes[$field->id] ?? null, $rawValue, $field->label);

                return [
                    'label' => $field->label,
                    'value' => $displayValue,
                ];
            })
            ->filter(fn ($field) => $field['value'] !== null && $field['value'] !== '')
            ->values()
            ->all();

        return $filledFields;
    }

    /**
     * Get a human-readable display value for different field types.
     */
    protected function getDisplayValueForField(?string $fieldType, mixed $rawValue, string $label): string
    {
        if ($rawValue === null || $rawValue === '') {
            return '';
        }

        // Handle signature fields - they store base64 data URLs which are too long for AI context
        if ($fieldType === SignatureFormFieldBlock::type()) {
            return '[Signature provided]';
        }

        // Handle checkbox fields - convert boolean to Yes/No
        if ($fieldType === CheckboxFormFieldBlock::type()) {
            return $rawValue ? 'Yes' : 'No';
        }

        // For all other fields, limit length and return the value
        $stringValue = (string) $rawValue;

        return Str::limit($stringValue, 255);
    }
}
