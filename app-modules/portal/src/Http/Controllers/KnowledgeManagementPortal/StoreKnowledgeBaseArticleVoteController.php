<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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

use AidingApp\KnowledgeBase\Models\KnowledgeBaseItem;
use AidingApp\Portal\Http\Requests\StoreKnowledgeBaseArticleVoteRequest;
use AidingApp\Portal\Models\KnowledgeBaseArticleVote;
use AidingApp\Portal\Models\PortalGuest;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class StoreKnowledgeBaseArticleVoteController extends Controller
{
    public function __invoke(StoreKnowledgeBaseArticleVoteRequest $request): JsonResponse
    {
        $articleVote = [];
        /**
         * @param PortalGuest|Contact|null $voter
         */
        $voter = auth('contact')->user() ?? PortalGuest::find(session('guest_id'));

        if (is_null($voter)) {
            $voter = PortalGuest::create();
            session()->put('guest_id', $voter->getKey());
        }

        if (! is_null($request->article_vote)) {
            $articleVote = $voter->knowledgeBaseArticleVotes()->where('article_id', $request->article_id)->first();

            if (empty($articleVote)) {
                $articleVote = new KnowledgeBaseArticleVote();
                $articleVote->voter()->associate($voter);
                $articleVote->article_id = $request->article_id;
            }
            $articleVote->is_helpful = $request->article_vote;
            $articleVote->save();
        } else {
            if ($voter) {
                $voter->knowledgeBaseArticleVotes()->where('article_id', $request->article_id)->delete();
            }
        }
        $helpfulVoteData = KnowledgeBaseItem::withCount([
            'votes',
            'votes as helpful_votes_count' => function ($query) {
                $query->where('is_helpful', true);
            },
        ])->find($request->article_id);

        $helpfulVotePercentage = 0;

        if ($helpfulVoteData->votes_count > 0) {
            $helpfulVotePercentage = round(($helpfulVoteData->helpful_votes_count / $helpfulVoteData->votes_count) * 100, 0);
        }

        $articleVote['helpful_vote_percentage'] = $helpfulVotePercentage;

        return response()->json($articleVote, 200);
    }
}
