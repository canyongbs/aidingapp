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

use AidingApp\Contact\Models\Contact;
use AidingApp\KnowledgeBase\Models\KnowledgeBaseItem;
use AidingApp\Portal\DataTransferObjects\KnowledgeBaseArticleData;
use AidingApp\Portal\DataTransferObjects\KnowledgeBaseCategoryData;
use AidingApp\Portal\Models\PortalGuest;
use AidingApp\Portal\Support\KnowledgeBasePortalUrl;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;

class KnowledgeManagementPortalArticleController extends Controller
{
    public function show(string $article): JsonResponse
    {
        $article = KnowledgeBasePortalUrl::resolveArticle($article);

        abort_if($article === null, 404);

        return $this->response($article);
    }

    public function showLegacy(string $category, string $article): JsonResponse
    {
        abort_if(KnowledgeBasePortalUrl::resolveCategory($category) === null, 404);

        $article = KnowledgeBasePortalUrl::resolveArticle($article);

        abort_if($article === null, 404);

        return $this->response($article);
    }

    /**
     * @param  array<string, mixed>|null  $content
     */
    protected static function generateTableOfContents(?array $content, int $maxDepth = 3): string
    {
        if (blank($content) || ! isset($content['content'])) {
            return '';
        }

        $headings = static::extractHeadings($content['content'], $maxDepth);

        if (empty($headings)) {
            return '';
        }

        $result = '<ul>';
        $prev = $headings[0]['level'];

        foreach ($headings as $item) {
            $prev <= $item['level'] ?: $result .= str_repeat('</ul>', $prev - $item['level']);
            $prev >= $item['level'] ?: $result .= '<ul>';

            $result .= '<li><a href="#' . $item['id'] . '">' . e($item['text']) . '</a></li>';

            $prev = $item['level'];
        }

        $result .= '</ul>';

        return $result;
    }

    /**
     * @param  array<int, array<string, mixed>>  $nodes
     *
     * @return array<int, array{level: int, id: string, text: string}>
     */
    protected static function extractHeadings(array $nodes, int $maxDepth): array
    {
        $headings = [];

        foreach ($nodes as $node) {
            if (($node['type'] ?? null) === 'heading') {
                $level = $node['attrs']['level'] ?? 1;

                if ($level <= $maxDepth) {
                    /** @var array<int, array<string, mixed>> $children */
                    $children = $node['content'] ?? [];

                    $text = collect($children)
                        ->map(fn (array $node): ?string => $node['text'] ?? null)
                        ->implode(' ');

                    $id = $node['attrs']['id'] ?? str($text)->kebab()->toString();

                    $headings[] = [
                        'level' => $level,
                        'id' => $id,
                        'text' => $text,
                    ];
                }
            }

            if (! empty($node['content'])) {
                $headings = [...$headings, ...static::extractHeadings($node['content'], $maxDepth)];
            }
        }

        return $headings;
    }

    private function response(KnowledgeBaseItem $article): JsonResponse
    {
        $category = $article->category;

        if (! auth()->guard('contact')->check() && ! session()->has('guest_id')) {
            $portalGuest = PortalGuest::create();
            session()->put('guest_id', $portalGuest->getKey());
        }
        $article->increment('portal_view_count');
        $voterType = session()->has('guest_id') ? (new PortalGuest())->getMorphClass() : (new Contact())->getMorphClass();
        $voterId = session()->has('guest_id') ? session('guest_id') : auth('contact')->user()?->getKey();

        $article->loadCount([
            'votes',
            'votes as helpful_votes_count' => fn (Builder $query) => $query->where('is_helpful', true),
        ]);

        $totalVotes = (int) $article->getAttribute('votes_count');
        $helpfulVotes = (int) $article->getAttribute('helpful_votes_count');

        $helpfulVotePercentage = 0;

        if ($totalVotes > 0) {
            $helpfulVotePercentage = round(($helpfulVotes / $totalVotes) * 100, 0);
        }

        if (! $article->public) {
            return response()->json([], 401);
        }

        $content = $article->article_details ? $article->renderRichContent('article_details') : '';

        if ($article->has_table_of_contents) {
            $tableOfContents = static::generateTableOfContents($article->article_details);

            if (filled($tableOfContents)) {
                $content = '<h2>Table of Contents</h2><div class="prose-toc">' . $tableOfContents . '</div>' . $content;
            }
        }

        return response()->json([
            'category' => KnowledgeBaseCategoryData::from([
                'slug' => $category->slug,
                'name' => $category->name,
                'description' => $category->description,
                'publicId' => $category->public_id,
                'parentCategory' => $category->parentCategory ? KnowledgeBaseCategoryData::from([
                    'slug' => $category->parentCategory->slug,
                    'name' => $category->parentCategory->name,
                    'description' => $category->parentCategory->description,
                    'publicId' => $category->parentCategory->public_id,
                ]) : null,
            ]),
            'article' => KnowledgeBaseArticleData::from([
                'id' => $article->getKey(),
                'categorySlug' => $article->category->slug,
                'name' => $article->title,
                'publicId' => $article->public_id,
                'slug' => KnowledgeBasePortalUrl::articleSlug($article),
                'lastUpdated' => $article->updated_at->toIso8601String(),
                'content' => $content,
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
                'attachments' => $article->getMedia('article_attachments')->map(fn ($media) => [
                    'name' => $media->file_name,
                    'url' => route('api.portal.knowledge-base-article.media.download', ['media' => $media->getKey()]),
                ])->toArray(),
            ]),
            'portal_view_count' => $article->portal_view_count,
            'helpful_vote_percentage' => $helpfulVotePercentage,
        ]);
    }
}
