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

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use AidingApp\Contact\Models\Contact;
use AidingApp\Portal\Models\PortalGuest;
use AidingApp\Portal\Models\KnowledgeBaseArticleVote;
use AidingApp\Portal\Http\Requests\StoreKnowledgeBaseArticleVoteRequest;

class StoreKnowledgeBaseArticleVoteController extends Controller
{
    public function __invoke(StoreKnowledgeBaseArticleVoteRequest $request): JsonResponse
    {
        $articleVote = [];
        $voterType = session()->has('guest_id') ? PortalGuest::class : Contact::class;
        $voterId = session()->has('guest_id') ? session('guest_id') : auth('contact')->user()->id;

        if (! is_null($request->articleVote)) {
            Log::info('here');
            $articleVote = KnowledgeBaseArticleVote::where('article_id', $request->articleId)->where('voter_id', $voterId)->first();

            if (empty($articleVote)) {
                $articleVote = new KnowledgeBaseArticleVote();
                $articleVote->voter_id = $voterId;
                $articleVote->voter_type = $voterType;
                $articleVote->article_id = $request->articleId;
            }
            $articleVote->is_helpful = $request->articleVote;
            $articleVote->save();
        } else {
            KnowledgeBaseArticleVote::where('article_id', $request->articleId)->where('voter_id', $voterId)->delete();
        }

        return response()->json($articleVote, 200);
    }
}
