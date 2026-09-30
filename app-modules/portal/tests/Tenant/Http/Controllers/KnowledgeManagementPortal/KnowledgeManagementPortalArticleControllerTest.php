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

use AidingApp\KnowledgeBase\Models\KnowledgeBaseCategory;
use AidingApp\KnowledgeBase\Models\KnowledgeBaseItem;
use AidingApp\Portal\Models\KnowledgeBaseArticleVote;
use AidingApp\Portal\Settings\PortalSettings;
use App\Features\KnowledgeBasePortalStableUrlsFeature;

use function Pest\Laravel\getJson;

beforeEach(function () {
    $settings = app(PortalSettings::class);
    $settings->knowledge_management_portal_enabled = true;
    $settings->save();
});

it('resolves a public article by its public ID locator', function () {
    $category = KnowledgeBaseCategory::factory()->create();
    $article = KnowledgeBaseItem::factory()->for($category, 'category')->create([
        'public' => true,
        'public_id' => '0OIlAa19',
        'title' => 'Resetting your password',
    ]);

    getJson(route('api.portal.article.show-canonical', [
        'article' => "stale-title-{$article->public_id}",
    ]))
        ->assertOk()
        ->assertJsonPath('article.id', $article->getKey())
        ->assertJsonPath('article.publicId', $article->public_id)
        ->assertJsonPath('article.slug', 'resetting-your-password')
        ->assertJsonPath('category.publicId', $category->public_id);
});

it('returns the helpful vote percentage', function () {
    $article = KnowledgeBaseItem::factory()->create(['public' => true]);

    KnowledgeBaseArticleVote::factory()
        ->count(2)
        ->for($article, 'knowledgeBaseArticle')
        ->state(['is_helpful' => true])
        ->create();

    KnowledgeBaseArticleVote::factory()
        ->for($article, 'knowledgeBaseArticle')
        ->state(['is_helpful' => false])
        ->create();

    getJson(route('api.portal.article.show-canonical', [
        'article' => "article-{$article->public_id}",
    ]))
        ->assertOk()
        ->assertJsonPath('helpful_vote_percentage', 67);
});

it('rejects the canonical article API while the feature is inactive', function () {
    $article = KnowledgeBaseItem::factory()->create(['public' => true]);

    KnowledgeBasePortalStableUrlsFeature::deactivate();

    getJson(route('api.portal.article.show-canonical', [
        'article' => "{$article->title}-{$article->public_id}",
    ]))->assertNotFound();
});

it('rejects the legacy article API when the category does not exist', function () {
    $article = KnowledgeBaseItem::factory()->create(['public' => true]);

    getJson(route('api.portal.article.show', [
        'category' => 'missing-category',
        'article' => $article->getKey(),
    ]))->assertNotFound();
});

it('preserves the legacy article API while the feature is inactive', function () {
    $category = KnowledgeBaseCategory::factory()->create();
    $article = KnowledgeBaseItem::factory()->for($category, 'category')->create(['public' => true]);

    KnowledgeBasePortalStableUrlsFeature::deactivate();

    getJson(route('api.portal.article.show', [
        'category' => $category->slug,
        'article' => $article->getKey(),
    ]))
        ->assertOk()
        ->assertJsonPath('article.id', $article->getKey())
        ->assertJsonPath('article.publicId', null);
});
