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

use AidingApp\ServiceManagement\Filament\Resources\ServiceRequestTypeResource;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use App\Concerns\EditPageRedirection;
use App\Enums\Feature;
use App\Filament\Forms\Components\IconSelect;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Gate;

class EditServiceRequestType extends EditRecord
{
    use EditPageRedirection;

    protected static string $resource = ServiceRequestTypeResource::class;

    protected static ?string $navigationLabel = 'Edit';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->columns()
                    ->schema([
                        TextInput::make('name')
                            ->label('Name')
                            ->required()
                            ->string(),
                        IconSelect::make('icon'),
                        Group::make()
                            ->schema([
                                Toggle::make('has_enabled_feedback_collection')
                                    ->label('Enable feedback collection')
                                    ->live(),
                                Toggle::make('has_enabled_csat')
                                    ->label('CSAT')
                                    ->live()
                                    ->validationMessages([
                                        'accepted' => 'At least one option must be accepted, CSAT or NPS.',
                                    ])
                                    ->accepted(fn (Get $get) => ! $get('has_enabled_nps') ? true : false)
                                    ->visible(fn (Get $get) => $get('has_enabled_feedback_collection')),
                                Toggle::make('has_enabled_nps')
                                    ->label('NPS')
                                    ->live()
                                    ->validationMessages([
                                        'accepted' => 'At least one option must be accepted, CSAT or NPS.',
                                    ])
                                    ->accepted(fn (Get $get) => ! $get('has_enabled_csat') ? true : false)
                                    ->visible(fn (Get $get) => $get('has_enabled_feedback_collection')),
                                Toggle::make('is_reminders_enabled')
                                    ->label('Feedback Reminder')
                                    ->helperText('An email reminder will be sent 2 days after the initial feedback survey is delivered if incomplete.')
                                    ->visible(fn (Get $get) => $get('has_enabled_feedback_collection') && ($get('has_enabled_csat') || $get('has_enabled_nps'))),
                            ])
                            ->columnSpanFull()
                            ->visible(fn (Get $get): bool => Gate::check(Feature::FeedbackManagement->getGateName())),
                        Textarea::make('description')
                            ->string()
                            ->columnSpanFull(),
                    ]),
            ])
            ->disabled(fn (ServiceRequestType $record) => $record->trashed());
    }

    protected function getSaveFormAction(): Action
    {
        return parent::getSaveFormAction()
            ->hidden(fn (ServiceRequestType $record) => $record->trashed());
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            RestoreAction::make(),
            ForceDeleteAction::make(),
        ];
    }
}
