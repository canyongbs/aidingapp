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

use AidingApp\Ai\Events\PortalAssistant\PortalAssistantActionRequest;
use AidingApp\Ai\Models\PortalAssistantThread;
use AidingApp\Ai\Tools\PortalAssistant\Concerns\FindsDraftServiceRequest;
use Prism\Prism\Tool;

class ShowPrioritySelectorTool extends Tool
{
    use FindsDraftServiceRequest;

    public function __construct(
        protected PortalAssistantThread $thread,
    ) {
        $this
            ->as('show_priority_selector')
            ->for('Displays a UI widget for the user to select the priority level for their service request. This becomes available after the description and title are provided. The system will automatically prompt the user with the available priority options.')
            ->using($this);
    }

    public function __invoke(): string
    {
        $draft = $this->findDraft();

        if (! $draft) {
            return json_encode([
                'error' => true,
                'message' => 'No draft exists.',
            ]);
        }

        $draft->load('serviceRequestFormSubmission.submissible.type');

        $type = $draft->serviceRequestFormSubmission?->submissible?->type;

        if (! $type) {
            return json_encode([
                'error' => true,
                'message' => 'No type selected. User must select a type first.',
            ]);
        }

        $priorities = $type->priorities()
            ->orderByDesc('order')
            ->get(['id', 'name'])
            ->map(fn ($p) => [
                'priority_id' => $p->id,
                'name' => $p->name,
            ])
            ->values()
            ->all();

        if (empty($priorities)) {
            return json_encode([
                'error' => true,
                'message' => 'No priorities available for this service request type.',
            ]);
        }

        // Get default priority to preselect in the UI (highest order)
        $defaultPriorityId = $priorities[0]['priority_id'] ?? null;

        event(new PortalAssistantActionRequest(
            $this->thread,
            'select_priority',
            [
                'priorities' => $priorities,
                'current_priority_id' => $draft->priority?->getKey(),
                'default_priority_id' => $defaultPriorityId,
            ]
        ));

        return 'Widget displayed to user.';
    }
}
