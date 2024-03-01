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

namespace AdvisingApp\Contact\Filament\Resources\ContactResource\Pages;

use App\Models\User;
use Filament\Forms\Get;
use Filament\Tables\Table;
use Filament\Actions\CreateAction;
use Filament\Actions\ImportAction;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use AdvisingApp\Contact\Models\Contact;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use App\Filament\Tables\Columns\IdColumn;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\BulkActionGroup;
use Illuminate\Database\Eloquent\Collection;
use AdvisingApp\Contact\Models\ContactSource;
use AdvisingApp\Contact\Models\ContactStatus;
use Filament\Tables\Actions\DeleteBulkAction;
use AdvisingApp\Contact\Imports\ContactImporter;
use AdvisingApp\Contact\Filament\Resources\ContactResource;
use AdvisingApp\Engagement\Filament\Actions\BulkEngagementAction;
use AdvisingApp\Notification\Filament\Actions\SubscribeBulkAction;
use AdvisingApp\CareTeam\Filament\Actions\ToggleCareTeamBulkAction;
use AdvisingApp\Notification\Filament\Actions\SubscribeTableAction;
use AdvisingApp\Engagement\Filament\Actions\Contracts\HasBulkEngagementAction;
use AdvisingApp\Engagement\Filament\Actions\Concerns\ImplementsHasBulkEngagementAction;

class ListContacts extends ListRecords implements HasBulkEngagementAction
{
    use ImplementsHasBulkEngagementAction;

    protected static string $resource = ContactResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                IdColumn::make(),
                TextColumn::make(Contact::displayNameKey())
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->translateLabel()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('mobile')
                    ->label('Mobile')
                    ->translateLabel()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->translateLabel()
                    ->state(function (Contact $record) {
                        return $record->status->name;
                    })
                    ->color(function (Contact $record) {
                        return $record->status->color->value;
                    })
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query
                            ->join('contact_statuses', 'contacts.status_id', '=', 'contact_statuses.id')
                            ->orderBy('contact_statuses.name', $direction);
                    }),
                TextColumn::make('source.name')
                    ->label('Source')
                    ->translateLabel()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('g:ia - M j, Y')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status_id')
                    ->relationship('status', 'name')
                    ->multiple()
                    ->preload(),
                SelectFilter::make('source_id')
                    ->relationship('source', 'name')
                    ->multiple()
                    ->preload(),
                Filter::make('care_team')
                    ->label('Care Team')
                    ->query(
                        function (Builder $query) {
                            return $query
                                ->whereRelation('careTeam', 'user_id', '=', auth()->id())
                                ->get();
                        }
                    ),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                SubscribeTableAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    SubscribeBulkAction::make(),
                    BulkEngagementAction::make(context: 'contacts'),
                    DeleteBulkAction::make(),
                    ToggleCareTeamBulkAction::make(),
                    BulkAction::make('bulk_update')
                        ->icon('heroicon-o-pencil-square')
                        ->form([
                            Select::make('field')
                                ->options([
                                    'assigned_to_id' => 'Assigned To',
                                    'description' => 'Description',
                                    'email_bounce' => 'Email Bounce',
                                    'hsgrad' => 'High School Graduation Date',
                                    'sms_opt_out' => 'SMS Opt Out',
                                    'source_id' => 'Source',
                                    'status_id' => 'Status',
                                ])
                                ->required()
                                ->live(),
                            Select::make('assigned_to_id')
                                ->label('Assigned To')
                                ->relationship('assignedTo', 'name')
                                ->searchable()
                                ->exists(
                                    table: (new User())->getTable(),
                                    column: (new User())->getKeyName()
                                )
                                ->required()
                                ->visible(fn (Get $get) => $get('field') === 'assigned_to_id'),
                            Textarea::make('description')
                                ->string()
                                ->required()
                                ->visible(fn (Get $get) => $get('field') === 'description'),
                            Radio::make('email_bounce')
                                ->label('Email Bounce')
                                ->boolean()
                                ->required()
                                ->visible(fn (Get $get) => $get('field') === 'email_bounce'),
                            TextInput::make('hsgrad')
                                ->label('High School Graduation Date')
                                ->numeric()
                                ->minValue(1920)
                                ->maxValue(now()->addYears(25)->year)
                                ->required()
                                ->visible(fn (Get $get) => $get('field') === 'hsgrad'),
                            Radio::make('sms_opt_out')
                                ->label('SMS Opt Out')
                                ->boolean()
                                ->required()
                                ->visible(fn (Get $get) => $get('field') === 'sms_opt_out'),
                            Select::make('source_id')
                                ->label('Source')
                                ->relationship('source', 'name')
                                ->exists(
                                    table: (new ContactSource())->getTable(),
                                    column: (new ContactSource())->getKeyName()
                                )
                                ->required()
                                ->visible(fn (Get $get) => $get('field') === 'source_id'),
                            Select::make('status_id')
                                ->label('Status')
                                ->relationship('status', 'name')
                                ->exists(
                                    table: (new ContactStatus())->getTable(),
                                    column: (new ContactStatus())->getKeyName()
                                )
                                ->required()
                                ->visible(fn (Get $get) => $get('field') === 'status_id'),
                        ])
                        ->action(function (Collection $records, array $data) {
                            $records->each(
                                fn (Contact $contact) => $contact
                                    ->forceFill([$data['field'] => $data[$data['field']]])
                                    ->save()
                            );

                            Notification::make()
                                ->title($records->count() . ' ' . str('Contact')->plural($records->count()) . ' Updated')
                                ->success()
                                ->send();
                        }),
                ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            ImportAction::make()
                ->importer(ContactImporter::class)
                ->authorize('import', Contact::class),
            CreateAction::make(),
        ];
    }
}
