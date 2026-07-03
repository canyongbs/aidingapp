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
use AidingApp\Report\Filament\Widgets\KnowledgeBaseStats;

it('returns correct knowledge base statistics without filters', function () {
    $status = KnowledgeBaseStatus::factory()->create();
    $category = KnowledgeBaseCategory::factory()->create();

    KnowledgeBaseItem::factory()->count(3)->state([
        'status_id' => $status->id,
        'category_id' => $category->id,
        'portal_view_count' => 10,
    ])->create();

    $widget = new KnowledgeBaseStats();
    $widget->cacheTag = 'test-kb-stats';
    $widget->pageFilters = [];

    $stats = $widget->getStats();

    expect($stats)->toHaveCount(3)
        ->and($stats[0]->getValue())->toEqual('3')
        ->and($stats[2]->getValue())->toEqual('30');
});

it('returns correct knowledge base statistics with category filter', function () {
    $status = KnowledgeBaseStatus::factory()->create();
    $categoryA = KnowledgeBaseCategory::factory()->create();
    $categoryB = KnowledgeBaseCategory::factory()->create();

    KnowledgeBaseItem::factory()->count(2)->state([
        'status_id' => $status->id,
        'category_id' => $categoryA->id,
        'portal_view_count' => 5,
    ])->create();

    KnowledgeBaseItem::factory()->count(3)->state([
        'status_id' => $status->id,
        'category_id' => $categoryB->id,
        'portal_view_count' => 10,
    ])->create();

    $widget = new KnowledgeBaseStats();
    $widget->cacheTag = 'test-kb-stats-filtered';
    $widget->pageFilters = [
        'categories' => [$categoryA->id],
    ];

    $stats = $widget->getStats();

    expect($stats)->toHaveCount(3)
        ->and($stats[0]->getValue())->toEqual('2')
        ->and($stats[2]->getValue())->toEqual('10');
});
