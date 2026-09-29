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

namespace AidingApp\Portal\Http\Middleware;

use AidingApp\Portal\Support\KnowledgeBasePortalUrl;
use App\Features\KnowledgeBasePortalStableUrlsFeature;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureKnowledgeBasePortalUrlIsCanonical
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! KnowledgeBasePortalStableUrlsFeature::active()) {
            return $next($request);
        }

        $canonicalUrl = match ($request->route()?->getName()) {
            'portal.category.show' => $this->categoryUrl((string) $request->route('category')),
            'portal.subcategory.show' => $this->categoryUrl((string) $request->route('subcategory')),
            'portal.article.show', 'portal.subcategory.article.show' => $this->articleUrl((string) $request->route('article')),
            default => null,
        };

        if ($canonicalUrl === null || $canonicalUrl === '/' . $request->path()) {
            return $next($request);
        }

        return $this->redirect($request, $canonicalUrl);
    }

    private function categoryUrl(string $locator): ?string
    {
        $category = KnowledgeBasePortalUrl::resolveCategory($locator);

        return $category ? KnowledgeBasePortalUrl::category($category, absolute: false) : null;
    }

    private function articleUrl(string $locator): ?string
    {
        $article = KnowledgeBasePortalUrl::resolveArticle($locator);

        if (! $article?->public) {
            return null;
        }

        return KnowledgeBasePortalUrl::article($article, absolute: false);
    }

    private function redirect(Request $request, string $url): RedirectResponse
    {
        $query = $request->getQueryString();

        return redirect($query ? "{$url}?{$query}" : $url, Response::HTTP_MOVED_PERMANENTLY);
    }
}
