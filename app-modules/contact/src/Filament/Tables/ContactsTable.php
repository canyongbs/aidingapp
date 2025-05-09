<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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

namespace AidingApp\Contact\Filament\Tables;

use AidingApp\Contact\Filament\Resources\ContactResource;
use AidingApp\Contact\Models\Contact;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\QueryBuilder\Constraints\BooleanConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\IsRelatedToOperator;
use Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ContactsTable
{
    public function __invoke(Table $table): Table
    {
        return $table
            ->query(fn () => Contact::query())
            ->columns([
                TextColumn::make(Contact::displayNameKey())
                    ->label('Name')
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->sortable(),
                TextColumn::make('mobile')
                    ->label('Mobile')
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
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
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('g:ia - M j, Y ')
                    ->sortable(),
            ])
            ->filters([
                QueryBuilder::make()
                    ->constraints([
                        TextConstraint::make('first_name')
                            ->icon('heroicon-m-user'),
                        TextConstraint::make('last_name')
                            ->icon('heroicon-m-user'),
                        TextConstraint::make('full_name')
                            ->icon('heroicon-m-user'),
                        TextConstraint::make('preferred')
                            ->label('Preferred Name')
                            ->icon('heroicon-m-user'),
                        QueryBuilder\Constraints\DateConstraint::make('created_at')
                            ->icon('heroicon-m-calendar'),
                        TextConstraint::make('email')
                            ->label('Email Address')
                            ->icon('heroicon-m-envelope'),
                        TextConstraint::make('mobile')
                            ->icon('heroicon-m-phone'),
                        TextConstraint::make('phone')
                            ->icon('heroicon-m-phone'),
                        TextConstraint::make('address')
                            ->icon('heroicon-m-map-pin'),
                        TextConstraint::make('address_2')
                            ->icon('heroicon-m-map-pin'),
                        BooleanConstraint::make('sms_opt_out')
                            ->label('SMS Opt Out')
                            ->icon('heroicon-m-chat-bubble-bottom-center'),
                        BooleanConstraint::make('email_bounce')
                            ->icon('heroicon-m-arrow-uturn-left'),
                        RelationshipConstraint::make('status')
                            ->icon('heroicon-m-flag')
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->multiple()
                                    ->preload(),
                            ),
                        RelationshipConstraint::make('source')
                            ->icon('heroicon-m-arrow-left-on-rectangle')
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->multiple()
                                    ->preload(),
                            ),
                    ])
                    ->constraintPickerColumns([
                        'md' => 2,
                        'lg' => 3,
                        'xl' => 4,
                    ])
                    ->constraintPickerWidth('7xl'),
            ], layout: FiltersLayout::AboveContent)
            ->actions([
                ViewAction::make()
                    ->authorize('view')
                    ->url(fn (Contact $record) => ContactResource::getUrl('view', ['record' => $record])),
            ]);
    }
}
