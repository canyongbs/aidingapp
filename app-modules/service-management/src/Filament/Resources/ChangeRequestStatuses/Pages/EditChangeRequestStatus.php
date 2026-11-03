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

namespace AidingApp\ServiceManagement\Filament\Resources\ChangeRequestStatuses\Pages;

use AidingApp\ServiceManagement\Enums\SystemChangeRequestClassification;
use AidingApp\ServiceManagement\Filament\Resources\ChangeRequestStatuses\ChangeRequestStatusResource;
use AidingApp\ServiceManagement\Models\ChangeRequestStatus;
use App\Features\ChangeRequestTypeAndStatusNameUniquenessFeature;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rules\Unique;

class EditChangeRequestStatus extends EditRecord
{
    protected static string $resource = ChangeRequestStatusResource::class;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->columns()
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->string()
                            ->maxLength(255)
                            // TODO: Cleanup Task (change-request-type-and-status-name-uniqueness): replace this `when()` with `->unique(ignoreRecord: true, modifyRuleUsing: fn (Unique $rule): Unique => $rule->withoutTrashed())` once the flag is removed.
                            ->when(
                                ChangeRequestTypeAndStatusNameUniquenessFeature::active(),
                                fn (TextInput $field): TextInput => $field->unique(ignoreRecord: true, modifyRuleUsing: fn (Unique $rule): Unique => $rule->withoutTrashed()),
                            ),
                        Select::make('classification')
                            ->searchable()
                            ->options(SystemChangeRequestClassification::class)
                            ->required()
                            ->enum(SystemChangeRequestClassification::class),
                    ]),
            ])->disabled(fn (ChangeRequestStatus $record) => $record->trashed());
    }

    protected function getSaveFormAction(): Action
    {
        return parent::getSaveFormAction()
            ->hidden(fn (ChangeRequestStatus $record) => $record->trashed());
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            RestoreAction::make()
                // TODO: Cleanup Task (change-request-type-and-status-name-uniqueness): remove the feature flag check from this `before()` hook once the flag is removed.
                ->before(function (RestoreAction $action, ChangeRequestStatus $record): void {
                    if (! ChangeRequestTypeAndStatusNameUniquenessFeature::active()) {
                        return;
                    }

                    if (ChangeRequestStatus::query()->where('name', $record->name)->exists()) {
                        Notification::make()
                            ->title('Unable to restore this change request status')
                            ->body('Another change request status is already using this name. Rename or delete it before restoring this one.')
                            ->danger()
                            ->send();

                        $action->halt();
                    }
                }),
            ForceDeleteAction::make(),
        ];
    }
}
