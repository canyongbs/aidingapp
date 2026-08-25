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

use AidingApp\ServiceManagement\Enums\SystemChangeRequestClassification;
use AidingApp\ServiceManagement\Filament\Resources\ChangeRequests\Pages\CreateChangeRequest;
use AidingApp\ServiceManagement\Models\ChangeRequest;
use AidingApp\ServiceManagement\Models\ChangeRequestStatus;
use AidingApp\ServiceManagement\Models\ChangeRequestType;
use Carbon\CarbonImmutable;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

test('CreateChangeRequest end_time field is disabled', function () {
    asSuperAdmin();

    livewire(CreateChangeRequest::class)
        ->assertSuccessful()
        ->assertFormFieldIsDisabled('end_time');
});

test('CreateChangeRequest calculates end_time from start_time and duration and saves successfully', function () {
    asSuperAdmin();

    ChangeRequestStatus::factory()->create([
        'classification' => SystemChangeRequestClassification::New,
    ]);

    $changeRequestType = ChangeRequestType::factory()->create();

    $startTime = '2026-08-20 11:05:00';
    $duration = 3;
    $expectedEndTime = CarbonImmutable::parse($startTime)->addMinutes($duration)->toDateTimeString();

    livewire(CreateChangeRequest::class)
        ->fillForm([
            'title' => 'Database Migration Rollout',
            'description' => 'Apply zero downtime schema update.',
            'change_request_type_id' => $changeRequestType->getKey(),
            'reason' => 'Release planned service-management fix.',
            'backout_strategy' => 'Rollback deployment and revert migration.',
            'impact' => 3,
            'likelihood' => 2,
            'start_time' => $startTime,
        ])
        ->fillForm([
            'duration' => $duration,
        ])
        ->assertFormSet([
            'end_time' => $expectedEndTime,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $changeRequest = ChangeRequest::query()
        ->where('title', 'Database Migration Rollout')
        ->where('change_request_type_id', $changeRequestType->getKey())
        ->latest('id')
        ->firstOrFail();

    assertDatabaseHas(ChangeRequest::class, [
        'id' => $changeRequest?->getKey(),
        'title' => 'Database Migration Rollout',
        'change_request_type_id' => $changeRequestType->getKey(),
    ]);
});
