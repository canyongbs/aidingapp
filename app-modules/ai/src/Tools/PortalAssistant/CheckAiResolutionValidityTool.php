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

namespace AidingApp\Ai\Tools\PortalAssistant;

use AidingApp\Ai\Actions\PortalAssistant\GetDraftStatus;
use AidingApp\Ai\Models\PortalAssistantThread;
use AidingApp\Ai\Settings\AiResolutionSettings;
use AidingApp\Ai\Tools\PortalAssistant\Concerns\FindsDraftServiceRequest;
use AidingApp\Ai\Tools\PortalAssistant\Concerns\SubmitsServiceRequest;
use AidingApp\ServiceManagement\Enums\ServiceRequestDraftStage;
use AidingApp\ServiceManagement\Enums\ServiceRequestUpdateType;
use Prism\Prism\Tool;

class CheckAiResolutionValidityTool extends Tool
{
    use FindsDraftServiceRequest;
    use SubmitsServiceRequest;

    public function __construct(
        protected PortalAssistantThread $thread,
    ) {
        $this
            ->as('check_ai_resolution_validity')
            ->for('Checks if an AI resolution meets the confidence threshold. Call this after clarifying questions when you have a potential solution. DO NOT show the resolution to the user until this tool returns meets_threshold: true.')
            ->withNumberParameter('confidence_score', 'Your confidence score from 0-100 that this resolution will help the user')
            ->withStringParameter('proposed_answer', 'The resolution text (do not show to user yet)')
            ->using($this);
    }

    public function __invoke(int $confidence_score, string $proposed_answer): string
    {
        $draft = $this->findDraft();

        if (! $draft) {
            return json_encode([
                'success' => false,
                'error' => 'No draft exists.',
            ]);
        }

        $draftStage = ServiceRequestDraftStage::fromServiceRequest($draft);

        if ($draftStage !== ServiceRequestDraftStage::Resolution) {
            // Too early - return draft status with next instruction
            $draftStatus = app(GetDraftStatus::class)->execute($draft);

            return json_encode([
                'success' => false,
                'error' => 'Not ready for resolution yet. Complete the required steps first.',
                ...$draftStatus,
            ]);
        }

        $aiResolutionSettings = app(AiResolutionSettings::class);
        $threshold = $aiResolutionSettings->confidence_threshold;

        $confidenceScore = max(0, min(100, $confidence_score));
        $meetsThreshold = $confidenceScore >= $threshold;

        $draft->ai_resolution_confidence_score = $confidenceScore;
        $draft->save();

        $draft->serviceRequestUpdates()->createQuietly([
            'update' => "Based on the information you've provided, here is a potential solution:\n\n{$proposed_answer}\n\nDid this resolve your issue?",
            'update_type' => ServiceRequestUpdateType::AiResolutionProposed,
            'internal' => false,
            'created_by_type' => $draft->getMorphClass(),
            'created_by_id' => $draft->getKey(),
        ]);

        if ($meetsThreshold) {
            return json_encode([
                'meets_threshold' => true,
                'next_instruction' => 'Present your resolution to the user. Do NOT ask for more details - it\'s too late for that. You MUST end with exactly: "Did this help resolve your issue?" When they respond, call record_resolution_response IMMEDIATELY - "yes/worked/thanks" = accepted=true, "no/didn\'t work/still broken" = accepted=false. Do NOT continue troubleshooting or offer more help - this submits the request.',
            ]);
        }

        // Confidence below threshold - auto-submit without showing resolution to user
        $draft->is_ai_resolution_attempted = true;
        $draft->is_ai_resolution_successful = false;
        $draft->save();

        $requestNumber = $this->submitServiceRequest($draft, false);

        return json_encode([
            'meets_threshold' => false,
            'request_number' => $requestNumber,
            'next_instruction' => "Confidence too low - resolution NOT shown. Request submitted for human review. Tell user their request number is {$requestNumber}.",
        ]);
    }
}
