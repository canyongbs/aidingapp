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

use AidingApp\ServiceManagement\Filament\Resources\ServiceRequests\Schemas\Components\ServiceRequestPriorityToggleButtons;
use AidingApp\ServiceManagement\Models\ServiceRequestPriority;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Exists;

it('builds a priority_id toggle buttons field with no default by default', function () {
    $toggleButtons = ServiceRequestPriorityToggleButtons::make(ServiceRequestType::factory()->create()->getKey());

    expect($toggleButtons->getName())->toBe('priority_id')
        ->and($toggleButtons->getDefaultState())->toBeNull()
        ->and($toggleButtons->isInline())->toBeTrue();
});

it('builds a toggle buttons field for a custom field name', function () {
    $toggleButtons = ServiceRequestPriorityToggleButtons::make(ServiceRequestType::factory()->create()->getKey(), 'automated_priority_id');

    expect($toggleButtons->getName())->toBe('automated_priority_id');
});

it('scopes options to priorities belonging to the given type', function () {
    $type = ServiceRequestType::factory()->create();

    $priority = ServiceRequestPriority::factory()->create(['type_id' => $type->getKey()]);

    ServiceRequestPriority::factory()->create([
        'type_id' => ServiceRequestType::factory()->create()->getKey(),
    ]);

    $toggleButtons = ServiceRequestPriorityToggleButtons::make($type->getKey());

    expect($toggleButtons->getOptions())
        ->toHaveKey($priority->getKey(), $priority->name)
        ->toHaveCount(1);
});

it('has no options when no type is given', function () {
    $toggleButtons = ServiceRequestPriorityToggleButtons::make('');

    expect($toggleButtons->getOptions())->toBeEmpty();
});

it('constrains the exists validation rule to priorities belonging to the given type', function () {
    $type = ServiceRequestType::factory()->create();

    $matchingPriority = ServiceRequestPriority::factory()->create(['type_id' => $type->getKey()]);

    $otherTypePriority = ServiceRequestPriority::factory()->create([
        'type_id' => ServiceRequestType::factory()->create()->getKey(),
    ]);

    $toggleButtons = ServiceRequestPriorityToggleButtons::make($type->getKey());
    $toggleButtons->container(Schema::make());

    $existsRule = collect($toggleButtons->getValidationRules())
        ->first(fn (mixed $rule): bool => $rule instanceof Exists);

    expect($existsRule)->not->toBeNull();

    expect(
        Validator::make(
            ['priority_id' => $matchingPriority->getKey()],
            ['priority_id' => [$existsRule]],
        )->passes()
    )->toBeTrue();

    expect(
        Validator::make(
            ['priority_id' => $otherTypePriority->getKey()],
            ['priority_id' => [$existsRule]],
        )->passes()
    )->toBeFalse();
});
