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

use AidingApp\Ai\Actions\PortalAssistant\BuildServiceRequestTypesTree;
use AidingApp\Ai\Events\PortalAssistant\PortalAssistantActionRequest;
use AidingApp\Ai\Models\PortalAssistantThread;
use AidingApp\Ai\Tools\PortalAssistant\Concerns\FindsDraftServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use Prism\Prism\Tool;

class ShowTypeSelectorTool extends Tool
{
    use FindsDraftServiceRequest;

    public function __construct(
        protected PortalAssistantThread $thread,
    ) {
        $this
            ->as('show_type_selector')
            ->for('Displays a UI widget to select a service request type. Call this AFTER get_service_request_types_for_suggestion. If you identified a match in the types_tree, pass that type_id as suggested_type_id. If no match, omit the parameter.')
            ->withStringParameter('suggested_type_id', 'REQUIRED IF MATCH FOUND: The exact UUID of the type to pre-select (e.g., "d691de0b-c90d-44b0-aa2b-6e17cf0ea10c"). Only omit this parameter if you found absolutely no matching type in the types_tree. Never pass an empty string.')
            ->using($this);
    }

    public function __invoke(?string $suggested_type_id = null): string
    {
        $draft = $this->findDraft();

        $typesTree = app(BuildServiceRequestTypesTree::class)->execute();

        if (empty($typesTree)) {
            return json_encode([
                'error' => true,
                'message' => 'No service request types are available.',
            ]);
        }

        $suggestion = null;

        if ($suggested_type_id && $suggested_type_id !== '') {
            $type = ServiceRequestType::whereHas('form')
                ->with(['priorities' => fn ($q) => $q->orderBy('order')])
                ->find($suggested_type_id);

            if ($type) {
                $suggestion = [
                    'type_id' => $type->getKey(),
                    'name' => $type->name,
                    'description' => $type->description,
                    'priorities' => $type->priorities
                        ->map(fn ($p) => [
                            'priority_id' => $p->id,
                            'name' => $p->name,
                        ])
                        ->values()
                        ->all(),
                ];
            }
        }

        event(new PortalAssistantActionRequest(
            $this->thread,
            'select_service_request_type',
            [
                'suggestion' => $suggestion,
                'types_tree' => $typesTree,
            ]
        ));

        if ($suggestion) {
            return json_encode([
                'success' => true,
                'suggested_type_name' => $suggestion['name'],
                'next_instruction' => "Tell user: \"I think '{$suggestion['name']}' fits your needs. You can confirm or choose a different type.\" Then wait for their selection.",
            ]);
        }

        return json_encode([
            'success' => true,
            'next_instruction' => 'Tell user: "Please select the type that best matches your request." Then wait for their selection.',
        ]);
    }
}
