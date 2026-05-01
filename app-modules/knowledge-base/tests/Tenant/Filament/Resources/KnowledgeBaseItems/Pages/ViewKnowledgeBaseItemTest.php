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
      of the licensor in the software. Any use of the licensor's trademarks is subject
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

use AidingApp\Contact\Models\Contact;
use AidingApp\KnowledgeBase\Filament\Resources\KnowledgeBaseItems\KnowledgeBaseItemResource;
use AidingApp\KnowledgeBase\Filament\Resources\KnowledgeBaseItems\Pages\ViewKnowledgeBaseItem;
use AidingApp\KnowledgeBase\Models\KnowledgeBaseItem;
use AidingApp\Portal\Models\KnowledgeBaseArticleVote;
use App\Models\User;
use App\Settings\LicenseSettings;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

test('ViewKnowledgeBaseItem is gated with proper access control', function () {
    $user = User::factory()->create();

    actingAs($user);

    $knowledgeBaseItem = KnowledgeBaseItem::factory()->create();

    get(
        KnowledgeBaseItemResource::getUrl('view', [
            'record' => $knowledgeBaseItem,
        ])
    )->assertForbidden();

    livewire(ViewKnowledgeBaseItem::class, [
        'record' => $knowledgeBaseItem->getRouteKey(),
    ])
        ->assertForbidden();

    $user->givePermissionTo('knowledge_base_item.view-any');
    $user->givePermissionTo('knowledge_base_item.*.view');

    get(
        KnowledgeBaseItemResource::getUrl('view', [
            'record' => $knowledgeBaseItem,
        ])
    )->assertSuccessful();
});

test('ViewKnowledgeBaseItem is gated with proper feature access control', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->knowledgeManagement = false;

    $settings->save();

    $user = User::factory()->create();

    actingAs($user);

    $user->givePermissionTo('knowledge_base_item.view-any');
    $user->givePermissionTo('knowledge_base_item.*.view');

    $knowledgeBaseItem = KnowledgeBaseItem::factory()->create();

    get(
        KnowledgeBaseItemResource::getUrl('view', [
            'record' => $knowledgeBaseItem,
        ])
    )->assertForbidden();

    livewire(ViewKnowledgeBaseItem::class, [
        'record' => $knowledgeBaseItem->getRouteKey(),
    ])
        ->assertForbidden();

    $settings->data->addons->knowledgeManagement = true;

    $settings->save();

    get(
        KnowledgeBaseItemResource::getUrl('view', [
            'record' => $knowledgeBaseItem,
        ])
    )->assertSuccessful();
});

test('rating displays Unrated when article has no votes', function () {
    asSuperAdmin();

    $knowledgeBaseItem = KnowledgeBaseItem::factory()->create();

    livewire(ViewKnowledgeBaseItem::class, ['record' => $knowledgeBaseItem->getRouteKey()])
        ->assertSeeText('Unrated');
});

test('rating displays correct percentage when article has votes', function () {
    asSuperAdmin();

    $knowledgeBaseItem = KnowledgeBaseItem::factory()->create();
    $contact = Contact::factory()->create();

    KnowledgeBaseArticleVote::factory()
        ->count(2)
        ->for($knowledgeBaseItem, 'knowledgeBaseArticle')
        ->state([
            'voter_id' => $contact->id,
            'voter_type' => $contact->getMorphClass(),
            'is_helpful' => true,
        ])
        ->create();

    KnowledgeBaseArticleVote::factory()
        ->for($knowledgeBaseItem, 'knowledgeBaseArticle')
        ->state([
            'voter_id' => $contact->id,
            'voter_type' => $contact->getMorphClass(),
            'is_helpful' => false,
        ])
        ->create();

    livewire(ViewKnowledgeBaseItem::class, ['record' => $knowledgeBaseItem->getRouteKey()])
        ->assertSeeText('67%');
});

test('rating displays 100 percent when all votes are helpful', function () {
    asSuperAdmin();

    $knowledgeBaseItem = KnowledgeBaseItem::factory()->create();
    $contact = Contact::factory()->create();

    KnowledgeBaseArticleVote::factory()
        ->count(3)
        ->for($knowledgeBaseItem, 'knowledgeBaseArticle')
        ->state([
            'voter_id' => $contact->id,
            'voter_type' => $contact->getMorphClass(),
            'is_helpful' => true,
        ])
        ->create();

    livewire(ViewKnowledgeBaseItem::class, ['record' => $knowledgeBaseItem->getRouteKey()])
        ->assertSeeText('100%');
});

test('rating displays 0 percent when no votes are helpful', function () {
    asSuperAdmin();

    $knowledgeBaseItem = KnowledgeBaseItem::factory()->create();
    $contact = Contact::factory()->create();

    KnowledgeBaseArticleVote::factory()
        ->count(3)
        ->for($knowledgeBaseItem, 'knowledgeBaseArticle')
        ->state([
            'voter_id' => $contact->id,
            'voter_type' => $contact->getMorphClass(),
            'is_helpful' => false,
        ])
        ->create();

    livewire(ViewKnowledgeBaseItem::class, ['record' => $knowledgeBaseItem->getRouteKey()])
        ->assertSeeText('0%');
});