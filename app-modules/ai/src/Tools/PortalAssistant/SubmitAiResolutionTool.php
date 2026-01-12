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
use Prism\Prism\Tool;
use AidingApp\Ai\Tools\PortalAssistant\Concerns\FindsDraftServiceRequest;

class SubmitAiResolutionTool extends Tool
{
    use FindsDraftServiceRequest;

    public function __construct(
        protected PortalAssistantThread $thread,
    ) {
        $this
            ->as('submit_ai_resolution')
            ->for('Submits an AI resolution attempt with a confidence score. Call this after clarifying questions when you have a potential solution.')
            ->withNumberParameter('confidence_score', 'Your confidence score from 0-100 that this resolution will help the user')
            ->withStringParameter('proposed_answer', 'The resolution text to present to the user')
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

        if ($draft->workflow_phase !== 'resolution') {
            return json_encode([
                'success' => false,
                'error' => 'Not in resolution phase. Current phase: ' . $draft->workflow_phase,
            ]);
        }

        $aiResolutionSettings = app(AiResolutionSettings::class);
        $threshold = $aiResolutionSettings->confidence_threshold;

        $confidenceScore = max(0, min(100, $confidence_score));

        $draft->ai_resolution = [
            'confidence_score' => $confidenceScore,
            'proposed_answer' => $proposed_answer,
            'threshold' => $threshold,
        ];
        $draft->ai_resolution_confidence_score = $confidenceScore;
        $draft->save();

        $meetsThreshold = $confidenceScore >= $threshold;

        if ($meetsThreshold) {
            return json_encode([
                'threshold' => $threshold,
                'confidence_score' => $confidenceScore,
                'meets_threshold' => true,
                'instruction' => 'Present your resolution to the user and ask if it solved their problem. Wait for explicit yes/no response.',
            ]);
        }

        return json_encode([
            'threshold' => $threshold,
            'confidence_score' => $confidenceScore,
            'meets_threshold' => false,
            'instruction' => 'Confidence too low. Do not show resolution to user. Call finalize_service_request to submit for human review.',
        ]);
    }
}
