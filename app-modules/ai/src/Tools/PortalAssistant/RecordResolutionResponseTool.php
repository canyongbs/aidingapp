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
use AidingApp\Ai\Tools\PortalAssistant\Concerns\FindsDraftServiceRequest;
use AidingApp\Ai\Tools\PortalAssistant\Concerns\SubmitsServiceRequest;
use AidingApp\ServiceManagement\Enums\ServiceRequestDraftStage;
use AidingApp\ServiceManagement\Enums\ServiceRequestUpdateType;
use Prism\Prism\Tool;

class RecordResolutionResponseTool extends Tool
{
    use FindsDraftServiceRequest;
    use SubmitsServiceRequest;

    public function __construct(
        protected PortalAssistantThread $thread,
    ) {
        $this
            ->as('record_resolution_response')
            ->for('Records the user\'s response to your resolution attempt. Call this after presenting a resolution and getting explicit yes/no feedback.')
            ->withBooleanParameter('accepted', 'Whether the user said the resolution solved their problem')
            ->using($this);
    }

    public function __invoke(bool $accepted): string
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
            return json_encode([
                'success' => false,
                'error' => 'Not in resolution stage. Current stage: ' . $draftStage?->value,
            ]);
        }

        $contact = $this->thread->contact;

        $draft->serviceRequestUpdates()->create([
            'update' => $accepted ? 'Yes, this resolved my issue.' : 'No, this did not resolve my issue.',
            'update_type' => ServiceRequestUpdateType::AiResolutionResponse,
            'internal' => false,
            'created_by_type' => $contact->getMorphClass(),
            'created_by_id' => $contact->getKey(),
        ]);

        $draft->is_ai_resolution_attempted = true;
        $draft->is_ai_resolution_successful = $accepted;
        $draft->save();

        $requestNumber = $this->submitServiceRequest($draft, $accepted);

        $instruction = $accepted
            ? "Tell the user their issue has been marked as resolved. Provide the request number ({$requestNumber}) for their records."
            : "Tell the user their request has been submitted for human review. Provide the request number ({$requestNumber}) and let them know a team member will follow up.";

        return json_encode([
            'success' => true,
            'request_number' => $requestNumber,
            'resolution_accepted' => $accepted,
            'status' => $accepted ? 'resolved' : 'open',
            'instruction' => $instruction,
        ]);
    }
}
