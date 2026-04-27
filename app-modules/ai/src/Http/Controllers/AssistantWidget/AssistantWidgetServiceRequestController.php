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
      of the licensor in the software. Any use of the licensor's trademarks is subject
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

namespace AidingApp\Ai\Http\Controllers\AssistantWidget;

use AidingApp\ServiceManagement\Models\ServiceRequestType;
use AidingApp\ServiceManagement\Models\ServiceRequestTypeCategory;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class AssistantWidgetServiceRequestController extends Controller
{
    public function index(): JsonResponse
    {
        $categories = ServiceRequestTypeCategory::query()->orderBy('sort')->get();
        $types = ServiceRequestType::query()
            ->withoutArchived()
            ->with(['priorities' => fn ($q) => $q->orderByDesc('order')])
            ->orderBy('sort')
            ->get();

        $catsById = [];

        foreach ($categories as $cat) {
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
            $payload = [
                'id' => $type->getKey(),
                'name' => $type->name,
                'description' => $type->description,
                'icon' => $type->icon ? svg($type->icon, 'h-5 w-5')->toHtml() : null,
                'sort' => $type->sort,
                'category_id' => $type->category_id,
                'priorities' => $type->priorities->map(fn ($p) => [
                    'id' => $p->getKey(),
                    'name' => $p->name,
                    'order' => $p->order,
                ])->values()->all(),
            ];

            if ($type->category_id && isset($catsById[$type->category_id])) {
                $catsById[$type->category_id]['types'][] = $payload;
            } else {
                $topLevelTypes[] = $payload;
            }
        }

        $topLevelCategories = [];

        foreach ($catsById as $id => $cat) {
            if ($cat['parent_id'] && isset($catsById[$cat['parent_id']])) {
                $catsById[$cat['parent_id']]['children'][] = &$catsById[$id];
            } else {
                $topLevelCategories[] = &$catsById[$id];
            }
        }

        $sortRecursive = function (array &$nodes) use (&$sortRecursive) {
            usort($nodes, fn (array $a, array $b) => ($a['sort'] ?? 0) <=> ($b['sort'] ?? 0));

            foreach ($nodes as &$node) {
                if (! empty($node['types'])) {
                    usort($node['types'], fn (array $a, array $b) => ($a['sort'] ?? 0) <=> ($b['sort'] ?? 0));
                }

                if (! empty($node['children'])) {
                    $sortRecursive($node['children']);
                }
            }
        };

        $sortRecursive($topLevelCategories);

        usort($topLevelTypes, fn (array $a, array $b) => ($a['sort'] ?? 0) <=> ($b['sort'] ?? 0));

        return response()->json([
            'categories' => $topLevelCategories,
            'types' => $topLevelTypes,
            'upload_url' => route('widgets.assistant.api.service-request.upload-url'),
            'store_url_base' => route('widgets.assistant.api.service-request.store', ['type' => '__TYPE__']),
        ]);
    }
}
