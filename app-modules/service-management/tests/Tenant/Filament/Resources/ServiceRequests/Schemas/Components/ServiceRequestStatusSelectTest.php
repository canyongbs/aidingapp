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

use AidingApp\ServiceManagement\Enums\SystemServiceRequestClassification;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequests\Schemas\Components\ServiceRequestStatusSelect;
use AidingApp\ServiceManagement\Models\ServiceRequestStatus;
use Filament\Forms\Components\Select;

/**
 * The select groups its options by classification label, so the status ids are the keys of
 * each group rather than of the option array itself.
 *
 * @return array<int, string>
 */
function optionIdsFor(Select $select): array
{
    return collect($select->getOptions())
        ->flatMap(fn (mixed $group): array => collect($group)->keys()->all())
        ->all();
}

it('builds a status_id select with no default by default', function () {
    $select = ServiceRequestStatusSelect::make();

    expect($select)->toBeInstanceOf(Select::class)
        ->and($select->getName())->toBe('status_id')
        ->and($select->getDefaultState())->toBeNull();
});

it('builds a select for a custom field name', function () {
    ServiceRequestStatus::factory()->create([
        'classification' => SystemServiceRequestClassification::Open,
    ]);

    $select = ServiceRequestStatusSelect::make('automated_status_id');

    expect($select->getName())->toBe('automated_status_id');
});

it('applies a chained default when provided', function () {
    $status = ServiceRequestStatus::factory()->create([
        'classification' => SystemServiceRequestClassification::Open,
    ]);

    $select = ServiceRequestStatusSelect::make()->default($status->getKey());

    expect($select->getDefaultState())->toBe($status->getKey());
});

describe('archiving', function () {
    it('does not offer archived service request statuses', function () {
        $active = ServiceRequestStatus::factory()->create([
            'classification' => SystemServiceRequestClassification::Open,
        ]);
        $archived = ServiceRequestStatus::factory()->archived()->create([
            'classification' => SystemServiceRequestClassification::Open,
        ]);

        $optionIds = optionIdsFor(ServiceRequestStatusSelect::make());

        expect($optionIds)->toContain($active->getKey())
            ->and($optionIds)->not->toContain($archived->getKey());
    });

    it('offers the currently selected status even when it is archived', function () {
        $archived = ServiceRequestStatus::factory()->archived()->create([
            'classification' => SystemServiceRequestClassification::Open,
        ]);

        $optionIds = optionIdsFor(ServiceRequestStatusSelect::make(selectedId: $archived->getKey()));

        expect($optionIds)->toContain($archived->getKey());
    });

    it('does not offer an archived status other than the selected one', function () {
        $selected = ServiceRequestStatus::factory()->archived()->create([
            'classification' => SystemServiceRequestClassification::Open,
        ]);
        $otherArchived = ServiceRequestStatus::factory()->archived()->create([
            'classification' => SystemServiceRequestClassification::Open,
        ]);

        $optionIds = optionIdsFor(ServiceRequestStatusSelect::make(selectedId: $selected->getKey()));

        expect($optionIds)->toContain($selected->getKey())
            ->and($optionIds)->not->toContain($otherArchived->getKey());
    });
});

describe('soft deleted statuses', function () {
    it('does not offer soft deleted service request statuses', function () {
        $active = ServiceRequestStatus::factory()->create([
            'classification' => SystemServiceRequestClassification::Open,
        ]);
        $trashed = ServiceRequestStatus::factory()->create([
            'classification' => SystemServiceRequestClassification::Open,
        ]);
        $trashed->delete();

        $optionIds = optionIdsFor(ServiceRequestStatusSelect::make());

        expect($optionIds)->toContain($active->getKey())
            ->and($optionIds)->not->toContain($trashed->getKey());
    });

    it('offers the currently selected status even when it is soft deleted', function () {
        $trashed = ServiceRequestStatus::factory()->create([
            'classification' => SystemServiceRequestClassification::Open,
        ]);
        $trashed->delete();

        $optionIds = optionIdsFor(ServiceRequestStatusSelect::make(selectedId: $trashed->getKey()));

        expect($optionIds)->toContain($trashed->getKey());
    });
});
