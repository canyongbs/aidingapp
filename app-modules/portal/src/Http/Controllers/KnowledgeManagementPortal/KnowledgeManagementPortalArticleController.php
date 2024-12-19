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

use AidingApp\Contact\Models\Contact;
use AidingApp\KnowledgeBase\Models\KnowledgeBaseCategory;
use AidingApp\KnowledgeBase\Models\KnowledgeBaseItem;
use AidingApp\Portal\DataTransferObjects\KnowledgeBaseArticleData;
use AidingApp\Portal\DataTransferObjects\KnowledgeBaseCategoryData;
use AidingApp\Portal\Models\PortalGuest;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class KnowledgeManagementPortalArticleController extends Controller
{
    public function show(KnowledgeBaseCategory $category, KnowledgeBaseItem $article): JsonResponse
    {
        if (! auth()->guard('contact')->check() && ! session()->has('guest_id')) {
            $portalGuest = PortalGuest::create();
            session()->put('guest_id', $portalGuest->getKey());
        }
        $article->increment('portal_view_count');
        $voterType = session()->has('guest_id') ? (new PortalGuest())->getMorphClass() : (new Contact())->getMorphClass();
        $voterId = session()->has('guest_id') ? session('guest_id') : auth('contact')->user()?->getKey();

        $totalVotes = $article->votes->count();
        $helpfulVotes = $article->votes->where('is_helpful', true)->count();

        $helpfulVotePercentage = 0;

        if ($totalVotes > 0) {
            $helpfulVotePercentage = round(($helpfulVotes / $totalVotes) * 100, 0);
        }

        if (! $article->public) {
            return response()->json([], 401);
        }

        return response()->json([
            'category' => KnowledgeBaseCategoryData::from([
                'slug' => $category->slug,
                'name' => $category->name,
                'description' => $category->description,
                'parentCategory' => $category->parentCategory,
            ]),
            'article' => KnowledgeBaseArticleData::from([
                'id' => $article->getKey(),
                'categorySlug' => $article->category->slug,
                'name' => $article->title,
                'lastUpdated' => $article->updated_at->format('M d Y, h:m a'),
                'content' => $article->article_details ? tiptap_converter()->record($article, attribute: 'article_details')->asHTML($article->article_details) : '',
                'tags' => $article->tags()
                    ->orderBy('name')
                    ->select([
                        'id',
                        'name',
                    ])
                    ->get()
                    ->toArray(),
                'vote' => optional(
                    $article->votes()
                        ->where('voter_id', $voterId)
                        ->where('voter_type', $voterType)
                        ->select([
                            'id',
                            'is_helpful',
                        ])
                        ->first()
                )->toArray(),
                'featured' => $article->is_featured,
            ]),
            'portal_view_count' => $article->portal_view_count,
            'helpful_vote_percentage' => $helpfulVotePercentage,
        ]);
    }
}
