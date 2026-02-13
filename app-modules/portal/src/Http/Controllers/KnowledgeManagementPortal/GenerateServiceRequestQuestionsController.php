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

namespace AidingApp\Portal\Http\Controllers\KnowledgeManagementPortal;

use AidingApp\Ai\Settings\AiClarificationSettings;
use AidingApp\Ai\Settings\AiIntegratedAssistantSettings;
use AidingApp\KnowledgeBase\Models\KnowledgeBaseItem;
use AidingApp\KnowledgeBase\Models\Scopes\KnowledgeBasePortalAssistantItem;
use AidingApp\Portal\Actions\GenerateServiceRequestQuestionsAiPrompt;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use App\Features\AiFeatureTogglesFeature;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Symfony\Component\HttpFoundation\Response;

class GenerateServiceRequestQuestionsController extends Controller
{
    public function __invoke(Request $request, ServiceRequestType $type): JsonResponse
    {
        $contact = auth('contact')->user();

        abort_if(is_null($contact), Response::HTTP_UNAUTHORIZED);

        if (! AiFeatureTogglesFeature::active() || ! app(AiClarificationSettings::class)->is_enabled) {
            return response()->json([
                'fields' => [],
            ]);
        }

        $formData = $request->input('formData', []);

        $prompt = app(GenerateServiceRequestQuestionsAiPrompt::class)->execute($type, $formData, $contact);

        $aiService = app(AiIntegratedAssistantSettings::class)->getDefaultModel()->getService();

        $response = $aiService->complete(
            prompt: 'Return each question on a new line, no need to number them. There should be exactly three questions in total, clarifying the service request based on the provided form data.',
            content: $prompt,
            files: KnowledgeBaseItem::query()->tap(app(KnowledgeBasePortalAssistantItem::class))->get(['id'])->all(),
        );

        $questions = array_values(array_filter(array_map(trim(...), explode(PHP_EOL, $response))));

        return response()->json([
            'fields' => Arr::map(
                $questions,
                fn (string $question): array => [
                    '$formkit' => 'textarea',
                    'name' => encrypt($question),
                    'label' => $question,
                    'validation' => 'required',
                ],
            ),
        ]);
    }
}
