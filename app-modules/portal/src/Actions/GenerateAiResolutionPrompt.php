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

        $prompt = "You are an expert support technician reviewing a service request from {$requesterName} for the service request type: {$serviceRequestType->name}.";

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
        $prompt .= "Based on the knowledge base articles available to you and the information provided above, determine if you can provide a complete, actionable solution to this request.\n\n";

        $prompt .= "## CRITICAL: Response Format Constraints\n\n";
        $prompt .= "The user will ONLY see your `proposed_answer` with two buttons: \"Yes (Resolved)\" and \"No\". They CANNOT provide any additional information, ask follow-up questions, or clarify anything. This is a one-shot response with no further interaction possible.\n\n";
        $prompt .= "Your `proposed_answer` MUST be:\n";
        $prompt .= "- A complete, self-contained solution that the user can act on immediately\n";
        $prompt .= "- Written as a direct answer, NOT as a question or request for clarification\n";
        $prompt .= "- Free of any phrases like \"Could you provide...\", \"Can you clarify...\", \"What is your...\", \"Please specify...\", or similar requests for information\n\n";
        $prompt .= "If you do not have enough information to provide a definitive solution:\n";
        $prompt .= "- Set a LOW confidence_score (below the threshold) so the ticket goes to a human agent\n";
        $prompt .= "- In `proposed_answer`, state what you would need to know and provide any partial guidance you can\n";
        $prompt .= "- In `reasoning`, explain what information is missing\n\n";

        $prompt .= "## Response Format\n\n";
        $prompt .= "You MUST respond in the following JSON format:\n";
        $prompt .= "```json\n";
        $prompt .= "{\n";
        $prompt .= "  \"confidence_score\": <integer 1-100>,\n";
        $prompt .= "  \"proposed_answer\": \"<your detailed solution here>\",\n";
        $prompt .= "  \"reasoning\": \"<brief explanation of your confidence level>\"\n";
        $prompt .= "}\n";
        $prompt .= "```\n\n";

        $prompt .= "## Confidence Score Guidelines\n\n";
        $prompt .= "The confidence_score should reflect how certain you are that your proposed_answer will fully resolve the user's issue without any human intervention:\n";
        $prompt .= "- 90-100: Highly confident - clear solution with direct knowledge base support, no human action required\n";
        $prompt .= "- 70-89: Moderately confident - likely solution but may need verification\n";
        $prompt .= "- 50-69: Somewhat confident - partial solution or general guidance\n";
        $prompt .= "- Below 50: Not confident - insufficient information, requires human support, configuration changes, or work on behalf of the user\n\n";
        $prompt .= "IMPORTANT: Only provide a high confidence score if:\n";
        $prompt .= "1. You have enough information to provide a complete answer\n";
        $prompt .= "2. The solution is something the user can implement themselves\n";
        $prompt .= "3. No support representative needs to make system changes, update configurations, or perform actions on their behalf\n";

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
