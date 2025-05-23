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
use App\Enums\Feature;
use App\Filament\Forms\Components\IconSelect;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Gate;

class CreateServiceRequestType extends CreateRecord
{
    protected static string $resource = ServiceRequestTypeResource::class;

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
                                    ->visible(fn (Get $get) => $get('has_enabled_feedback_collection')),
                                Toggle::make('has_enabled_nps')
                                    ->label('NPS')
                                    ->visible(fn (Get $get) => $get('has_enabled_feedback_collection')),
                            ])
                            ->visible(fn (Get $get): bool => Gate::check(Feature::FeedbackManagement->getGateName())),
                        Textarea::make('description')
                            ->string()
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    protected function afterCreate(): void
    {
        $this->getRecord()->priorities()->createMany(
            [
                ['name' => 'High', 'order' => 1],
                ['name' => 'Medium', 'order' => 2],
                ['name' => 'Low', 'order' => 3],
            ]
        );
    }
}
