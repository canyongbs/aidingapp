<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Aiding App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Aiding App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    <https://www.canyongbs.com> or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AidingApp\Ai\Actions;

use AidingApp\Ai\Settings\AiClarificationSettings;
use AidingApp\Contact\Models\Contact;
use AidingApp\ServiceManagement\Models\ServiceRequestFormField;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use Illuminate\Support\Collection;

class GenerateServiceRequestQuestionAiPrompt
{
    /**
     * @param array<string, mixed> $formData
     * @param array<int, array{question: string, answer: string}> $previousQuestionsAndAnswers
     */
    public function execute(
        ServiceRequestType $serviceRequestType,
        array $formData,
        array $previousQuestionsAndAnswers,
        int $questionNumber,
        Contact $contact
    ): string {
        $requesterName = $contact->full_name ?? $contact->name ?? 'the user';

        $fieldsMap = $this->buildFieldsMapFromType($serviceRequestType);

        $missingDetailKeys = $this->findMissingDetailKeys($formData, $fieldsMap);

        if (! empty($missingDetailKeys)) {
            $databaseLabels = $this->fetchMissingFieldLabels($missingDetailKeys);

            foreach ($databaseLabels as $id => $label) {
                $fieldsMap[$id] = $label;
            }
        }

        $formDataMarkdown = $this->renderFormDataMarkdown($formData, $fieldsMap);

        $prompt = "You are a support agent handling a ticket submitted by {$requesterName} for the service request type {$serviceRequestType->name}.";

        if (! empty($serviceRequestType->description)) {
            $prompt .= " The service request type description is: {$serviceRequestType->description}.";
        }

        $totalQuestions = AiClarificationSettings::NUMBER_OF_QUESTIONS;

        $prompt .= "\n\nReview the submitted form data below and generate exactly one clarifying question (question {$questionNumber} of {$totalQuestions}). The answer should give the human agent handling this request the information they need to resolve it faster. Only ask about information that is directly relevant to resolving this particular request. Do not ask compound questions that cover multiple topics — ask one simple, focused question.\n\n";

        $prompt .= $formDataMarkdown;

        if (! empty($previousQuestionsAndAnswers)) {
            $prompt .= "## Previous Clarifying Questions and Answers\n\n";

            foreach ($previousQuestionsAndAnswers as $index => $entry) {
                $num = $index + 1;
                $prompt .= "**Question {$num}:** {$entry['question']}\n**Answer {$num}:** {$entry['answer']}\n\n";
            }

            $prompt .= "Based on the above questions and answers, ask a NEW follow-up question that gathers additional information not already covered. Do not repeat or rephrase previous questions.\n\n";
        }

        $prompt .= "Example: if the request is to replace a light bulb, ask which building and room the issue is located in.\n";
        $prompt .= "\nRespond with only the question text, nothing else. Do not number it or prefix it.\n";

        return $prompt;
    }

    /**
     * @return Collection<int, string>
     */
    protected function buildFieldsMapFromType(ServiceRequestType $serviceRequestType): Collection
    {
        $map = collect();

        if (! $serviceRequestType->form) {
            return $map;
        }

        foreach ($serviceRequestType->form->steps ?? [] as $step) {
            foreach ($step->fields ?? [] as $field) {
                $map[$field->getKey()] = $field->label;
            }
        }

        return $map;
    }

    /**
     * @param array<string, mixed> $formData
     * @param Collection<int, string> $fieldsMap
     *
     * @return array<int, string>
     */
    protected function findMissingDetailKeys(array $formData, Collection $fieldsMap): array
    {
        /** @var array<string, mixed> */
        $details = $formData['Details'] ?? [];

        $detailKeys = collect($details)->keys()->filter()->values();

        return $detailKeys->filter(fn ($key) => ! $fieldsMap->has($key))->all();
    }

    /**
     * @param array<int, string> $ids
     *
     * @return array<int, string>
     */
    protected function fetchMissingFieldLabels(array $ids): array
    {
        return ServiceRequestFormField::query()
            ->whereIn('id', $ids)
            ->pluck('label', 'id')
            ->all();
    }

    /**
     * @param array<string, mixed> $formData
     * @param Collection<int, string> $fieldsMap
     */
    protected function renderFormDataMarkdown(array $formData, Collection $fieldsMap): string
    {
        $markdown = "## Submitted form data\n\n";

        foreach ($formData as $section => $values) {
            $markdown .= "### {$section}\n\n";

            if (is_array($values)) {
                foreach ($values as $key => $value) {
                    $label = $fieldsMap[$key] ?? $key;

                    if (is_array($value) || is_object($value)) {
                        $valueString = json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                    } elseif ($value === null || $value === '') {
                        $valueString = '—';
                    } else {
                        $valueString = (string) $value;
                    }

                    $markdown .= "- **{$label}**: {$valueString}\n";
                }
            } else {
                $markdown .= '- ' . json_encode($values) . "\n";
            }

            $markdown .= "\n";
        }

        return $markdown;
    }
}
