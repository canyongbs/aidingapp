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

namespace AidingApp\Portal\Http\Controllers\KnowledgeManagementPortal;

use AidingApp\ServiceManagement\Models\Scopes\WithCategoryAssignments;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use AidingApp\ServiceManagement\Models\ServiceRequestTypeCategory;
use App\Features\ServiceRequestTypeVisibilityRestrictionsFeature;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class ServiceRequestTypesController extends Controller
{
    public function index(): JsonResponse
    {
        // Load all categories and types and build a nested tree in PHP so the frontend can
        // render top-level categories/types and navigate into subcategories without
        // additional requests.
        $visibilityRestrictionsEnabled = ServiceRequestTypeVisibilityRestrictionsFeature::active();

        $contactTypeId = $visibilityRestrictionsEnabled ? auth('contact')->user()?->type_id : null;

        $categoriesQuery = ServiceRequestTypeCategory::query()->orderBy('sort');

        $typesQuery = ServiceRequestType::query()->withoutArchived()->orderBy('sort')->tap(new WithCategoryAssignments());

        if ($visibilityRestrictionsEnabled) {
            $categoriesQuery->with('restrictedToContactTypes:id');
            $typesQuery->with('restrictedToContactTypes:id');
        }

        $categories = $categoriesQuery->get();

        $types = $typesQuery->get();

        $catsById = [];
        $categoryAllowed = [];

        foreach ($categories as $cat) {
            $categoryAllowed[$cat->id] = ! $visibilityRestrictionsEnabled
                || $cat->passesOwnVisibilityRestriction($contactTypeId);

            $catsById[$cat->id] = [
                'id' => $cat->id,
                'name' => $cat->name,
                'sort' => $cat->sort,
                'parent_id' => $cat->parent_id,
                'children' => [],
                'types' => [],
            ];
        }

        $topLevelTypes = [];

        foreach ($types as $type) {
            if ($visibilityRestrictionsEnabled && ! $type->passesOwnVisibilityRestriction($contactTypeId)) {
                continue;
            }

            $categoryId = $type->firstCategoryId();

            $payload = [
                'id' => $type->getKey(),
                'name' => $type->name,
                'description' => $type->description,
                'icon' => $type->icon ? svg($type->icon, 'h-6 w-6')->toHtml() : null,
                'sort' => $type->sort,
                'category_id' => $categoryId,
            ];

            if ($categoryId && isset($catsById[$categoryId])) {
                $catsById[$categoryId]['types'][] = $payload;
            } else {
                $topLevelTypes[] = $payload;
            }
        }

        // Attach children categories to their parents
        $topLevelCategories = [];

        foreach ($catsById as $id => $cat) {
            if ($cat['parent_id'] && isset($catsById[$cat['parent_id']])) {
                $catsById[$cat['parent_id']]['children'][] = &$catsById[$id];
            } else {
                $topLevelCategories[] = &$catsById[$id];
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

        // Recursive sort for children and types using a closure to avoid function redeclaration
        $sortRecursive = function (array &$nodes) use (&$sortRecursive) {
            usort($nodes, function (array $left, array $right): int {
                return ($left['sort'] ?? 0) <=> ($right['sort'] ?? 0);
            });

            foreach ($nodes as &$node) {
                if (! empty($node['types'])) {
                    usort($node['types'], function (array $left, array $right): int {
                        return ($left['sort'] ?? 0) <=> ($right['sort'] ?? 0);
                    });
                }

                if (! empty($node['children'])) {
                    $sortRecursive($node['children']);
                }
            }
        };

        $sortRecursive($topLevelCategories);

        // Also sort top-level types
        usort($topLevelTypes, function (array $left, array $right): int {
            return ($left['sort'] ?? 0) <=> ($right['sort'] ?? 0);
        });

        // Remove categories that have no types and no children with types (recursively)
        $filterEmptyCategories = function (array &$nodes) use (&$filterEmptyCategories): array {
            return array_values(array_filter($nodes, function (array &$node) use (&$filterEmptyCategories): bool {
                $node['children'] = $filterEmptyCategories($node['children']);

                return ! empty($node['types']) || ! empty($node['children']);
            }));
        };

        $topLevelCategories = $filterEmptyCategories($topLevelCategories);

        return response()->json([
            'categories' => $topLevelCategories,
            'types' => $topLevelTypes,
        ]);
    }
}
