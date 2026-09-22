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

namespace AidingApp\ServiceManagement\Filament\Resources\ServiceRequestStatuses\Actions;

use AidingApp\ServiceManagement\Filament\Resources\ServiceRequestStatuses\ServiceRequestStatusResource;
use AidingApp\ServiceManagement\Models\ServiceRequestStatus;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;

/**
 * The counterpart to `CanyonGBS\Common\Filament\Actions\ArchiveAction`, which Common does
 * not ship. Archiving is the only way to retire a status now that it cannot be deleted, so
 * without this the action would be a one way door.
 */
class UnarchiveServiceRequestStatusAction
{
    public static function make(): Action
    {
        return Action::make('unarchive')
            ->label('Unarchive')
            ->icon(Heroicon::ArchiveBoxArrowDown)
            ->color('warning')
            ->requiresConfirmation()
            ->modalHeading(fn (ServiceRequestStatus $record): string => "Unarchive {$record->name}")
            ->modalIcon(Heroicon::OutlinedArchiveBoxArrowDown)
            ->visible(fn (ServiceRequestStatus $record): bool => $record->isArchived())
            ->authorize(fn (ServiceRequestStatus $record): bool => ServiceRequestStatusResource::can('restore', $record))
            ->action(fn (ServiceRequestStatus $record) => $record->unarchive())
            ->successNotificationTitle('Unarchived');
    }
}
