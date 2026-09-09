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

namespace AidingApp\ServiceManagement\Filament\Resources\Advisories\RelationManagers;

use AidingApp\ServiceManagement\Enums\SystemAdvisoryStatusClassification;
use AidingApp\ServiceManagement\Models\Advisory;
use AidingApp\ServiceManagement\Models\AdvisoryStatus;
use AidingApp\ServiceManagement\Models\AdvisoryUpdate;
use App\Features\AdvisoryUpdateTitleAndDateFeature;
use App\Filament\Tables\Columns\IdColumn;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Arr;

class AdvisoryUpdatesRelationManager extends RelationManager
{
    protected static string $relationship = 'advisoryUpdates';

    public function form(Schema $schema): Schema
    {
        assert($this->getOwnerRecord() instanceof Advisory);

        return $schema
            ->components([
                static::getPropertiesSectionSchema(),
                ToggleButtons::make('status_id')
                    ->label('Status')
                    ->inline()
                    ->options(fn (): array => static::getStatusOptions($this->getOwnerRecord()))
                    ->default($this->getOwnerRecord()->status->getKey())
                    ->exists((new AdvisoryStatus())->getTable(), 'id')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        assert($this->getOwnerRecord() instanceof Advisory);

        return $table
            ->columns([
                IdColumn::make(),
                ...(AdvisoryUpdateTitleAndDateFeature::active()
                    ? [
                        TextColumn::make('title')
                            ->label('Title')
                            ->description(fn (AdvisoryUpdate $record): string => $record->update)
                            ->searchable()
                            ->color('primary')
                            ->action(static::getViewOrEditAdvisoryUpdateAction()),
                    ]
                    : [
                        TextColumn::make('update')
                            ->label('Update')
                            ->words(6),
                    ]),
                IconColumn::make('internal')
                    ->boolean(),
                ...(AdvisoryUpdateTitleAndDateFeature::active()
                    ? [
                        TextColumn::make('date')
                            ->label('Date')
                            ->dateTime()
                            ->sortable(),
                    ]
                    : [
                        TextColumn::make('created_at')
                            ->sortable(),
                        TextColumn::make('updated_at')
                            ->sortable(),
                    ]),
            ])
            ->defaultSort(AdvisoryUpdateTitleAndDateFeature::active() ? 'date' : 'created_at', 'desc')
            ->headerActions([
                CreateAction::make()
                    ->visible($this->getOwnerRecord()->status->classification === SystemAdvisoryStatusClassification::Resolved ? false : true)
                    ->after(function (array $data, AdvisoryUpdate $advisoryUpdate) {
                        $advisoryUpdate->advisory->update(['status_id' => $data['status_id']]);
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->authorizeIndividualRecords('delete'),
                ]),
            ]);
    }

    private static function getViewOrEditAdvisoryUpdateAction(): Action
    {
        return Action::make('viewOrEditAdvisoryUpdate')
            ->authorize(fn (AdvisoryUpdate $record): bool => auth()->user()->can('view', $record))
            ->label(fn (AdvisoryUpdate $record): string => auth()->user()->can('update', $record) ? 'Edit' : 'View')
            ->modalHeading(fn (AdvisoryUpdate $record): string => auth()->user()->can('update', $record) ? 'Edit advisory update' : 'View advisory update')
            ->schema([
                static::getPropertiesSectionSchema(),
                ToggleButtons::make('status_id')
                    ->label('Status')
                    ->inline()
                    ->options(fn (AdvisoryUpdate $record): array => static::getStatusOptions($record->advisory))
                    ->exists((new AdvisoryStatus())->getTable(), 'id')
                    ->required()
                    ->columnSpanFull(),
            ])
            ->fillForm(fn (AdvisoryUpdate $record): array => [
                'title' => $record->title,
                'update' => $record->update,
                'internal' => $record->internal,
                'date' => $record->date,
                'status_id' => $record->advisory->status_id,
            ])
            ->disabledForm(fn (AdvisoryUpdate $record): bool => auth()->user()->cannot('update', $record))
            ->modalSubmitAction(fn (AdvisoryUpdate $record): bool|null => auth()->user()->can('update', $record) ? null : false)
            ->modalCancelActionLabel(fn (AdvisoryUpdate $record): string => auth()->user()->can('update', $record) ? 'Cancel' : 'Close')
            ->action(function (array $data, AdvisoryUpdate $record): void {
                abort_unless(auth()->user()->can('update', $record), 403);

                $record->update(Arr::except($data, ['status_id']));
                $record->advisory->update(['status_id' => $data['status_id']]);
            });
    }

    private static function getPropertiesSectionSchema(): Section
    {
        return Section::make('Properties')
            ->schema([
                TextInput::make('title')
                    ->label('Title')
                    ->required()
                    ->maxLength(255)
                    ->string()
                    ->visible(AdvisoryUpdateTitleAndDateFeature::active())
                    ->columnSpanFull(),
                Textarea::make('update')
                    ->label('Description')
                    ->rows(3)
                    ->required()
                    ->string()
                    ->columnSpanFull(),
                Toggle::make('internal')
                    ->label('Internal')
                    ->rule(['boolean'])
                    ->columnSpanFull(),
                DateTimePicker::make('date')
                    ->label('Date')
                    ->required()
                    ->default(now())
                    ->visible(AdvisoryUpdateTitleAndDateFeature::active())
                    ->columnSpanFull(),
            ]);
    }

    /**
     * @return array<string, string>
     */
    private static function getStatusOptions(Advisory $currentAdvisory): array
    {
        return AdvisoryStatus::orderBy('classification')
            ->orderBy('name')
            ->get(['id', 'name'])
            ->mapWithKeys(fn (AdvisoryStatus $status): array => [
                $status->getKey() => $status->name . ($status->getKey() === $currentAdvisory->status->getKey() ? ' (Current)' : ''),
            ])
            ->all();
    }
}
