<?php

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
