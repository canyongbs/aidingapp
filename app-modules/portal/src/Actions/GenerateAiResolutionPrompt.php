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

namespace AidingApp\Portal\Actions;

use AidingApp\Contact\Models\Contact;
use AidingApp\ServiceManagement\Models\ServiceRequestFormField;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use Illuminate\Support\Collection;

class GenerateAiResolutionPrompt
{
    /**
     * @param array<string, mixed> $formData
     * @param array<string, string> $questionsAndAnswers
     */
    public function execute(
        ServiceRequestType $serviceRequestType,
        array $formData,
        array $questionsAndAnswers,
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

        $prompt = "Adopt the persona of a Level 1 helpdesk technician.\n\n";

        $prompt .= "Review all available context, including:\n";
        $prompt .= "- The knowledge and background information provided\n";
        $prompt .= "- The customer's original question or request\n";
        $prompt .= "- Any clarification questions already answered by the customer\n\n";

        $prompt .= "## Background\n\n";
        $prompt .= "The customer ({$requesterName}) submitted a service request for the service request type: {$serviceRequestType->name}.";

        if (! empty($serviceRequestType->description)) {
            $prompt .= " The service request type description is: {$serviceRequestType->description}.";
        }

        $prompt .= "\n\n## Original Request Details\n\n";
        $prompt .= $this->renderFormDataMarkdown($formData, $fieldsMap);

        $prompt .= "\n## Clarifying Questions and Answers\n\n";

        foreach ($questionsAndAnswers as $question => $answer) {
            $prompt .= "**Q:** {$question}\n**A:** {$answer}\n\n";
        }

        $prompt .= "\n## Your Task\n\n";
        $prompt .= "Determine whether the customer's request can be fully resolved without collecting additional information and without requiring a human to take any follow-up action.\n\n";

        $prompt .= "A \"yes\" disposition means:\n";
        $prompt .= "- You have enough information to fully answer the customer's request\n";
        $prompt .= "- The response can directly resolve the issue or provide complete service steps\n";
        $prompt .= "- No additional customer input is needed\n";
        $prompt .= "- No human action is required to complete a dependent task\n\n";

        $prompt .= "A \"no\" disposition means:\n";
        $prompt .= "- More information is needed from the customer\n";
        $prompt .= "- The request requires a human to perform an action\n";
        $prompt .= "- The request depends on an external system, approval, account change, investigation, escalation, or other task that cannot be completed through the response alone\n";
        $prompt .= "- You are not confident that the response fully resolves the customer's request\n\n";

        $prompt .= "## Response Format\n\n";
        $prompt .= "Return only a structured JSON response using the following format.\n\n";
        $prompt .= "For a \"yes\" disposition:\n";
        $prompt .= "- Set `disposition` to `\"yes\"`\n";
        $prompt .= "- Set `confidence` to a number from 1 to 100\n";
        $prompt .= "- Set `detailed_response` to the complete customer-facing answer, formatted in Markdown when helpful\n\n";

        $prompt .= "For a \"no\" disposition:\n";
        $prompt .= "- Set `disposition` to `\"no\"`\n";
        $prompt .= "- Set `confidence` to an empty string\n";
        $prompt .= "- Set `detailed_response` to an empty string\n\n";

        $prompt .= "Do not include any explanation outside the JSON response.\n";

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
        $markdown = '';

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
