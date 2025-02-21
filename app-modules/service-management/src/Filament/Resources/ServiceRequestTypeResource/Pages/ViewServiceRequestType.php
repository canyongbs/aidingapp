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

namespace AidingApp\ServiceManagement\Filament\Resources\ServiceRequestTypeResource\Pages;

use AidingApp\ServiceManagement\Filament\Resources\ServiceRequestFormResource;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequestTypeResource;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use App\Enums\Feature;
use Filament\Actions\EditAction;
use Filament\Forms\Get;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Gate;

class ViewServiceRequestType extends ViewRecord
{
    protected static string $resource = ServiceRequestTypeResource::class;

    protected static ?string $navigationLabel = 'View';

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make()
                    ->columns()
                    ->schema([
                        TextEntry::make('name')
                            ->label('Name'),
                        TextEntry::make('icon')
                            ->state(fn (ServiceRequestType $record): string => str($record->icon)->after('heroicon-o-')->headline()->toString())
                            ->icon(fn (ServiceRequestType $record): string => $record->icon),
                        TextEntry::make('form.name')
                            ->label('Form')
                            ->hidden(fn (ServiceRequestType $record) => ! $record->form)
                            ->url(fn (ServiceRequestType $record) => $record->form ? ServiceRequestFormResource::getUrl('edit', ['record' => $record?->form]) : null)
                            ->color('primary'),
                        Group::make()
                            ->schema([
                                TextEntry::make('has_enabled_feedback_collection')
                                    ->hiddenLabel()
                                    ->state('Feedback collection')
                                    ->badge()
                                    ->color(fn (ServiceRequestType $record) => $record->has_enabled_feedback_collection ? 'success' : 'gray'),
                                TextEntry::make('has_enabled_csat')
                                    ->hiddenLabel()
                                    ->state('CSAT')
                                    ->badge()
                                    ->color(fn (ServiceRequestType $record) => $record->has_enabled_csat ? 'success' : 'gray'),
                                TextEntry::make('has_enabled_nps')
                                    ->hiddenLabel()
                                    ->state('NPS')
                                    ->badge()
                                    ->color(fn (ServiceRequestType $record) => $record->has_enabled_nps ? 'success' : 'gray'),
                            ])
                            ->visible(fn (Get $get): bool => Gate::check(Feature::FeedbackManagement->getGateName())),
                        TextEntry::make('description')
                            ->label('Description')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
