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

namespace AidingApp\Portal\Http\Controllers\KnowledgeManagementPortal;

use AidingApp\Ai\Settings\AiIntegratedAssistantSettings;
use AidingApp\Ai\Settings\AiResolutionSettings;
use AidingApp\KnowledgeBase\Models\KnowledgeBaseItem;
use AidingApp\KnowledgeBase\Models\Scopes\KnowledgeBasePortalAssistantItem;
use AidingApp\Portal\Actions\GenerateAiResolutionPrompt;
use AidingApp\Portal\DataTransferObjects\AiResolutionEvaluation;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EvaluateServiceRequestAiResolutionController extends Controller
{
    public function __invoke(Request $request, ServiceRequestType $type): JsonResponse
    {
        $contact = auth('contact')->user();

        abort_if(is_null($contact), Response::HTTP_UNAUTHORIZED);

        abort_unless($type->isVisibleToContactType($contact->type_id), Response::HTTP_NOT_FOUND);

        $settings = app(AiResolutionSettings::class);

        if (! $settings->is_enabled
            || ! $type->is_ai_resolution_enabled) {
            return response()->json([
                'is_ai_resolution_available' => false,
            ]);
        }

        $formData = $request->input('formData', []);

        $questionsAndAnswers = [];

        foreach ($formData['Questions'] ?? [] as $encryptedQuestion => $answer) {
            $payload = json_decode(decrypt($encryptedQuestion), true);

            abort_if(
                ! is_array($payload)
                    || ($payload['contact_id'] ?? null) !== $contact->getKey()
                    || ($payload['type_id'] ?? null) !== $type->getKey(),
                Response::HTTP_FORBIDDEN
            );

            $questionsAndAnswers[$payload['text']] = $answer;
        }

        $prompt = app(GenerateAiResolutionPrompt::class)->execute(
            $type,
            $formData,
            $questionsAndAnswers,
            $contact
        );

        $aiService = app(AiIntegratedAssistantSettings::class)->getDefaultModel()->getService();

        $response = $aiService->complete(
            prompt: 'You must respond with valid JSON only. No other text.',
            content: $prompt,
            files: KnowledgeBaseItem::query()->tap(app(KnowledgeBasePortalAssistantItem::class))->get(['id'])->all(),
        );

        try {
            $decodedResponse = json_decode($response, true);

            if (! is_array($decodedResponse)) {
                return response()->json([
                    'is_ai_resolution_available' => false,
                ]);
            }

            $evaluation = AiResolutionEvaluation::from($decodedResponse);
        } catch (Exception) {
            return response()->json([
                'is_ai_resolution_available' => false,
            ]);
        }

        if ($evaluation->disposition !== 'yes') {
            return response()->json([
                'is_ai_resolution_available' => false,
            ]);
        }

        $meetsThreshold = $evaluation->confidence >= $settings->confidence_threshold;

        if (! $meetsThreshold) {
            return response()->json([
                'is_ai_resolution_available' => false,
                'confidence_score' => $evaluation->confidence,
            ]);
        }

        return response()->json([
            'is_ai_resolution_available' => true,
            'confidence_score' => $evaluation->confidence,
            'proposed_answer' => $evaluation->detailedResponse,
            'encrypted_proposed_answer' => encrypt(json_encode([
                'text' => $evaluation->detailedResponse,
                'contact_id' => $contact->getKey(),
                'type_id' => $type->getKey(),
            ])),
        ]);
    }
}
