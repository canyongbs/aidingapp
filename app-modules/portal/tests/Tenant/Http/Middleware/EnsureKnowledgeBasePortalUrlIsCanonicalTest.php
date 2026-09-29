<?php

use AidingApp\KnowledgeBase\Models\KnowledgeBaseCategory;
use AidingApp\KnowledgeBase\Models\KnowledgeBaseItem;
use AidingApp\Portal\Settings\PortalSettings;
use AidingApp\Portal\Support\KnowledgeBasePortalUrl;
use App\Features\KnowledgeBasePortalStableUrlsFeature;

use function Pest\Laravel\get;

use Symfony\Component\HttpFoundation\Response;

beforeEach(function () {
    $settings = app(PortalSettings::class);
    $settings->knowledge_management_portal_enabled = true;
    $settings->save();
});

it('redirects a legacy category URL to its canonical URL', function () {
    $category = KnowledgeBaseCategory::factory()->create();

    get(route('portal.category.show', ['category' => $category->slug]))
        ->assertStatus(Response::HTTP_MOVED_PERMANENTLY)
        ->assertRedirect(KnowledgeBasePortalUrl::category($category));
});

it('renders a canonical category URL without redirecting', function () {
    $category = KnowledgeBaseCategory::factory()->create();

    get(KnowledgeBasePortalUrl::category($category))
        ->assertOk();
});

it('redirects a stale category slug to its current canonical URL', function () {
    $category = KnowledgeBaseCategory::factory()->create(['slug' => 'old-slug']);
    $staleUrl = KnowledgeBasePortalUrl::category($category);

    $category->slug = 'current-slug';
    $category->save();

    get("{$staleUrl}?filter=featured")
        ->assertStatus(Response::HTTP_MOVED_PERMANENTLY)
        ->assertRedirect(KnowledgeBasePortalUrl::category($category) . '?filter=featured');
});

it('redirects a stale subcategory hierarchy to its current canonical URL', function () {
    $originalParent = KnowledgeBaseCategory::factory()->create();
    $currentParent = KnowledgeBaseCategory::factory()->create();
    $subcategory = KnowledgeBaseCategory::factory()->for($originalParent, 'parentCategory')->create();
    $staleUrl = KnowledgeBasePortalUrl::category($subcategory);

    $subcategory->parentCategory()->associate($currentParent);
    $subcategory->save();

    get($staleUrl)
        ->assertStatus(Response::HTTP_MOVED_PERMANENTLY)
        ->assertRedirect(KnowledgeBasePortalUrl::category($subcategory));
});

it('redirects a stale article title and category hierarchy to its canonical URL', function () {
    $originalCategory = KnowledgeBaseCategory::factory()->create();
    $currentParent = KnowledgeBaseCategory::factory()->create();
    $currentCategory = KnowledgeBaseCategory::factory()->for($currentParent, 'parentCategory')->create();
    $article = KnowledgeBaseItem::factory()->for($originalCategory, 'category')->create([
        'public' => true,
        'title' => 'Old title',
    ]);
    $staleUrl = KnowledgeBasePortalUrl::article($article);

    $article->title = 'Current title';
    $article->category()->associate($currentCategory);
    $article->save();

    get($staleUrl)
        ->assertStatus(Response::HTTP_MOVED_PERMANENTLY)
        ->assertRedirect(KnowledgeBasePortalUrl::article($article));
});

it('redirects a legacy article UUID URL to its canonical URL', function () {
    $category = KnowledgeBaseCategory::factory()->create();
    $article = KnowledgeBaseItem::factory()->for($category, 'category')->create(['public' => true]);

    get(route('portal.article.show', [
        'category' => $category->slug,
        'article' => $article->getKey(),
    ]))
        ->assertStatus(Response::HTTP_MOVED_PERMANENTLY)
        ->assertRedirect(KnowledgeBasePortalUrl::article($article));
});

it('renders legacy category URLs while the feature is inactive', function () {
    $category = KnowledgeBaseCategory::factory()->create();

    KnowledgeBasePortalStableUrlsFeature::deactivate();

    get(route('portal.category.show', ['category' => $category->slug]))
        ->assertOk();
});
