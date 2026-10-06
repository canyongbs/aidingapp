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

namespace AidingApp\Portal\Support;

use AidingApp\KnowledgeBase\Models\KnowledgeBaseCategory;
use AidingApp\KnowledgeBase\Models\KnowledgeBaseItem;
use AidingApp\KnowledgeBase\Support\KnowledgeBasePublicId;
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
        if ($publicId = self::publicIdFromLocator($locator)) {
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
        if ($publicId = self::publicIdFromLocator($locator)) {
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
