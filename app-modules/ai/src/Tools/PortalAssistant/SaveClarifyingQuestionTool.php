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

namespace AidingApp\Ai\Tools\PortalAssistant;

use AidingApp\Ai\Models\PortalAssistantThread;
use AidingApp\Ai\Settings\AiResolutionSettings;
use AidingApp\Ai\Tools\PortalAssistant\Concerns\FindsDraftServiceRequest;
use AidingApp\Ai\Tools\PortalAssistant\Concerns\SubmitsServiceRequest;
use AidingApp\ServiceManagement\Enums\ServiceRequestDraftStage;
use AidingApp\ServiceManagement\Enums\ServiceRequestUpdateType;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use Illuminate\Support\Str;
use Prism\Prism\Tool;

class SaveClarifyingQuestionTool extends Tool
{
    use FindsDraftServiceRequest;
    use SubmitsServiceRequest;

    public function __construct(
        protected PortalAssistantThread $thread,
    ) {
        $this
            ->as('save_clarifying_question')
            ->for('CRITICAL: Saves a clarifying question and the user\'s answer. Must be called IMMEDIATELY after the user answers each question. Call this tool exactly 3 times during clarifying_questions stage. This tool is for gathering information ONLY - do NOT provide solutions or advice before calling this tool.')
            ->withStringParameter('question', 'The exact question text you asked the user')
            ->withStringParameter('answer', 'The user\'s complete response to your question')
            ->using($this);
    }

    public function __invoke(string $question, string $answer): string
    {
        $draft = $this->findDraft();

        if (! $draft) {
            return json_encode([
                'success' => false,
                'error' => 'No draft exists.',
            ]);
        }

        $draftStage = ServiceRequestDraftStage::fromServiceRequest($draft);

        if ($draftStage !== ServiceRequestDraftStage::ClarifyingQuestions) {
            return json_encode([
                'success' => false,
                'error' => 'Not in clarifying_questions stage. Current stage: ' . $draftStage->value,
            ]);
        }

        $clarifyingQuestionsCount = $draft->serviceRequestUpdates()
            ->where('update_type', ServiceRequestUpdateType::ClarifyingQuestion)
            ->count();

        if ($clarifyingQuestionsCount >= 3) {
            return json_encode([
                'success' => false,
                'error' => 'Already have 3 clarifying questions. Move to resolution stage.',
            ]);
        }

        $respondent = $draft->respondent;

        $updateUuids = collect([
            (string) Str::orderedUuid(),
            (string) Str::orderedUuid(),
        ]);

        $draft->serviceRequestUpdates()->createQuietly([
            'id' => $updateUuids->shift(),
            'update' => $question,
            'update_type' => ServiceRequestUpdateType::ClarifyingQuestion,
            'internal' => false,
            'created_by_type' => $draft->getMorphClass(),
            'created_by_id' => $draft->getKey(),
        ]);

        $draft->serviceRequestUpdates()->createQuietly([
            'id' => $updateUuids->shift(),
            'update' => $answer,
            'update_type' => ServiceRequestUpdateType::ClarifyingAnswer,
            'internal' => false,
            'created_by_type' => $respondent->getMorphClass(),
            'created_by_id' => $respondent->getKey(),
        ]);

        $completed = $clarifyingQuestionsCount + 1;
        $remaining = 3 - $completed;

        $result = [
            'success' => true,
            'completed' => $completed,
            'remaining' => $remaining,
        ];

        if ($remaining === 0) {
            $aiResolutionSettings = app(AiResolutionSettings::class);
            $result['draft_stage'] = ServiceRequestDraftStage::Resolution->value;
            $result['ai_resolution_enabled'] = $aiResolutionSettings->is_enabled;

            if ($aiResolutionSettings->is_enabled) {
                $result['instruction'] = 'All 3 clarifying questions complete. You are now in the resolution stage. Call get_draft_status to refresh your context, then call check_ai_resolution_validity with your confidence score and proposed answer. DO NOT show the resolution to the user until the tool tells you to.';
            } else {
                // AI resolution disabled - auto-submit for human review
                $requestNumber = $this->submitServiceRequest($draft, false);

                $result['request_number'] = $requestNumber;
                $result['instruction'] = "AI resolution is disabled. Service request has been automatically submitted for human review. Tell the user their request number is {$requestNumber} and a team member will review it.";
            }
        }

        return json_encode($result);
    }
}
