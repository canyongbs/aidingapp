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

namespace AidingApp\Contact\Filament\Resources\ContactResource\Pages;

use AidingApp\Contact\Filament\Resources\ContactResource;
use AidingApp\Contact\Filament\Resources\ContactResource\Actions\BulkUpdateContactsAction;
use AidingApp\Contact\Imports\ContactImporter;
use AidingApp\Contact\Models\Contact;
use AidingApp\Engagement\Filament\Actions\BulkEngagementAction;
use App\Filament\Tables\Columns\IdColumn;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ImportAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ListContacts extends ListRecords
{
    protected static string $resource = ContactResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                IdColumn::make(),
                TextColumn::make(Contact::displayNameKey())
                    ->label('Name')
                    ->searchable()
                    ->sortable()
                    ->icon(fn (Contact $record): ?Heroicon => $record->isManaged() ? Heroicon::LockClosed : null)
                    ->iconColor('gray')
                    ->iconPosition(IconPosition::After)
                    ->tooltip(fn (Contact $record): ?string => $record->isManaged() ? 'This is a User\'s managed non-administrative account for the self-service portal. The information displayed is synchronized directly from the User record.' : null),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('mobile')
                    ->label('Mobile')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type')
                    ->badge()
                    ->state(function (Contact $record) {
                        return $record->type->name;
                    })
                    ->color(function (Contact $record) {
                        return $record->type->color->value;
                    })
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query
                            ->join('contact_types', 'contacts.type_id', '=', 'contact_types.id')
                            ->orderBy('contact_types.name', $direction);
                    }),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('type_id')
                    ->label('Type')
                    ->relationship('type', 'name')
                    ->multiple()
                    ->preload(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make()
                    ->hidden(fn (Contact $record): bool => $record->isManaged()),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkEngagementAction::make(context: 'contacts'),
                    DeleteBulkAction::make()
                        ->authorizeIndividualRecords('delete'),
                    BulkUpdateContactsAction::make(),
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
