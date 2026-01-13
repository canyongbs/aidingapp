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

namespace AidingApp\Ai\Actions\PortalAssistant;

use AidingApp\ServiceManagement\Models\ServiceRequestType;
use AidingApp\ServiceManagement\Models\ServiceRequestTypeCategory;

class BuildServiceRequestTypesTree
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public function execute(): array
    {
        $categories = ServiceRequestTypeCategory::with([
            'children',
            'types' => fn ($q) => $q->whereHas('form')->with(['priorities' => fn ($q) => $q->orderBy('order')]),
        ])
            ->whereNull('parent_id')
            ->orderBy('sort')
            ->get();

        $uncategorizedTypes = ServiceRequestType::whereHas('form')
            ->whereDoesntHave('category')
            ->with(['priorities' => fn ($q) => $q->orderBy('order')])
            ->get();

        $tree = $categories->map(fn ($category) => $this->formatCategory($category))->all();

        if ($uncategorizedTypes->isNotEmpty()) {
            $tree[] = [
                'category_id' => null,
                'name' => 'Other',
                'children' => [],
                'types' => $uncategorizedTypes->map(fn ($type) => $this->formatType($type))->all(),
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
            ->map(fn ($type) => $this->formatType($type))
            ->values()
            ->all();

        return [
            'category_id' => $category->getKey(),
            'name' => $category->name,
            'children' => $children,
            'types' => $types,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function formatType(ServiceRequestType $type): array
    {
        return [
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
