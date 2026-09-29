<?php

namespace AidingApp\Portal\Support;

use AidingApp\KnowledgeBase\Models\KnowledgeBaseCategory;
use AidingApp\KnowledgeBase\Models\KnowledgeBaseItem;
use AidingApp\KnowledgeBase\Support\KnowledgeBasePublicId;
use App\Features\KnowledgeBasePortalStableUrlsFeature;
use Illuminate\Support\Str;

final class KnowledgeBasePortalUrl
{
    private const string PUBLIC_ID_PATTERN = '[0-9A-Za-z]';

    public static function category(KnowledgeBaseCategory $category, bool $absolute = true): string
    {
        $category->loadMissing('parentCategory');

        if ($category->parentCategory) {
            return route('portal.subcategory.show', [
                'category' => self::categoryLocator($category->parentCategory),
                'subcategory' => self::categoryLocator($category),
            ], $absolute);
        }

        return route('portal.category.show', [
            'category' => self::categoryLocator($category),
        ], $absolute);
    }

    public static function article(KnowledgeBaseItem $article, bool $absolute = true): string
    {
        $article->loadMissing('category.parentCategory');

        if ($article->category->parentCategory) {
            return route('portal.subcategory.article.show', [
                'category' => self::categoryLocator($article->category->parentCategory),
                'subcategory' => self::categoryLocator($article->category),
                'article' => self::articleLocator($article),
            ], $absolute);
        }

        return route('portal.article.show', [
            'category' => self::categoryLocator($article->category),
            'article' => self::articleLocator($article),
        ], $absolute);
    }

    public static function resolveCategory(string $locator): ?KnowledgeBaseCategory
    {
        if (KnowledgeBasePortalStableUrlsFeature::active() && ($publicId = self::publicIdFromLocator($locator))) {
            $category = KnowledgeBaseCategory::query()
                ->where('public_id', $publicId)
                ->first();

            if ($category) {
                return $category;
            }
        }

        if (Str::isUuid($locator)) {
            return KnowledgeBaseCategory::query()->find($locator);
        }

        return KnowledgeBaseCategory::query()
            ->where('slug', $locator)
            ->first();
    }

    public static function resolveArticle(string $locator): ?KnowledgeBaseItem
    {
        if (KnowledgeBasePortalStableUrlsFeature::active() && ($publicId = self::publicIdFromLocator($locator))) {
            $article = KnowledgeBaseItem::query()
                ->where('public_id', $publicId)
                ->first();

            if ($article) {
                return $article;
            }
        }

        if (Str::isUuid($locator)) {
            return KnowledgeBaseItem::query()->find($locator);
        }

        return null;
    }

    public static function categoryLocator(KnowledgeBaseCategory $category): string
    {
        return "{$category->slug}-{$category->public_id}";
    }

    public static function articleLocator(KnowledgeBaseItem $article): string
    {
        return self::articleSlug($article) . "-{$article->public_id}";
    }

    public static function articleSlug(KnowledgeBaseItem $article): string
    {
        return Str::slug($article->title);
    }

    private static function publicIdFromLocator(string $locator): ?string
    {
        preg_match('/-(' . self::PUBLIC_ID_PATTERN . '{' . KnowledgeBasePublicId::LENGTH . '})$/', $locator, $matches);

        return $matches[1] ?? null;
    }
}
