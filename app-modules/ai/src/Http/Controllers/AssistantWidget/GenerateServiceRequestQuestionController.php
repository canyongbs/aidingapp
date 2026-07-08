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

namespace AidingApp\Ai\Http\Controllers\AssistantWidget;

use AidingApp\Ai\Actions\GenerateServiceRequestQuestionAiPrompt;
use AidingApp\Ai\Settings\AiClarificationSettings;
use AidingApp\Ai\Settings\AiIntegratedAssistantSettings;
use AidingApp\Contact\Models\Contact;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use App\Features\ServiceRequestTypeVisibilityRestrictionsFeature;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GenerateServiceRequestQuestionController extends Controller
{
    public function __invoke(Request $request, ServiceRequestType $type): JsonResponse
    {
        $contact = auth('contact')->user() ?? $request->user();

        abort_if(! ($contact instanceof Contact), Response::HTTP_UNAUTHORIZED);

        if (ServiceRequestTypeVisibilityRestrictionsFeature::active()) {
            abort_unless($type->isVisibleToContactType($contact->type_id), Response::HTTP_NOT_FOUND);
        }

        if (! app(AiClarificationSettings::class)->is_enabled
            || ! $type->is_ai_clarification_enabled) {
            return response()->json([
                'field' => null,
            ]);
        }

        $data = $request->validate([
            'title' => ['required', 'string'],
            'description' => ['required', 'string'],
            'priority_id' => ['nullable', 'string'],
            'custom_fields' => ['nullable', 'array'],
            'previous_questions_and_answers' => ['nullable', 'array'],
            'previous_questions_and_answers.*.encrypted_question' => ['required', 'string'],
            'previous_questions_and_answers.*.answer' => ['required', 'string'],
            'question_number' => ['required', 'integer', 'min:1', 'max:' . AiClarificationSettings::NUMBER_OF_QUESTIONS],
        ]);

        $formData = [
            'Main' => [
                'title' => $data['title'],
                'description' => $data['description'],
                'priority' => $data['priority_id'] ?? '',
            ],
            'Details' => $data['custom_fields'] ?? [],
        ];

        $previousQuestionsAndAnswers = [];

        foreach ($data['previous_questions_and_answers'] ?? [] as $entry) {
            $payload = json_decode(decrypt($entry['encrypted_question']), true);

            abort_if(
                ! is_array($payload)
                    || ($payload['contact_id'] ?? null) !== $contact->getKey()
                    || ($payload['type_id'] ?? null) !== $type->getKey(),
                Response::HTTP_FORBIDDEN
            );

            $previousQuestionsAndAnswers[] = [
                'question' => $payload['text'],
                'answer' => $entry['answer'],
            ];
        }

        $prompt = app(GenerateServiceRequestQuestionAiPrompt::class)->execute(
            $type,
            $formData,
            $previousQuestionsAndAnswers,
            $data['question_number'],
            $contact
        );

        $aiService = app(AiIntegratedAssistantSettings::class)->getDefaultModel()->getService();

        $response = $aiService->complete(
            prompt: 'Return only the question text. No numbering, no prefix, no explanation. Just the question.',
            content: $prompt,
        );

        $question = trim($response);

        return response()->json([
            'field' => [
                '$formkit' => 'textarea',
                'name' => encrypt(json_encode([
                    'text' => $question,
                    'contact_id' => $contact->getKey(),
                    'type_id' => $type->getKey(),
                ])),
                'label' => $question,
                'validation' => 'required',
            ],
        ]);
    }
}
