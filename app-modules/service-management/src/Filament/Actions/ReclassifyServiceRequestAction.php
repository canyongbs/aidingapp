<?php

namespace AidingApp\ServiceManagement\Filament\Actions;

use AidingApp\ServiceManagement\Enums\ServiceRequestAssignmentStatus;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequests\ServiceRequestResource;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestPriority;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use AidingApp\ServiceManagement\Rules\ManagedServiceRequestType;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class ReclassifyServiceRequestAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Reclassify')
            ->modalSubmitActionLabel('Reclassify')
            ->modalHeading(fn (): HtmlString => new HtmlString(
                'Reclassify Service Request<br><span class="text-sm font-normal text-gray-500">' . $this->getRecord()?->service_request_number . '</span>'
            ))
            ->modalDescription('Reclassify this request to a different service request type. The selected type determines the responsible team, assignment rules, and communication templates.')
            ->schema([
                Section::make('Assignment')
                    ->schema([
                        Select::make('type_id')
                            ->label('Type')
                            ->options(fn (ServiceRequest $record) => ServiceRequestType::query()
                                ->where(function (Builder $query) {
                                    $query->withoutArchived();

                                    if (! auth()->user()->isSuperAdmin()) {
                                        $query->where(function (Builder $query) {
                                            $query->whereHas('managerUsers', fn (Builder $query) => $query->where('users.id', auth()->user()->getKey()));
                                            $query->orWhereHas('managerTeams', fn (Builder $query) => $query->where('teams.id', auth()->user()->team?->getKey()));
                                        });
                                    }
                                })
                                ->whereKeyNot($record->priority->type->getKey())
                                ->orderBy('name')
                                ->pluck('name', 'id'))
                            ->afterStateUpdated(function (?string $state, Set $set): void {
                                if (! $state) {
                                    $set('priority_id', null);

                                    return;
                                }

                                $currentPriorityName = $this->getRecord()?->priority?->name;

                                $matchingPriority = ServiceRequestPriority::query()
                                    ->where('type_id', $state)
                                    ->where('name', $currentPriorityName)
                                    ->first();

                                $set('priority_id', $matchingPriority?->getKey());
                            })
                            ->required()
                            ->rule(new ManagedServiceRequestType())
                            ->live()
                            ->exists(ServiceRequestType::class, 'id'),

                        ToggleButtons::make('priority_id')
                            ->label('Priority')
                            ->options(fn (Get $get) => ServiceRequestPriority::query()
                                ->where('type_id', $get('type_id'))
                                ->orderBy('order')
                                ->pluck('name', 'id'))
                            ->inline()
                            ->required()
                            ->visible(fn (Get $get): bool => filled($get('type_id'))),

                        ToggleButtons::make('assignment_method')
                            ->label('Assignment Method')
                            ->options([
                                'default' => 'Default',
                                'override' => 'Override',
                            ])
                            ->default('default')
                            ->inline()
                            ->required()
                            ->live()
                            ->helperText(fn (Get $get): string => $get('assignment_method') === 'override'
                                ? 'Override lets you manually assign the request to a specific eligible agent.'
                                : 'Default applies the assignment rules configured for the selected service request type.'),

                        Select::make('assign_to')
                            ->label('Assign To')
                            ->visible(fn (Get $get): bool => $get('assignment_method') === 'override')
                            ->required(fn (Get $get): bool => $get('assignment_method') === 'override')
                            ->searchable()
                            ->getSearchResultsUsing(fn (string $search, Get $get): array => User::query()
                                ->where(function (Builder $query) use ($get): void {
                                    $typeId = $get('type_id');
                                    $query->whereHas('manageableServiceRequestTypes', fn (Builder $q) => $q->where('service_request_type_id', $typeId));
                                    $query->orWhereHas('team.manageableServiceRequestTypes', fn (Builder $q) => $q->where('service_request_type_id', $typeId));
                                })
                                ->where(new Expression('lower(name)'), 'like', '%' . Str::lower($search) . '%')
                                ->pluck('name', 'id')
                                ->all())
                            ->getOptionLabelUsing(fn ($value): ?string => User::find($value)?->name),
                    ]),
            ])
            ->action(function (array $data, ServiceRequest $record): void {
                DB::transaction(function () use ($data, $record): void {
                    $record->priority_id = $data['priority_id'];
                    $record->save();
                    $record->refresh();

                    if ($data['assignment_method'] === 'override') {
                        $record->assignments()->create([
                            'user_id' => $data['assign_to'],
                            'assigned_by_id' => auth()->id(),
                            'assigned_at' => now(),
                            'status' => ServiceRequestAssignmentStatus::Active,
                        ]);
                    } else {
                        ServiceRequestType::find($data['type_id'])
                            ?->assignment_type
                            ?->getAssignerClass()
                            ?->execute($record);
                    }
                });
            })
            ->successRedirectUrl(fn (ServiceRequest $record): string => ServiceRequestResource::getUrl('view', ['record' => $record]));
    }
}
