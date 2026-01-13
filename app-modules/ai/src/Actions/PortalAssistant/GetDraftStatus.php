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

        $result = [
            'has_draft' => true,
            'draft_stage' => $draftStage?->value,
            'title' => $draft->title,
            'description' => $draft->close_details,
        ];

        $type = $draft->priority?->type;

        if ($type) {
            $result['type_id'] = $type->getKey();
            $result['type_name'] = $type->name;
            $result['priority_id'] = $draft->priority?->getKey();
            $result['priority_name'] = $draft->priority?->name;

            $result['form_fields'] = $this->getFormFields($draft, $type);
            $result['missing_required'] = $this->getMissingRequired($draft, $result['form_fields']);
            $result['optional_fields'] = $this->getOptionalFields($result['form_fields']);
        } else {
            $result['type_id'] = null;
            $result['type_name'] = null;
            $result['priority_id'] = null;
            $result['priority_name'] = null;
            $result['form_fields'] = [];
            $result['missing_required'] = ['type'];
            $result['optional_fields'] = [];
        }

        $clarifyingQuestionsCount = $draft->serviceRequestUpdates()
            ->where('update_type', ServiceRequestUpdateType::ClarifyingQuestion)
            ->count();

        $result['clarifying_questions'] = [
            'completed' => $clarifyingQuestionsCount,
            'required' => 3,
        ];

        $result['next_instruction'] = $this->getStageInstruction($draftStage, $result);

        return $result;
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

        $data = [
            'field_id' => $fieldId,
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

        if ($hasCustomFields) {
            foreach ($formFields as $field) {
                if ($field['required'] && ! $field['filled']) {
                    $missing[] = $field['field_id'];
                }
            }
        }

        if (empty($draft->close_details)) {
            $missing[] = 'description';
        }

        if (empty($draft->title)) {
            $missing[] = 'title';
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
                ];
            }
        }

        return $optional;
    }

    /**
     * @param array<string, mixed> $result
     */
    protected function getStageInstruction(?ServiceRequestDraftStage $draftStage, array $result): string
    {
        if (! $draftStage) {
            return 'Draft stage could not be determined.';
        }

        return match ($draftStage) {
            ServiceRequestDraftStage::DataCollection => $this->getDataCollectionInstruction($result),
            ServiceRequestDraftStage::ClarifyingQuestions => sprintf(
                'Ask clarifying question %d of 3. After user answers, call save_clarifying_question with both your question and their answer.',
                ($result['clarifying_questions']['completed'] ?? 0) + 1
            ),
            ServiceRequestDraftStage::Resolution => $this->getResolutionInstruction(),
        };
    }

    protected function getResolutionInstruction(): string
    {
        $aiResolutionSettings = app(AiResolutionSettings::class);

        if ($aiResolutionSettings->is_enabled) {
            return 'Call check_ai_resolution_validity with your confidence score and proposed answer.';
        }

        return 'AI resolution is disabled. Service request will be submitted for human review.';
    }

    /**
     * @param array<string, mixed> $result
     */
    protected function getDataCollectionInstruction(array $result): string
    {
        $missing = $result['missing_required'] ?? [];

        if (empty($missing)) {
            return 'All required information collected. Stage will transition to clarifying_questions.';
        }

        $hasCustomFields = ! empty($result['form_fields'] ?? []);
        $firstMissing = $missing[0];

        if ($hasCustomFields && $firstMissing !== 'title' && $firstMissing !== 'description') {
            foreach ($result['form_fields'] ?? [] as $field) {
                if ($field['field_id'] === $firstMissing) {
                    $isComplexField = ! in_array($field['type'], [TextInputFormFieldBlock::type(), TextAreaFormFieldBlock::type()]);

                    return sprintf(
                        'Next: Ask about "%s"%s.',
                        $field['label'],
                        $isComplexField ? ' (use show_field_input for widget)' : ''
                    );
                }
            }
        }

        if ($firstMissing === 'description') {
            return 'Next: Ask for description of their issue.';
        }

        if ($firstMissing === 'title') {
            return 'Next: Suggest a title based on collected information.';
        }

        return 'Continue collecting required information.';
    }
}
