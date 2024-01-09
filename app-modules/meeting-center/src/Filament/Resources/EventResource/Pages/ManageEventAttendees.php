<?php

/*
<COPYRIGHT>

    Copyright © 2022-2023, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

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
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\MeetingCenter\Filament\Resources\EventResource\Pages;

use Filament\Tables\Table;
use App\Filament\Columns\IdColumn;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use AdvisingApp\Prospect\Models\Prospect;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\Livewire;
use Filament\Infolists\Components\TextEntry;
use AdvisingApp\StudentDataModel\Models\Student;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Resources\Pages\ManageRelatedRecords;
use AdvisingApp\MeetingCenter\Models\EventAttendee;
use AdvisingApp\Prospect\Filament\Resources\ProspectResource;
use AdvisingApp\MeetingCenter\Filament\Resources\EventResource;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource;
use AdvisingApp\MeetingCenter\Filament\Actions\InviteAttendeesAction;

class ManageEventAttendees extends ManageRelatedRecords
{
    protected static string $resource = EventResource::class;

    // TODO: Obsolete when there is no table, remove from Filament
    protected static string $relationship = 'attendees';

    protected static ?string $navigationLabel = 'Attendees';

    protected static ?string $breadcrumb = 'Attendees';

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                IdColumn::make(),
                TextColumn::make('status')
                    ->badge(),
                TextColumn::make('email'),
            ])
            ->filters([])
            ->headerActions([])
            ->actions([
                ViewAction::make()
                    ->modalHeading(fn (EventAttendee $record) => $record->event->title . ' - ' . $record->email)
                    ->infolist(fn (EventAttendee $record): array => [
                        Fieldset::make('Attendee Info')
                            ->schema([
                                TextEntry::make('status')
                                    ->label('Status')
                                    ->badge(),
                                TextEntry::make('email')
                                    ->label('Email address'),
                            ])
                            ->columns(),

                        Fieldset::make('Relations')
                            ->schema([
                                RepeatableEntry::make('prospects')
                                    ->schema([
                                        TextEntry::make(Prospect::displayNameKey())
                                            ->label('Name')
                                            ->color('primary')
                                            ->url(fn (Prospect $record): string => ProspectResource::getUrl('view', ['record' => $record])),
                                    ])
                                    ->columns()
                                    ->visible(fn (EventAttendee $record): bool => $record->prospects->isNotEmpty()),
                                RepeatableEntry::make('students')
                                    ->schema([
                                        TextEntry::make(Student::displayNameKey())
                                            ->label('Name')
                                            ->color('primary')
                                            ->url(fn (Student $record): string => StudentResource::getUrl('view', ['record' => $record])),
                                    ])
                                    ->columns()
                                    ->visible(fn (EventAttendee $record): bool => $record->students->isNotEmpty()),
                            ])
                            ->visible(fn (EventAttendee $record): bool => $record->prospects->isNotEmpty() || $record->students->isNotEmpty())
                            ->columns(),

                        Fieldset::make('Attendee Submissions')
                            ->schema([
                                // TODO: Look into Livewire/Filament bug that prevents us from passing the class here, requiring that we define it in the Service Provider
                                // TODO: Look into bug where, without lazy load, you have to click view twice to get the modal to show
                                Livewire::make('event-attendee-submissions-manager')->lazy()
                                    ->columnSpanFull(),
                            ]),
                    ]),
            ])
            ->bulkActions([]);
    }

    protected function getHeaderActions(): array
    {
        return [
            InviteAttendeesAction::make(),
        ];
    }
}
