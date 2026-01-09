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

class SaveClarifyingQuestionTool extends Tool
{
    public function __construct(
        protected PortalAssistantThread $thread,
    ) {
        $this
            ->as('save_clarifying_question')
            ->for('Saves a clarifying question and the user\'s answer. Must be called exactly 3 times during the clarifying_questions phase.')
            ->withStringParameter('question', 'The question that was asked')
            ->withStringParameter('answer', 'The user\'s response')
            ->using($this);
    }

    public function __invoke(string $question, string $answer): string
    {
        $draft = $this->thread->draftServiceRequest;

        if (! $draft) {
            return json_encode([
                'success' => false,
                'error' => 'No draft exists.',
            ]);
        }

        if ($draft->workflow_phase !== 'clarifying_questions') {
            return json_encode([
                'success' => false,
                'error' => 'Not in clarifying_questions phase. Current phase: ' . $draft->workflow_phase,
            ]);
        }

        $clarifyingQuestions = $draft->clarifying_questions ?? [];

        if (count($clarifyingQuestions) >= 3) {
            return json_encode([
                'success' => false,
                'error' => 'Already have 3 clarifying questions. Move to resolution phase.',
            ]);
        }

        $clarifyingQuestions[] = [
            'question' => $question,
            'answer' => $answer,
        ];

        $draft->clarifying_questions = $clarifyingQuestions;
        $completed = count($clarifyingQuestions);
        $remaining = 3 - $completed;

        if ($remaining === 0) {
            $draft->workflow_phase = 'resolution';
        }

        $draft->save();

        $result = [
            'success' => true,
            'completed' => $completed,
            'remaining' => $remaining,
        ];

        if ($remaining === 0) {
            $aiResolutionSettings = app(AiResolutionSettings::class);
            $result['workflow_phase'] = 'resolution';
            $result['ai_resolution_enabled'] = $aiResolutionSettings->is_enabled;

            if ($aiResolutionSettings->is_enabled) {
                $result['instruction'] = 'Attempt to resolve the request using available knowledge. Call submit_ai_resolution with your confidence score and proposed answer.';
            } else {
                $result['instruction'] = 'Call finalize_service_request to submit for human review.';
            }
        }

        return json_encode($result);
    }
}
