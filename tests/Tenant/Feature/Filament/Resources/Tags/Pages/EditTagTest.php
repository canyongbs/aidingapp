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

use AidingApp\KnowledgeBase\Models\KnowledgeBaseItem;
use App\Filament\Resources\Tags\Pages\EditTag;
use App\Models\Tag;
use Livewire\Livewire;

use function Pest\Laravel\assertDatabaseHas;
use function Tests\asSuperAdmin;

use Tests\Tenant\Feature\Filament\Resources\Tags\RequestFactories\EditTagRequestFactory;

it('displays the tag data', function (): void {
    asSuperAdmin();
    $tag = Tag::factory()->forClass(new KnowledgeBaseItem())->create();

    Livewire::test(EditTag::class, ['record' => $tag->getRouteKey()])
        ->assertSchemaStateSet([
            'name' => $tag->name,
        ]);
});

it('can update a tag', function (): void {
    asSuperAdmin();
    $tag = Tag::factory()->forClass(new KnowledgeBaseItem())->create();

    $request = EditTagRequestFactory::new()->create();

    Livewire::test(EditTag::class, ['record' => $tag->getRouteKey()])
        ->fillForm($request)
        ->call('save')
        ->assertHasNoFormErrors();

    assertDatabaseHas(Tag::class, [
        'id' => $tag->getKey(),
        ...$request,
    ]);
});

it('allows saving with a different case of the current tag name', function (): void {
    asSuperAdmin();
    $tag = Tag::factory()->forClass(new KnowledgeBaseItem())->create(['name' => 'Billing']);

    Livewire::test(EditTag::class, ['record' => $tag->getRouteKey()])
        ->fillForm(['name' => 'BILLING'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($tag->refresh()->name)->toBe('BILLING');
});

it('allows saving with the name of a tag of a different type', function (): void {
    asSuperAdmin();

    Tag::factory()->create(['name' => 'Billing', 'type' => 'other_type']);
    $tag = Tag::factory()->forClass(new KnowledgeBaseItem())->create(['name' => 'Shipping']);

    Livewire::test(EditTag::class, ['record' => $tag->getRouteKey()])
        ->fillForm(['name' => 'BILLING'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($tag->refresh()->name)->toBe('BILLING');
});

it('validates the inputs', function (EditTagRequestFactory $data, array $errors): void {
    asSuperAdmin();

    Tag::factory()->forClass(new KnowledgeBaseItem())->create(['name' => 'Billing']);
    $tag = Tag::factory()->forClass(new KnowledgeBaseItem())->create();

    Livewire::test(EditTag::class, ['record' => $tag->getRouteKey()])
        ->fillForm($data->create())
        ->call('save')
        ->assertHasFormErrors($errors);
})->with([
    'name required' => [EditTagRequestFactory::new()->state(['name' => null]), ['name' => 'required']],
    'name unique' => [EditTagRequestFactory::new()->state(['name' => 'Billing']), ['name' => 'unique']],
    'name unique case insensitive' => [EditTagRequestFactory::new()->state(['name' => 'BILLING']), ['name' => 'unique']],
]);
