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
use AidingApp\ServiceManagement\Models\ServiceRequestFormSubmission;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class GetDraftStatus
{
    /**
     * @return array<string, mixed>
     */
    public function execute(ServiceRequest $draft): array
    {
        $draft->load(['priority', 'priority.type', 'serviceRequestFormSubmission']);

        $stage = ServiceRequestDraftStage::fromServiceRequest($draft);
        $type = $draft->priority?->type;

        $result = $this->buildResult($draft, $stage, $type);

        return $this->cleanupResult([
            'next_instruction' => $this->getStageInstruction($stage, $draft, $result),
            ...$result,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    protected function buildResult(ServiceRequest $draft, ?ServiceRequestDraftStage $stage, ?ServiceRequestType $type): array
    {
        $result = [
            'draft_stage' => $stage?->value,
            'type_name' => $type?->name,
            'priority_name' => $draft->priority?->name,
        ];

        if ($stage === ServiceRequestDraftStage::DataCollection) {
            return array_merge($result, $this->buildDataCollectionResult($draft, $type));
        }

        return array_merge($result, $this->buildLaterStageResult($draft, $type));
    }

    /**
     * @return array<string, mixed>
     */
    protected function buildDataCollectionResult(ServiceRequest $draft, ?ServiceRequestType $type): array
    {
        $data = [
            'title' => $draft->title,
            'description' => $draft->close_details,
        ];

        if (! $type) {
            return array_merge($data, [
                'next_field' => null,
                'has_custom_form_fields' => false,
            ]);
        }

        $formFields = $this->getFormFields($draft, $type);
        $nextField = $this->getNextFieldToCollect($draft, $formFields);

        $data['form_fields'] = $formFields;
        $data['next_field'] = $nextField;
        $data['has_custom_form_fields'] = ! empty($formFields);

        $this->addFilledFieldsIfOnlyTitleDescriptionRemaining($data, $draft, $type, $nextField);

        return $data;
    }

    /**
     * @param array<string, mixed> $data
     * @param array<string, mixed>|null $nextField
     */
    protected function addFilledFieldsIfOnlyTitleDescriptionRemaining(array &$data, ServiceRequest $draft, ServiceRequestType $type, ?array $nextField): void
    {
        if ($nextField === null) {
            return;
        }

        $fieldId = $nextField['field_id'] ?? null;

        if (! in_array($fieldId, ['title', 'description'])) {
            return;
        }

        $filledFields = $this->getFilledFormFields($draft, $type);

        if (empty($filledFields)) {
            return;
        }

        $data['filled_form_fields'] = $filledFields;
    }

    /**
     * @return array<string, mixed>
     */
    protected function buildLaterStageResult(ServiceRequest $draft, ?ServiceRequestType $type): array
    {
        return [
            'title' => $draft->title,
            'description' => $draft->close_details,
            'filled_form_fields' => $this->getFilledFormFields($draft, $type),
            'questions_completed' => $this->countClarifyingQuestions($draft),
        ];
    }

    protected function countClarifyingQuestions(ServiceRequest $draft): int
    {
        return $draft->serviceRequestUpdates()
            ->where('update_type', ServiceRequestUpdateType::ClarifyingQuestion)
            ->count();
    }

    /**
     * @param array<string, mixed> $result
     *
     * @return array<string, mixed>
     */
    protected function cleanupResult(array $result): array
    {
        unset($result['form_fields']);

        return $result;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function getFormFields(ServiceRequest $draft, ServiceRequestType $type): array
    {
        $form = $type->form;

        if (! $form) {
            return [];
        }

        $filledValues = $this->getFilledFieldValues($draft->serviceRequestFormSubmission);

        return $this->collectFieldsFromForm($form, $filledValues);
    }

    /**
     * @return array<string, mixed>
     */
    protected function getFilledFieldValues(?ServiceRequestFormSubmission $submission): array
    {
        if (! $submission) {
            return [];
        }

        $submittedFieldIds = $submission->fields()->pluck('service_request_form_field_id')->all();

        $values = [];

        foreach ($submittedFieldIds as $fieldId) {
            $field = $submission->fields()->where('service_request_form_field_id', $fieldId)->first();
            $values[$fieldId] = $field?->getRelationValue('pivot')->response ?? '';
        }

        return $values;
    }

    /**
     * @param array<string, mixed> $filledValues
     *
     * @return array<int, array<string, mixed>>
     */
    protected function collectFieldsFromForm(mixed $form, array $filledValues): array
    {
        $fields = [];

        foreach ($this->getOrderedSteps($form) as $step) {
            foreach ($step->fields as $field) {
                assert($field instanceof ServiceRequestFormField);

                $fields[] = $this->formatField($field, $filledValues, $step);
            }
        }

        foreach ($this->getOrphanedFields($form) as $field) {
            $fields[] = $this->formatField($field, $filledValues, null);
        }

        return $fields;
    }

    /**
     * @return EloquentCollection<int, ServiceRequestFormStep>
     */
    protected function getOrderedSteps(mixed $form): EloquentCollection
    {
        return $form->steps()->orderBy('sort')->with('fields')->get();
    }

    /**
     * @return EloquentCollection<int, ServiceRequestFormField>
     */
    protected function getOrphanedFields(mixed $form): EloquentCollection
    {
        return $form->fields()->whereNull('service_request_form_step_id')->get();
    }

    /**
     * @param array<string, mixed> $filledValues
     *
     * @return array<string, mixed>
     */
    protected function formatField(ServiceRequestFormField $field, array $filledValues, ?ServiceRequestFormStep $step): array
    {
        $fieldId = $field->getKey();
        $hasSubmission = array_key_exists($fieldId, $filledValues);
        $value = $filledValues[$fieldId] ?? null;

        $data = [
            'field_id' => $fieldId,
            'label' => $field->label,
            'type' => $field->type,
            'required' => (bool) $field->is_required,
            'value' => $value,
            'has_submission' => $hasSubmission,
            'filled' => $hasSubmission && $value !== '',
            'collection_method' => $this->getCollectionMethod($field->type),
            'step_id' => $step?->getKey(),
            'step_label' => $step?->label,
        ];

        if (isset($field->config['options'])) {
            $data['options'] = $field->config['options'];
        }

        return $data;
    }

    protected function getCollectionMethod(string $fieldType): string
    {
        $textFieldTypes = [
            TextInputFormFieldBlock::type(),
            TextAreaFormFieldBlock::type(),
        ];

        return in_array($fieldType, $textFieldTypes) ? 'text' : 'show_field_input';
    }

    /**
     * @param array<int, array<string, mixed>> $formFields
     *
     * @return array<string, mixed>|null
     */
    protected function getNextFieldToCollect(ServiceRequest $draft, array $formFields): ?array
    {
        foreach ($formFields as $field) {
            if (! $field['has_submission']) {
                return $this->buildNextFieldData($field);
            }
        }

        if (empty($draft->close_details)) {
            return [
                'field_id' => 'description',
                'label' => 'Description',
                'type' => 'description',
                'required' => true,
                'is_optional' => false,
            ];
        }

        if (empty($draft->title)) {
            return [
                'field_id' => 'title',
                'label' => 'Title',
                'type' => 'title',
                'required' => true,
                'is_optional' => false,
            ];
        }

        return null;
    }

    /**
     * @param array<string, mixed> $field
     *
     * @return array<string, mixed>
     */
    protected function buildNextFieldData(array $field): array
    {
        $data = [
            'field_id' => $field['field_id'],
            'label' => $field['label'],
            'type' => $field['type'],
            'required' => $field['required'],
            'is_optional' => ! $field['required'],
            'collection_method' => $field['collection_method'],
        ];

        if (isset($field['options'])) {
            $data['options'] = $field['options'];
        }

        return $data;
    }

    /**
     * @param array<string, mixed> $result
     */
    protected function getStageInstruction(?ServiceRequestDraftStage $stage, ServiceRequest $draft, array $result): string
    {
        if (! $stage) {
            return 'Draft stage could not be determined.';
        }

        return match ($stage) {
            ServiceRequestDraftStage::DataCollection => $this->getDataCollectionInstruction($result),
            ServiceRequestDraftStage::ClarifyingQuestions => $this->getClarifyingQuestionsInstruction($result),
            ServiceRequestDraftStage::Resolution => $this->getResolutionInstruction($draft),
        };
    }

    protected function getResolutionInstruction(ServiceRequest $draft): string
    {
        if (! $this->isAiResolutionEnabled()) {
            return 'Service request has been submitted for review. Thank the user and let them know a team member will follow up to help resolve this.';
        }

        if ($this->hasResolutionBeenProposed($draft)) {
            return 'You already presented a resolution. Wait for yes/no feedback, then call record_resolution_response IMMEDIATELY. If they say no, the request auto-submits for human review with ALL details already collected - do NOT say they need to provide more details.';
        }

        return 'The request has not been submitted to a human yet. To submit to a human, you are first required to attempt to resolve the user\'s issue or tell the system that you do not have the ability to do so and that a human must be involved. Formulate a helpful resolution based on everything collected, if you can. You MUST now call check_ai_resolution_validity(confidence_score=<0-100>, proposed_answer="<your resolution>"). If you are not able to solve the issue and would like to submit the ticket for a human, use confidence_score=0. Do NOT mention escalation or what happens if it doesn\'t work.';
    }

    protected function isAiResolutionEnabled(): bool
    {
        return app(AiResolutionSettings::class)->is_enabled;
    }

    protected function hasResolutionBeenProposed(ServiceRequest $draft): bool
    {
        return $draft->serviceRequestUpdates()
            ->where('update_type', ServiceRequestUpdateType::AiResolutionProposed)
            ->exists();
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

        if ($completed === 0) {
            return "Tell the user you'll ask a few quick questions. Ask your first question naturally. Good topics: when it started, what they've tried, error messages, urgency. AFTER they answer: call save_clarifying_question_answer IMMEDIATELY before saying anything else. (0/3 saved)";
        }

        return "SAVE FIRST: Call save_clarifying_question_answer(question=\"<your last question>\", answer=\"<user's response>\") NOW. Then ask your next clarifying question. Good topics: when it started, what they've tried, error messages, urgency. ({$completed}/3 saved)";
    }

    /**
     * @param array<string, mixed> $result
     */
    protected function getDataCollectionInstruction(array $result): string
    {
        $nextField = $result['next_field'] ?? null;

        if (! $nextField) {
            return 'All information collected. Transition to asking clarifying questions.';
        }

        return $this->getFieldInstruction($nextField, $result);
    }

    /**
     * @param array<string, mixed> $field
     * @param array<string, mixed> $result
     */
    protected function getFieldInstruction(array $field, array $result): string
    {
        $fieldId = $field['field_id'];
        $fieldLabel = $field['label'];
        $isOptional = $field['is_optional'] ?? false;

        $skipNote = $isOptional
            ? " This field is optional - if the user says skip, N/A, or doesn't have this info, call skip_form_field(field_id=\"{$fieldId}\")."
            : '';

        if ($fieldId === 'description') {
            $hasFormFields = $result['has_custom_form_fields'] ?? false;

            if ($hasFormFields) {
                return 'Ask: "Is there anything else you\'d like to add about this request? Feel free to attach any files if helpful." IMMEDIATELY after they respond with ANY text, you MUST call update_description(description="<their response>") before doing anything else. Do not push them to improve their description even if it is vague, just move on.';
            }

            return 'Ask: "Can you describe what\'s happening? Feel free to attach any screenshots if that helps." IMMEDIATELY after they respond with ANY description, you MUST call update_description(description="<their response>") before doing anything else. Do not push them to improve their description even if it is vague, just move on.';
        }

        if ($fieldId === 'title') {
            return 'Based on what they\'ve told you, suggest a short title. Say something like: "I\'ll title this \'[your suggested title]\' - does that work?" After they confirm (or suggest changes), call update_title(title="<final title>").';
        }

        $collectionMethod = $field['collection_method'] ?? 'text';

        if ($collectionMethod === 'show_field_input') {
            return sprintf(
                'Call show_field_input(field_id="%s") to display the "%s" input. In the same response, ask ONE natural question to prompt for the "%s" field (e.g., "What\'s your *?" or "Could you select the *?"). Do NOT mention the widget or list the options - the user sees them. Wait for their widget submission.%s',
                $fieldId,
                $fieldLabel,
                $fieldLabel,
                $skipNote
            );
        }

        return sprintf(
            'Ask the user for "%s" (e.g., "What\'s your *?"). Wait for their NEW response, then call update_form_field(field_id="%s", value="<their NEW response>").%s',
            strtolower($fieldLabel),
            $fieldId,
            $skipNote
        );
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function getFilledFormFields(ServiceRequest $draft, ?ServiceRequestType $type): array
    {
        if (! $type?->form) {
            return [];
        }

        $submission = $draft->serviceRequestFormSubmission;

        if (! $submission) {
            return [];
        }

        $fieldTypes = $this->buildFieldTypeMap($type->form);

        return $this->formatFilledFields($submission, $fieldTypes);
    }

    /**
     * @return Collection<string, string>
     */
    protected function buildFieldTypeMap(mixed $form): Collection
    {
        return $form->fields->keyBy('id')->map(
            fn (ServiceRequestFormField $field) => $field->type
        );
    }

    /**
     * @param Collection<string, string> $fieldTypes
     *
     * @return array<int, array<string, mixed>>
     */
    protected function formatFilledFields(ServiceRequestFormSubmission $submission, Collection $fieldTypes): array
    {
        return $submission->fields()
            ->get()
            ->keyBy('id')
            ->map(fn (ServiceRequestFormField $field) => $this->formatFilledField($field, $fieldTypes)) // @phpstan-ignore argument.type
            ->filter(fn (array $field) => $field['value'] !== null && $field['value'] !== '') // @phpstan-ignore notIdentical.alwaysTrue
            ->values()
            ->all();
    }

    /**
     * @param Collection<string, string> $fieldTypes
     *
     * @return array<string, mixed>
     */
    protected function formatFilledField(ServiceRequestFormField $field, Collection $fieldTypes): array
    {
        $rawValue = $field->getRelationValue('pivot')->response;
        $fieldType = $field->type ?? $fieldTypes[$field->getKey()] ?? null;

        return [
            'label' => $field->label,
            'value' => $this->formatDisplayValue($fieldType, $rawValue),
        ];
    }

    protected function formatDisplayValue(?string $fieldType, mixed $rawValue): string
    {
        if ($rawValue === null || $rawValue === '') {
            return '';
        }

        if ($fieldType === SignatureFormFieldBlock::type()) {
            return '[Signature provided]';
        }

        if ($fieldType === CheckboxFormFieldBlock::type()) {
            return $rawValue ? 'Yes' : 'No';
        }

        return Str::limit((string) $rawValue, 255);
    }
}
