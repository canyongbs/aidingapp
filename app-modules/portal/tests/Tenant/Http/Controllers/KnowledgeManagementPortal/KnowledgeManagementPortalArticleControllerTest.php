<?php

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
