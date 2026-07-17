<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Aiding App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Aiding App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    <https://www.canyongbs.com> or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AidingApp\ServiceManagement\Actions;

use AidingApp\ServiceManagement\Models\ServiceRequestType;
use AidingApp\ServiceManagement\Models\ServiceRequestTypeCategory;
use App\Features\ServiceRequestTypeMultipleCategoriesFeature;
use Closure;
use Illuminate\Database\Eloquent\Builder;

/**
 * Builds the nested category → type tree shown to contacts (portal and assistant widget).
 *
 * A type is placed under every category it belongs to (and once at the root if it belongs to none).
 * A placement is hidden when the type fails its own visibility restriction, or when any category on
 * the path to that placement fails its own restriction (the whole restricted subtree is pruned).
 */
class BuildContactServiceRequestTypeTree
{
    /**
     * @param  Closure(ServiceRequestType, ?string): array<string, mixed>  $formatType  receives the type and the id of the category the placement sits under (null at the root)
     * @param  (Closure(Builder<ServiceRequestType>): mixed)|null  $prepareTypesQuery  hook to add caller specific eager loads to the types query
     *
     * @return array{categories: array<int, array<string, mixed>>, types: array<int, array<string, mixed>>}
     */
    public function execute(
        ?string $contactTypeId,
        bool $visibilityRestrictionsEnabled,
        Closure $formatType,
        ?Closure $prepareTypesQuery = null,
    ): array {
        $categoriesQuery = ServiceRequestTypeCategory::query()->orderBy('sort');

        $typesQuery = ServiceRequestType::query()
            ->withoutArchived()
            ->orderBy('sort')
            ->when(
                ServiceRequestTypeMultipleCategoriesFeature::active(),
                fn (Builder $query) => $query->with('categories:id'),
            );

        if ($prepareTypesQuery !== null) {
            $prepareTypesQuery($typesQuery);
        }

        if ($visibilityRestrictionsEnabled) {
            $categoriesQuery->with('restrictedToContactTypes:id');
            $typesQuery->with('restrictedToContactTypes:id');
        }

        $categories = $categoriesQuery->get();
        $types = $typesQuery->get();

        $categoriesById = [];
        $categoryAllowed = [];

        foreach ($categories as $category) {
            $categoryAllowed[$category->id] = ! $visibilityRestrictionsEnabled
                || $category->passesOwnVisibilityRestriction($contactTypeId);

            $categoriesById[$category->id] = [
                'id' => $category->id,
                'name' => $category->name,
                'sort' => $category->sort,
                'parent_id' => $category->parent_id,
                'children' => [],
                'types' => [],
            ];
        }

        $topLevelTypes = [];

        foreach ($types as $type) {
            if ($visibilityRestrictionsEnabled && ! $type->passesOwnVisibilityRestriction($contactTypeId)) {
                continue;
            }

            $placedUnderCategory = false;

            foreach ($type->categorySortMap() as $categoryId => $sort) {
                if (! isset($categoriesById[$categoryId])) {
                    continue;
                }

                // The per-area sort lives on the pivot, so each placement is ordered independently
                // of the type's global sort and of its ordering under any other category.
                $categoriesById[$categoryId]['types'][] = [
                    ...$formatType($type, $categoryId),
                    'sort' => $sort,
                ];
                $placedUnderCategory = true;
            }

            if (! $placedUnderCategory) {
                $topLevelTypes[] = $formatType($type, null);
            }
        }

        $topLevelCategories = [];

        foreach ($categoriesById as $id => $category) {
            if ($category['parent_id'] && isset($categoriesById[$category['parent_id']])) {
                $categoriesById[$category['parent_id']]['children'][] = &$categoriesById[$id];
            } else {
                $topLevelCategories[] = &$categoriesById[$id];
            }
        }

        // Remove categories the contact is not allowed to see, including their entire subtree.
        $filterRestrictedCategories = function (array &$nodes) use (&$filterRestrictedCategories, $categoryAllowed): array {
            return array_values(array_filter($nodes, function (array &$node) use (&$filterRestrictedCategories, $categoryAllowed): bool {
                if (! ($categoryAllowed[$node['id']] ?? true)) {
                    return false;
                }

                $node['children'] = $filterRestrictedCategories($node['children']);

                return true;
            }));
        };

        if ($visibilityRestrictionsEnabled) {
            $topLevelCategories = $filterRestrictedCategories($topLevelCategories);
        }

        $sortRecursive = function (array &$nodes) use (&$sortRecursive): void {
            usort($nodes, fn (array $left, array $right): int => ($left['sort'] ?? 0) <=> ($right['sort'] ?? 0));

            foreach ($nodes as &$node) {
                if (! empty($node['types'])) {
                    usort($node['types'], fn (array $left, array $right): int => ($left['sort'] ?? 0) <=> ($right['sort'] ?? 0));
                }

                if (! empty($node['children'])) {
                    $sortRecursive($node['children']);
                }
            }
        };

        $sortRecursive($topLevelCategories);

        usort($topLevelTypes, fn (array $left, array $right): int => ($left['sort'] ?? 0) <=> ($right['sort'] ?? 0));

        // Remove categories that have no types and no children with types (recursively).
        $filterEmptyCategories = function (array &$nodes) use (&$filterEmptyCategories): array {
            return array_values(array_filter($nodes, function (array &$node) use (&$filterEmptyCategories): bool {
                $node['children'] = $filterEmptyCategories($node['children']);

                return ! empty($node['types']) || ! empty($node['children']);
            }));
        };

        $topLevelCategories = $filterEmptyCategories($topLevelCategories);

        return [
            'categories' => $topLevelCategories,
            'types' => $topLevelTypes,
        ];
    }
}
