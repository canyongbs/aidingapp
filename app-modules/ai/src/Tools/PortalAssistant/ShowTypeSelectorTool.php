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
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use AidingApp\ServiceManagement\Models\ServiceRequestTypeCategory;
use Prism\Prism\Tool;

class ShowTypeSelectorTool extends Tool
{
    use FindsDraftServiceRequest;

    public function __construct(
        protected PortalAssistantThread $thread,
    ) {
        $this
            ->as('show_type_selector')
            ->for('Displays a UI widget for the user to select a service request type. Call this after fetching types. The system will automatically ask the user to make a selection.')
            ->withStringParameter('suggested_type_id', 'Optional: The UUID of the type to suggest/highlight based on conversation context')
            ->using($this);
    }

    public function __invoke(?string $suggested_type_id = null): string
    {
        $draft = $this->findDraft();

        $typesTree = $this->buildTypesTree();

        if (empty($typesTree)) {
            return json_encode([
                'error' => true,
                'message' => 'No service request types are available.',
            ]);
        }

        $suggestion = null;

        if ($suggested_type_id) {
            $type = ServiceRequestType::whereHas('form')->find($suggested_type_id);

            if ($type) {
                $suggestion = [
                    'type_id' => $type->getKey(),
                    'name' => $type->name,
                    'description' => $type->description,
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

        return 'Widget displayed to user.';
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function buildTypesTree(): array
    {
        $categories = ServiceRequestTypeCategory::with(['children', 'types' => fn ($q) => $q->whereHas('form')])
            ->whereNull('parent_id')
            ->orderBy('sort')
            ->get();

        $uncategorizedTypes = ServiceRequestType::whereHas('form')
            ->whereDoesntHave('category')
            ->get();

        $tree = $categories->map(fn ($category) => $this->formatCategory($category))->all();

        if ($uncategorizedTypes->isNotEmpty()) {
            $tree[] = [
                'category_id' => null,
                'name' => 'Other',
                'children' => [],
                'types' => $uncategorizedTypes->map(fn ($type) => [
                    'type_id' => $type->getKey(),
                    'name' => $type->name,
                    'description' => $type->description,
                ])->all(),
            ];
        }

        return $tree;
    }

    /**
     * @return array<string, mixed>
     */
    protected function formatCategory(ServiceRequestTypeCategory $category): array
    {
        $children = $category->children->map(fn ($child) => $this->formatCategory($child))->all();

        $types = $category->types
            ->filter(fn ($type) => $type->form !== null)
            ->map(fn ($type) => [
                'type_id' => $type->getKey(),
                'name' => $type->name,
                'description' => $type->description,
            ])
            ->values()
            ->all();

        return [
            'category_id' => $category->getKey(),
            'name' => $category->name,
            'children' => $children,
            'types' => $types,
        ];
    }
}
