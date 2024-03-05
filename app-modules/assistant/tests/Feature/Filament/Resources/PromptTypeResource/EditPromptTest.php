<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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

use function Pest\Laravel\get;

use Filament\Actions\DeleteAction;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

use AidingApp\Assistant\Models\PromptType;

use function Pest\Laravel\assertDatabaseHas;
use function PHPUnit\Framework\assertNotNull;

use AidingApp\Authorization\Enums\LicenseType;

use function Pest\Laravel\assertDatabaseCount;

use AidingApp\Assistant\Filament\Resources\PromptTypeResource;
use AidingApp\Assistant\Filament\Resources\PromptTypeResource\Pages\EditPromptType;

/** @var array<LicenseType> $licenses */
$licenses = [
    LicenseType::ConversationalAi,
];

$roles = [
    'assistant.assistant_prompt_management',
];

it('cannot render without a license', function () use ($roles) {
    actingAs(user(
        roles: $roles
    ));

    $record = PromptType::factory()->create();

    get(PromptTypeResource::getUrl('edit', [
        'record' => $record->getRouteKey(),
    ]))
        ->assertForbidden();
});

it('cannot render without permissions', function () use ($licenses) {
    actingAs(user(
        licenses: $licenses,
    ));

    $record = PromptType::factory()->create();

    get(PromptTypeResource::getUrl('edit', [
        'record' => $record->getRouteKey(),
    ]))
        ->assertForbidden();
});

it('can render', function () use ($licenses, $roles) {
    actingAs(user(
        licenses: $licenses,
        roles: $roles
    ));

    $record = PromptType::factory()->create();

    get(PromptTypeResource::getUrl('edit', [
        'record' => $record->getRouteKey(),
    ]))
        ->assertSuccessful();
});

it('can edit a record', function () use ($licenses, $roles) {
    actingAs(user(
        licenses: $licenses,
        roles: $roles
    ));

    $record = PromptType::factory()->make();

    livewire(EditPromptType::class, [
        'record' => PromptType::factory()->create()->getRouteKey(),
    ])
        ->assertSuccessful()
        ->fillForm($record->toArray())
        ->call('save')
        ->assertHasNoFormErrors();

    assertDatabaseCount(PromptType::class, 1);

    assertDatabaseHas(PromptType::class, $record->toArray());
});

it('can delete a record', function () use ($licenses, $roles) {
    actingAs(user(
        licenses: $licenses,
        roles: $roles
    ));

    $record = PromptType::factory()->create();

    assertDatabaseCount(PromptType::class, 1);

    assertDatabaseHas(PromptType::class, $record->toArray());

    livewire(EditPromptType::class, [
        'record' => $record->getRouteKey(),
    ])
        ->assertSuccessful()
        ->assertActionExists(DeleteAction::class)
        ->assertActionEnabled(DeleteAction::class)
        ->callAction(DeleteAction::class);

    // TODO: Bring back when we propagate soft deletes
    //assertDatabaseCount(PromptType::class, 0);

    assertNotNull($record->refresh()->deleted_at);
});
