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

namespace AidingApp\Contact\Filament\Actions;

use AidingApp\Contact\Models\Contact;
use AidingApp\Contact\Models\ContactType;
use Filament\Actions\BulkAction;
use Filament\Forms\Components\Select;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class ChangeTypeBulkAction
{
    public static function make(): BulkAction
    {
        return BulkAction::make('changeType')
            ->label('Change Type')
            ->icon(Heroicon::OutlinedTag)
            ->slideOver()
            ->modalHeading('Change Contact Type')
            ->modalDescription(fn (Collection $records): string => "You have selected {$records->count()} contacts to change the type of.")
            ->modalSubmitActionLabel('Change Type')
            ->schema([
                Select::make('type_id')
                    ->label('Type')
                    ->options(fn (): array => ContactType::query()->orderBy('name')->pluck('name', 'id')->all())
                    ->searchable()
                    ->required()
                    ->exists((new ContactType())->getTable(), 'id'),
            ])
            ->authorize('updateAny')
            ->authorizeIndividualRecords('update')
            ->action(function (Collection $records, array $data): void {
                $type = ContactType::query()->findOrFail($data['type_id']);

                DB::transaction(function () use ($records, $type): void {
                    foreach ($records as $record) {
                        assert($record instanceof Contact);

                        $record->type()->associate($type);
                        $record->save();
                    }
                });
            })
            ->successNotificationTitle('Changed the contact type')
            ->failureNotificationTitle(fn (int $successCount, int $totalCount): string => $successCount
                ? "Changed the type of {$successCount} of {$totalCount} contacts"
                : 'Failed to change the contact type')
            ->deselectRecordsAfterCompletion();
    }
}
