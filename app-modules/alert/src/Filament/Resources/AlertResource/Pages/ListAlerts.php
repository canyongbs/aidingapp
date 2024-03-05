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

namespace AdvisingApp\Alert\Filament\Resources\AlertResource\Pages;

use AdvisingApp\Alert\Enums\AlertSeverity;
use AdvisingApp\Alert\Enums\AlertStatus;
use AdvisingApp\Alert\Filament\Resources\AlertResource;
use AdvisingApp\Alert\Models\Alert;
use AdvisingApp\Contact\Filament\Resources\ContactResource\Pages\ManageContactAlerts;
use AdvisingApp\Contact\Models\Contact;
use App\Filament\Forms\Components\EducatableSelect;
use App\Filament\Tables\Columns\IdColumn;
use App\Models\Scopes\EducatableSearch;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;

use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ListAlerts extends ListRecords
{
    protected static string $resource = AlertResource::class;

    // TODO: Change this to a link to the students page when tableAction link triggering becomes available in Filament 3.1
    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('concern.display_name')
                    ->label('Related To')
                    ->getStateUsing(fn (Alert $record): ?string => $record->concern?->{$record->concern::displayNameKey()})
                    ->url(fn (Alert $record) => match ($record->concern ? $record->concern::class : null) {
                        Contact::class => ManageContactAlerts::getUrl(['record' => $record->concern]),
                        default => null,
                    }),
                TextEntry::make('description'),
                TextEntry::make('severity'),
                TextEntry::make('suggested_intervention'),
                TextEntry::make('status'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                IdColumn::make(),
                TextColumn::make('concern.display_name')
                    ->label('Related To')
                    ->getStateUsing(fn (Alert $record): ?string => $record->concern?->{$record->concern::displayNameKey()})
                    ->url(fn (Alert $record) => match ($record->concern ? $record->concern::class : null) {
                        Contact::class => ManageContactAlerts::getUrl(['record' => $record->concern]),
                        default => null,
                    })
                    ->searchable(query: fn (Builder $query, $search) => $query->tap(new EducatableSearch(relationship: 'concern', search: $search)))
                    ->forceSearchCaseInsensitive()
                    ->sortable(),
                TextColumn::make('description')
                    ->searchable()
                    ->limit(),
                TextColumn::make('severity')
                    ->sortable(),
                TextColumn::make('status')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->sortable(),
            ])
            ->filters([
                Filter::make('subscribed')
                    ->query(
                        fn (Builder $query): Builder => $query->whereHas(
                            relation: 'concern',
                            callback: fn (Builder $query) => $query->whereRelation('subscriptions', 'user_id', auth()->id())
                        )
                    ),
                Filter::make('care_team')
                    ->label('Care Team')
                    ->query(
                        fn (Builder $query): Builder => $query->whereHas(
                            relation: 'concern',
                            callback: fn (Builder $query) => $query->whereRelation('careTeam', 'user_id', auth()->id())
                        )
                    ),
                SelectFilter::make('severity')
                    ->options(AlertSeverity::class),
                SelectFilter::make('status')
                    ->options(AlertStatus::class)
                    ->multiple()
                    ->default([AlertStatus::Active->value]),
            ])
            ->actions([
                ViewAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->form([
                    EducatableSelect::make('concern')
                        ->label('Related To')
                        ->required(),
                    Group::make()
                        ->schema([
                            Textarea::make('description')
                                ->required()
                                ->string(),
                            Select::make('severity')
                                ->options(AlertSeverity::class)
                                ->selectablePlaceholder(false)
                                ->default(AlertSeverity::default())
                                ->required()
                                ->enum(AlertSeverity::class),
                            Textarea::make('suggested_intervention')
                                ->required()
                                ->string(),
                            Select::make('status')
                                ->options(AlertStatus::class)
                                ->selectablePlaceholder(false)
                                ->default(AlertStatus::default())
                                ->required()
                                ->enum(AlertStatus::class),
                        ])
                        ->columns(),
                ]),
        ];
    }
}
