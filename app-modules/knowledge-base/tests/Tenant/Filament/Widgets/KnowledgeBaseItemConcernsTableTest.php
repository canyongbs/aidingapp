<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

    Aiding App™ is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Aiding App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    <https://www.canyongbs.com> or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

use AidingApp\KnowledgeBase\Enums\ConcernStatus;
use AidingApp\KnowledgeBase\Filament\Widgets\KnowledgeBaseItemConcernsTable;
use AidingApp\KnowledgeBase\Models\KnowledgeBaseItem;
use AidingApp\KnowledgeBase\Models\KnowledgeBaseItemConcern;

use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

it('returns all new concerns for a given knowledge base item by default', function () {
    asSuperAdmin();

    $knowledgeBaseItem = KnowledgeBaseItem::factory()->create();

    $newConcerns = KnowledgeBaseItemConcern::factory()
        ->count(3)
        ->state(['status' => ConcernStatus::New])
        ->for($knowledgeBaseItem, 'knowledgeBaseItem')
        ->create();

    $archivedConcerns = KnowledgeBaseItemConcern::factory()
        ->count(3)
        ->state(['status' => ConcernStatus::Archived])
        ->for($knowledgeBaseItem, 'knowledgeBaseItem')
        ->create();

    $resolvedConcerns = KnowledgeBaseItemConcern::factory()
        ->count(3)
        ->state(['status' => ConcernStatus::Resolved])
        ->for($knowledgeBaseItem, 'knowledgeBaseItem')
        ->create();

    livewire(KnowledgeBaseItemConcernsTable::class, ['record' => $knowledgeBaseItem])
        ->assertCanSeeTableRecords($newConcerns)
        ->assertCanNotSeeTableRecords($archivedConcerns->merge($resolvedConcerns));
});

it('can filter concerns by status', function () {
    asSuperAdmin();

    $knowledgeBaseItem = KnowledgeBaseItem::factory()->create();

    $newConcerns = KnowledgeBaseItemConcern::factory()
        ->count(3)
        ->state(['status' => ConcernStatus::New])
        ->for($knowledgeBaseItem, 'knowledgeBaseItem')
        ->create();

    $archivedConcerns = KnowledgeBaseItemConcern::factory()
        ->count(3)
        ->state(['status' => ConcernStatus::Archived])
        ->for($knowledgeBaseItem, 'knowledgeBaseItem')
        ->create();

    $resolvedConcerns = KnowledgeBaseItemConcern::factory()
        ->count(3)
        ->state(['status' => ConcernStatus::Resolved])
        ->for($knowledgeBaseItem, 'knowledgeBaseItem')
        ->create();

    livewire(KnowledgeBaseItemConcernsTable::class, ['record' => $knowledgeBaseItem])
        ->filterTable('status', ConcernStatus::Archived->value)
        ->assertCanSeeTableRecords($archivedConcerns)
        ->assertCanNotSeeTableRecords($newConcerns->merge($resolvedConcerns))
        ->filterTable('status', ConcernStatus::Resolved->value)
        ->assertCanSeeTableRecords($resolvedConcerns)
        ->assertCanNotSeeTableRecords($newConcerns->merge($archivedConcerns));
});

it('can change the status of a concern properly', function () {
    asSuperAdmin();

    $concern = KnowledgeBaseItemConcern::factory()->create(['status' => ConcernStatus::New]);

    livewire(KnowledgeBaseItemConcernsTable::class, ['record' => $concern->knowledgeBaseItem])
        ->callTableAction('changeConcernStatus', $concern->getKey(), ['status' => ConcernStatus::Resolved])
        ->assertHasNoErrors();

    expect($concern->refresh()->status)->toBe(ConcernStatus::Resolved);
});
