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

use Illuminate\Http\Request;
use App\Models\Scopes\SearchBy;
use Illuminate\Support\Stringable;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;
use AidingApp\KnowledgeBase\Models\KnowledgeBaseItem;
use AidingApp\KnowledgeBase\Models\KnowledgeBaseCategory;
use AidingApp\Portal\DataTransferObjects\KnowledgeBaseArticleData;
use AidingApp\Portal\DataTransferObjects\KnowledgeBaseCategoryData;
use AidingApp\Portal\DataTransferObjects\KnowledgeManagementSearchData;

class KnowledgeManagementPortalSearchController extends Controller
{
    public function get(Request $request): KnowledgeManagementSearchData
    {
        $search = str(json_decode($request->get('search')))
            ->lower()
            ->trim();

        $tags = str($request->get('tags'))
            ->trim()
            ->when(
                fn (Stringable $string) => $string->isEmpty(),
                fn () => collect(),
                fn (Stringable $string) => $string->explode(',')
            );

        $itemData = KnowledgeBaseArticleData::collection(
            KnowledgeBaseItem::query()
                ->public()
                ->with('tags')
                ->when($search->isNotEmpty(), fn (Builder $query) => $query->tap(new SearchBy('title', $search)))
                ->when($tags->isNotEmpty(), fn (Builder $query) => $query->whereHas('tags', fn (Builder $query) => $query->whereIn('id', $tags)))
                ->get()
                ->map(function (KnowledgeBaseItem $article) {
                    return [
                        'id' => $article->getKey(),
                        'categoryId' => $article->category_id,
                        'name' => $article->title,
                        'tags' => $article->tags()
                            ->orderBy('name')
                            ->select([
                                'id',
                                'name',
                            ])
                            ->get()
                            ->toArray(),
                    ];
                })
                ->toArray()
        );

        $categoryData = KnowledgeBaseCategoryData::collection(
            KnowledgeBaseCategory::query()
                ->tap(new SearchBy('name', $search))
                ->get()
                ->map(function (KnowledgeBaseCategory $category) {
                    return [
                        'id' => $category->getKey(),
                        'name' => $category->name,
                        'description' => $category->description,
                    ];
                })
                ->toArray()
        );

        $searchResults = KnowledgeManagementSearchData::from([
            'articles' => $itemData,
            'categories' => $categoryData,
        ]);

        return $searchResults->wrap('data');
    }
}
