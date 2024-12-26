<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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

namespace AidingApp\Portal\Http\Controllers\KnowledgeManagementPortal;

use AidingApp\KnowledgeBase\Models\KnowledgeBaseCategory;
use AidingApp\KnowledgeBase\Models\KnowledgeBaseItem;
use AidingApp\Portal\DataTransferObjects\KnowledgeBaseCategoryData;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;

class KnowledgeManagementPortalCategoryController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(
            KnowledgeBaseCategoryData::collection(
                KnowledgeBaseCategory::query()
                    ->where('parent_id', null)
                    ->orderBy('name')
                    ->get()
                    ->map(function (KnowledgeBaseCategory $category) {
                        return [
                            'slug' => $category->slug,
                            'name' => $category->name,
                            'description' => $category->description,
                            'icon' => $category->icon ? svg($category->icon, 'h-6 w-6')->toHtml() : null,
                        ];
                    })
                    ->toArray()
            )
        );
    }

    public function show(KnowledgeBaseCategory $category): JsonResponse
    {
        return response()->json([
            'category' => KnowledgeBaseCategoryData::from([
                'slug' => $category->slug,
                'name' => $category->name,
                'description' => $category->description,
                'parentCategory' => $category->parentCategory,
                'subCategories' => $category
                    ->subCategories()
                    ->with(['parentCategory:id,name,slug'])
                    ->get()
                    ->map(function (KnowledgeBaseCategory $subCategory) {
                        return KnowledgeBaseCategoryData::from([
                            'slug' => $subCategory->slug,
                            'name' => $subCategory->name,
                            'description' => $subCategory->description,
                            'icon' => $subCategory->icon ? svg($subCategory->icon, 'h-6 w-6')->toHtml() : null,
                            'parentCategory' => $subCategory->parentCategory,
                        ]);
                    }),
            ]),
            'articles' => $category->knowledgeBaseItems()
                ->with('tags')
                ->public()
                ->when(request()->get('filter') === 'featured', function (Builder $query) {
                    $query->where('is_featured', true);
                })
                ->when(request()->get('filter') === 'most-viewed', function (Builder $query) {
                    $query->where('portal_view_count', '>', 0)->orderBy('portal_view_count', 'desc');
                })
                ->paginate(5)
                ->through(function (KnowledgeBaseItem $article) {
                    return [
                        'id' => $article->getKey(),
                        'categorySlug' => $article->category->slug,
                        'name' => $article->title,
                        'tags' => $article->tags()
                            ->orderBy('name')
                            ->select([
                                'id',
                                'name',
                            ])
                            ->get()
                            ->toArray(),
                        'featured' => $article->is_featured,
                    ];
                }),
        ]);
    }
}
