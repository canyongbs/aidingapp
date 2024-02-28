<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/aidingapp/blob/main/LICENSE.

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

namespace AdvisingApp\MeetingCenter\Livewire;

use Livewire\Component;
use Filament\Tables\Table;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Concerns\InteractsWithTable;
use AdvisingApp\MeetingCenter\Models\EventAttendee;
use Illuminate\Database\Eloquent\Relations\HasMany;
use AdvisingApp\MeetingCenter\Models\EventRegistrationFormSubmission;

class EventAttendeeSubmissionsManager extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public EventAttendee $record;

    public function render()
    {
        return view('meeting-center::livewire.event-attendee-submissions-manager');
    }

    public function table(Table $table): Table
    {
        return $table
            ->relationship(fn (): HasMany => $this->record->submissions())
            ->inverseRelationship('author')
            ->columns([
                TextColumn::make('submitted_at')
                    ->label('Submitted At')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('attendee_status')
                    ->label('Submission Status')
                    ->badge(),
            ])
            ->defaultSort('submitted_at', 'desc')
            ->actions([
                ViewAction::make()
                    ->modalHeading(fn (EventRegistrationFormSubmission $record) => 'Submission Details: ' . $record->submitted_at->format('M j, Y H:i:s'))
                    ->infolist(fn (EventRegistrationFormSubmission $record): array => [
                        Section::make('Metadata')
                            ->schema([
                                TextEntry::make('author.email')
                                    ->label('Author Email Address'),
                                TextEntry::make('attendee_status')
                                    ->label('Submission Status')
                                    ->badge(),
                            ])
                            ->columns(),
                    ])
                    ->modalContent(fn (EventRegistrationFormSubmission $record) => view('meeting-center::event-registration-submission', ['submission' => $record])),
            ]);
    }
}
