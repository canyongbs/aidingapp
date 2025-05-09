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

namespace AidingApp\ServiceManagement\Filament\Resources\ServiceRequestResource\RelationManagers;

use AidingApp\ServiceManagement\Models\ServiceRequestFormSubmission;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ServiceRequestFormSubmissionRelationManager extends RelationManager
{
    protected static string $relationship = 'serviceRequestFormSubmission';

    protected static ?string $recordTitleAttribute = 'id';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('submitted_at')
                    ->dateTime(),
                TextColumn::make('author.email')
                    ->label('Submitted By')
                    ->url(
                        fn (ServiceRequestFormSubmission $record) => $record->author
                        ? resolve($record->author::filamentResource())->getUrl('view', ['record' => $record->author])
                        : null
                    )
                    ->color('primary'),
                TextColumn::make('author_type')
                    ->label('Submitted By Type')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): ?string => filled($state) ? ucfirst($state) : null)
                    ->color('success'),
            ])
            ->actions([
                ViewAction::make()
                    ->modalHeading(fn (ServiceRequestFormSubmission $record) => 'Submission Details: ' . $record->submitted_at->format('M j, Y H:i:s'))
                    ->infolist(fn (ServiceRequestFormSubmission $record): ?array => ($record->author && $record->submissible->is_authenticated) ? [
                        Section::make('Submitted By')
                            ->schema([
                                TextEntry::make('author.' . $record->author::displayNameKey())
                                    ->label('Name'),
                                TextEntry::make('author.email')
                                    ->label('Email address'),
                            ])
                            ->columns(2),
                    ] : null)
                    ->modalContent(fn (ServiceRequestFormSubmission $record) => view('service-management::submission', ['submission' => $record]))
                    ->visible(fn (ServiceRequestFormSubmission $record) => $record->submitted_at),
            ])
            ->paginated(false);
    }
}
