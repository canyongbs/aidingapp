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

namespace AidingApp\ServiceManagement\Filament\Resources\ServiceRequestResource\Pages;

use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Illuminate\Support\Collection;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use AidingApp\Division\Models\Division;
use Filament\Forms\Components\Textarea;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\ManageRelatedRecords;
use App\Filament\Forms\Components\EducatableSelect;
use Filament\Resources\RelationManagers\RelationManager;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use AidingApp\ServiceManagement\Models\ServiceRequestStatus;
use AidingApp\ServiceManagement\Models\ServiceRequestPriority;
use AidingApp\ServiceManagement\Rules\ManagedServiceRequestType;
use AidingApp\ServiceManagement\Actions\CreateServiceRequestAction;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequestResource;
use AidingApp\ServiceManagement\DataTransferObjects\ServiceRequestDataObject;

class CreateServiceRequest extends CreateRecord
{
    protected static string $resource = ServiceRequestResource::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('division_id')
                    ->relationship('division', 'name')
                    ->label('Division')
                    ->required()
                    ->exists((new Division())->getTable(), 'id')
                    ->default(auth()->user()->teams()->count() ? auth()->user()?->teams()->first()?->division?->id : ''),
                Select::make('status_id')
                    ->relationship('status', 'name')
                    ->label('Status')
                    ->options(fn () => ServiceRequestStatus::query()
                        ->orderBy('classification')
                        ->orderBy('name')
                        ->get(['id', 'name', 'classification'])
                        ->groupBy(fn (ServiceRequestStatus $status) => $status->classification->getlabel())
                        ->map(fn (Collection $group) => $group->pluck('name', 'id')))
                    ->required()
                    ->exists((new ServiceRequestStatus())->getTable(), 'id'),
                Grid::make()
                    ->schema([
                        Select::make('type_id')
                            ->options(ServiceRequestType::when(! auth()->user()->hasRole('authorization.super_admin'), function (Builder $query) {
                                $query->whereHas('managers', function (Builder $query): void {
                                    $query->where('teams.id', auth()->user()->teams()->first()?->getKey());
                                });
                            })
                                ->pluck('name', 'id'))
                            ->rule(new ManagedServiceRequestType())
                            ->afterStateUpdated(fn (Set $set) => $set('priority_id', null))
                            ->label('Type')
                            ->required()
                            ->live()
                            ->exists(ServiceRequestType::class, 'id'),
                        Select::make('priority_id')
                            ->relationship(
                                name: 'priority',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn (Get $get, Builder $query) => $query->where('type_id', $get('type_id'))->orderBy('order'),
                            )
                            ->label('Priority')
                            ->required()
                            ->exists(ServiceRequestPriority::class, 'id')
                            ->visible(fn (Get $get): bool => filled($get('type_id'))),
                    ]),
                TextInput::make('title')
                    ->required()
                    ->string()
                    ->maxLength(255)
                    ->columnSpanFull(),
                Textarea::make('close_details')
                    ->label('Description')
                    ->nullable()
                    ->string()
                    ->columnSpan(1),
                Textarea::make('res_details')
                    ->label('Internal Details')
                    ->nullable()
                    ->string()
                    ->columnSpan(1),
                EducatableSelect::make('respondent')
                    ->label('Related To')
                    ->required()
                    ->hiddenOn([RelationManager::class, ManageRelatedRecords::class]),
            ]);
    }

    protected function handleRecordCreation(array $data): Model
    {
        $serviceRequestDataObject = ServiceRequestDataObject::fromData($data);

        return app(CreateServiceRequestAction::class)->execute($serviceRequestDataObject);
    }
}
