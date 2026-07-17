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
use AidingApp\KnowledgeBase\Models\KnowledgeBaseStatus;
use AidingApp\Report\Filament\Widgets\KnowledgeBaseArticlesTable;
use App\Models\User;
use Filament\Actions\ExportAction;
use Illuminate\Support\Facades\Storage;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

it('returns all knowledge base articles without filters', function () {
    $status = KnowledgeBaseStatus::factory()->create();
    $category = KnowledgeBaseCategory::factory()->create();

    $articles = KnowledgeBaseItem::factory()->count(3)->state([
        'status_id' => $status->id,
        'category_id' => $category->id,
    ])->create();

    livewire(KnowledgeBaseArticlesTable::class, [
        'cacheTag' => 'test-kb-articles-table',
        'pageFilters' => [],
    ])
        ->assertCanSeeTableRecords($articles);
});

it('filters articles by category from page filter', function () {
    $status = KnowledgeBaseStatus::factory()->create();
    $categoryA = KnowledgeBaseCategory::factory()->create();
    $categoryB = KnowledgeBaseCategory::factory()->create();

    $articlesA = KnowledgeBaseItem::factory()->count(2)->state([
        'status_id' => $status->id,
        'category_id' => $categoryA->id,
    ])->create();

    $articlesB = KnowledgeBaseItem::factory()->count(3)->state([
        'status_id' => $status->id,
        'category_id' => $categoryB->id,
    ])->create();

    livewire(KnowledgeBaseArticlesTable::class, [
        'cacheTag' => 'test-kb-articles-table-filtered',
        'pageFilters' => [
            'categories' => [$categoryA->id],
        ],
    ])
        ->assertCanSeeTableRecords($articlesA)
        ->assertCanNotSeeTableRecords($articlesB);
});

it('has an export action', function () {
    livewire(KnowledgeBaseArticlesTable::class, [
        'cacheTag' => 'test-kb-articles-table-export',
        'pageFilters' => [],
    ])->assertTableActionExists(ExportAction::class);
});

it('can start an export and send a notification', function () {
    Storage::fake('s3');

    actingAs(User::factory()->create());

    livewire(KnowledgeBaseArticlesTable::class, [
        'cacheTag' => 'test-kb-articles-table-export-notification',
        'pageFilters' => [],
    ])
        ->callTableAction(ExportAction::class)
        ->assertNotified();
});

it('filters records by status using the table-level status filter', function () {
    $statusA = KnowledgeBaseStatus::factory()->create(['name' => 'Published']);
    $statusB = KnowledgeBaseStatus::factory()->create(['name' => 'Draft']);
    $category = KnowledgeBaseCategory::factory()->create();

    $publishedArticle = KnowledgeBaseItem::factory()->state([
        'status_id' => $statusA->id,
        'category_id' => $category->id,
    ])->create();

    $draftArticle = KnowledgeBaseItem::factory()->state([
        'status_id' => $statusB->id,
        'category_id' => $category->id,
    ])->create();

    livewire(KnowledgeBaseArticlesTable::class, [
        'cacheTag' => 'test-kb-articles-table-status-filter',
        'pageFilters' => [],
    ])
        ->filterTable('status', $statusA->id)
        ->assertCanSeeTableRecords(collect([$publishedArticle]))
        ->assertCanNotSeeTableRecords(collect([$draftArticle]))
        ->filterTable('status', $statusB->id)
        ->assertCanSeeTableRecords(collect([$draftArticle]))
        ->assertCanNotSeeTableRecords(collect([$publishedArticle]));
});

it('filters records by category using the table-level category filter', function () {
    $status = KnowledgeBaseStatus::factory()->create();
    $categoryA = KnowledgeBaseCategory::factory()->create();
    $categoryB = KnowledgeBaseCategory::factory()->create();

    $articleA = KnowledgeBaseItem::factory()->state([
        'status_id' => $status->id,
        'category_id' => $categoryA->id,
    ])->create();

    $articleB = KnowledgeBaseItem::factory()->state([
        'status_id' => $status->id,
        'category_id' => $categoryB->id,
    ])->create();

    livewire(KnowledgeBaseArticlesTable::class, [
        'cacheTag' => 'test-kb-articles-table-category-filter',
        'pageFilters' => [],
    ])
        ->filterTable('category', $categoryA->id)
        ->assertCanSeeTableRecords(collect([$articleA]))
        ->assertCanNotSeeTableRecords(collect([$articleB]))
        ->filterTable('category', $categoryB->id)
        ->assertCanSeeTableRecords(collect([$articleB]))
        ->assertCanNotSeeTableRecords(collect([$articleA]));
});

it('filters records by public using the table-level public filter', function () {
    $status = KnowledgeBaseStatus::factory()->create();
    $category = KnowledgeBaseCategory::factory()->create();

    $publicArticle = KnowledgeBaseItem::factory()->state([
        'status_id' => $status->id,
        'category_id' => $category->id,
        'public' => true,
    ])->create();

    $privateArticle = KnowledgeBaseItem::factory()->state([
        'status_id' => $status->id,
        'category_id' => $category->id,
        'public' => false,
    ])->create();

    livewire(KnowledgeBaseArticlesTable::class, [
        'cacheTag' => 'test-kb-articles-table-public-filter',
        'pageFilters' => [],
    ])
        ->filterTable('public', true)
        ->assertCanSeeTableRecords(collect([$publicArticle]))
        ->assertCanNotSeeTableRecords(collect([$privateArticle]))
        ->filterTable('public', false)
        ->assertCanSeeTableRecords(collect([$privateArticle]))
        ->assertCanNotSeeTableRecords(collect([$publicArticle]));
});

it('filters records by created_at using the table-level created after filter', function () {
    $status = KnowledgeBaseStatus::factory()->create();
    $category = KnowledgeBaseCategory::factory()->create();

    $recentArticle = KnowledgeBaseItem::factory()->state([
        'status_id' => $status->id,
        'category_id' => $category->id,
        'created_at' => now(),
    ])->create();

    $oldArticle = KnowledgeBaseItem::factory()->state([
        'status_id' => $status->id,
        'category_id' => $category->id,
        'created_at' => now()->subDays(10),
    ])->create();

    livewire(KnowledgeBaseArticlesTable::class, [
        'cacheTag' => 'test-kb-articles-table-created-at-filter',
        'pageFilters' => [],
    ])
        ->filterTable('created_at', ['created_after' => now()->subDays(5)->toDateString()])
        ->assertCanSeeTableRecords(collect([$recentArticle]))
        ->assertCanNotSeeTableRecords(collect([$oldArticle]));
});

it('filters records by updated_at using the table-level updated after filter', function () {
    $status = KnowledgeBaseStatus::factory()->create();
    $category = KnowledgeBaseCategory::factory()->create();

    $recentlyUpdatedArticle = KnowledgeBaseItem::factory()->state([
        'status_id' => $status->id,
        'category_id' => $category->id,
        'updated_at' => now(),
    ])->create();

    $staleArticle = KnowledgeBaseItem::factory()->state([
        'status_id' => $status->id,
        'category_id' => $category->id,
        'created_at' => now()->subDays(10),
        'updated_at' => now()->subDays(10),
    ])->create();

    livewire(KnowledgeBaseArticlesTable::class, [
        'cacheTag' => 'test-kb-articles-table-updated-at-filter',
        'pageFilters' => [],
    ])
        ->filterTable('updated_at', ['updated_after' => now()->subDays(5)->toDateString()])
        ->assertCanSeeTableRecords(collect([$recentlyUpdatedArticle]))
        ->assertCanNotSeeTableRecords(collect([$staleArticle]));
});
