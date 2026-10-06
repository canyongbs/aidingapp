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
use AidingApp\Portal\Settings\PortalSettings;
use AidingApp\Portal\Support\KnowledgeBasePortalUrl;

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
