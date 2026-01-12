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
use AidingApp\Contact\Models\Contact;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use AidingApp\ServiceManagement\Models\ServiceRequestTypeCategory;
use Prism\Prism\Tool;

class FetchServiceRequestTypesTool extends Tool
{
    public function __construct(
        protected PortalAssistantThread $thread,
    ) {
        $this
            ->as('fetch_service_request_types')
            ->for('Retrieves available service request types. Call this when the user wants to submit a service request, report an issue, or speak to a human.')
            ->using($this);
    }

    public function __invoke(): string
    {
        $draft = ServiceRequest::withoutGlobalScope('excludeDrafts')
            ->where('portal_assistant_thread_id', $this->thread->getKey())
            ->where('is_draft', true)
            ->latest('created_at')
            ->first();

        $draftCreated = false;

        if (! $draft) {
            $draft = $this->createDraft();
            $draftCreated = true;
        }

        $typesTree = $this->buildTypesTree();

        if (empty($typesTree)) {
            return json_encode([
                'error' => true,
                'message' => 'No service request types are available.',
            ]);
        }

        return json_encode([
            'types_tree' => $typesTree,
            'draft_created' => $draftCreated,
            'workflow_phase' => $draft->workflow_phase,
            'instruction' => 'Call show_type_selector to display the type selection UI. You can pass a suggested_type_id if you can infer the best type from the conversation.',
        ]);
    }

    protected function createDraft(): ServiceRequest
    {
        $contact = $this->thread->author;

        $attributes = [
            'is_draft' => true,
            'workflow_phase' => 'type_selection',
            'clarifying_questions' => [],
            'portal_assistant_thread_id' => $this->thread->getKey(),
        ];

        if ($contact instanceof Contact) {
            $attributes['respondent_id'] = $contact->getKey();
        }

        return ServiceRequest::create($attributes);
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
