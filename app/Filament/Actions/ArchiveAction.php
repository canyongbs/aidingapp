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

namespace App\Filament\Actions;

use Exception;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component;

class ArchiveAction
{
    public static function make(): Action
    {
        return Action::make('archive')
            ->label('Archive')
            ->modalHeading(fn (Action $action): string => "Archive {$action->getRecordTitle()}")
            ->modalSubmitActionLabel('Archive')
            ->successNotificationTitle('Archived')
            ->defaultColor('warning')
            ->groupedIcon('heroicon-m-archive-box')
            ->modalIcon('heroicon-o-archive-box')
            ->hidden(static function (Model $record): bool {
                if (! method_exists($record, 'isArchived')) {
                    throw new Exception('The [ArchiveAction] requires the model to use the [CanBeArchived] trait.');
                }

                return $record->isArchived();
            })
            ->requiresConfirmation()
            ->action(function (Action $action, Model $record): void {
                if (! method_exists($record, 'archive')) {
                    throw new Exception('The [ArchiveAction] requires the model to use the [CanBeArchived] trait.');
                }

                $result = $record->archive();

                if (! $result) {
                    $action->failure();

                    return;
                }

                $action->success();
            })
            ->authorize(function (Model $record, Component $livewire): bool {
                if ((! $livewire instanceof EditRecord) && (! $livewire instanceof ViewRecord)) {
                    throw new Exception('Unsupported Livewire component for [ArchiveAction] authorization. It must be used within [EditRecord] or [ViewRecord], or a custom [authorize()] function must be used.');
                }

                return $livewire::getResource()::can('archive', $record);
            })
            ->successRedirectUrl(function (Component $livewire): string {
                if ((! $livewire instanceof EditRecord) && (! $livewire instanceof ViewRecord)) {
                    throw new Exception('Unsupported Livewire component for [ArchiveAction] redirect. It must be used within [EditRecord] or [ViewRecord], or a custom [successRedirectUrl()] function must be used.');
                }

                return $livewire::getResource()::getUrl('index');
            });
    }
}
