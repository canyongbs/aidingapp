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

use AidingApp\Ai\Settings\AiResolutionSettings;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use Prism\Prism\Tool;

class EvaluateAiResolutionTool extends Tool
{
    public function __construct()
    {
        $this
            ->as('evaluate_ai_resolution')
            ->for('Evaluates if the service request can be resolved by AI before submission. Call this after collecting all required fields and clarifying questions.')
            ->withStringParameter('type_id', 'The service request type UUID')
            ->withStringParameter('title', 'The service request title')
            ->withStringParameter('description', 'The service request description')
            ->withStringParameter('questions_summary', 'Summary of clarifying questions and answers')
            ->using($this);
    }

    public function __invoke(
        string $type_id,
        string $title,
        string $description,
        string $questions_summary,
    ): string {
        $settings = app(AiResolutionSettings::class);

        if (! $settings->is_enabled) {
            return json_encode([
                'skipped' => true,
                'reason' => 'AI resolution is not enabled.',
            ]);
        }

        $type = ServiceRequestType::find($type_id);

        if (! $type) {
            return json_encode([
                'error' => true,
                'message' => 'Service request type not found.',
            ]);
        }

        return json_encode([
            'enabled' => true,
            'threshold' => $settings->confidence_threshold,
            'instructions' => 'Based on the collected information, determine if you can provide a resolution. ' .
                'If you can confidently resolve this issue, provide your proposed_answer and rate your confidence_score (0-100). ' .
                'Only propose a resolution if your confidence_score meets or exceeds the threshold.',
            'context' => [
                'type_name' => $type->name,
                'title' => $title,
                'description' => $description,
                'questions_summary' => $questions_summary,
            ],
        ]);
    }
}
