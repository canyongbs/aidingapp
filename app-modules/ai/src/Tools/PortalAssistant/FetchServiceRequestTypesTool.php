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

use AidingApp\Ai\Models\PortalAssistantThread;
use AidingApp\Ai\Tools\PortalAssistant\Concerns\FindsDraftServiceRequest;
use AidingApp\Ai\Tools\PortalAssistant\Concerns\LogsToolExecution;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use AidingApp\ServiceManagement\Models\ServiceRequestTypeCategory;
use Prism\Prism\Tool;

class FetchServiceRequestTypesTool extends Tool
{
    use FindsDraftServiceRequest;
    use LogsToolExecution;

    public function __construct(
        protected PortalAssistantThread $thread,
    ) {
        $this
            ->as('fetch_service_request_types')
            ->for('STEP 1: Retrieves the complete list of available service request types. Call this ONLY when: (1) User first expresses intent to submit a NEW service request, or (2) User explicitly wants to CHANGE to a different type. DO NOT call if already working on a draft unless user explicitly requests to change types. Returns a types_tree to analyze before calling show_type_selector.')
            ->using($this);
    }

    public function __invoke(): string
    {
        $draft = $this->findDraft();

        $typesTree = $this->buildTypesTree();

        if (empty($typesTree)) {
            $result = json_encode([
                'error' => true,
                'message' => 'No service request types are available.',
            ]);
            $this->logToolResult('fetch_service_request_types', $result);

            return $result;
        }

        $result = json_encode([
            'types_tree' => $typesTree,
            'has_draft' => $draft !== null,
            'instruction' => 'CRITICAL: You MUST analyze the types_tree to find a matching type BEFORE calling show_type_selector. Look at the type name for matches. Examples: User says "printer broken" → matches "Printer Issue" (type_id: "d691de0b-c90d-44b0-aa2b-6e17cf0ea10c"), User says "password help" → matches "Password Reset". If you find a match, extract the EXACT type_id UUID string and pass it like: show_type_selector(suggested_type_id="d691de0b-c90d-44b0-aa2b-6e17cf0ea10c"). If NO clear match exists, call show_type_selector() with NO suggested_type_id parameter at all - omit it completely from your tool call.',
        ]);

        $this->logToolResult('fetch_service_request_types', $result);

        return $result;
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
                'name' => 'Other',
                'children' => [],
                'types' => $uncategorizedTypes->map(fn ($type) => [
                    'type_id' => $type->getKey(),
                    'name' => $type->name,
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
            ])
            ->values()
            ->all();

        return [
            'name' => $category->name,
            'children' => $children,
            'types' => $types,
        ];
    }
}
