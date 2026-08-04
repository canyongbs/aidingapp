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

namespace AidingApp\Project\Filament\Resources\Pipelines\Forms;

use AidingApp\Contact\Models\Contact;
use AidingApp\Project\Filament\Tables\PipelineEntryAssetsTable;
use AidingApp\Project\Filament\Tables\PipelineEntryAssignedToContactsTable;
use AidingApp\Project\Filament\Tables\PipelineEntryAssignedToUsersTable;
use AidingApp\Project\Filament\Tables\PipelineEntryMilestonesTable;
use AidingApp\Project\Filament\Tables\PipelineEntryServiceRequestsTable;
use AidingApp\Project\Filament\Tables\ProjectPipelinesStageTable;
use AidingApp\Project\Models\Pipeline;
use AidingApp\Project\Models\PipelineEntry;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use App\Models\User;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\ModalTableSelect;
use Filament\Forms\Components\TableSelect;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Str;

class PipelineEntryForm
{
    /**
     * Returns the shared form components common to create and edit pipeline entry forms.
     *
     * @return array<int, mixed>
     */
    public static function components(?Pipeline $pipeline = null, bool $isStageVisible = true): array
    {
        return [
            TextInput::make('name')
                ->label('Task Name')
                ->required()
                ->maxLength(255),
            Textarea::make('description')
                ->label('Task Description')
                ->maxLength(65535),
            TableSelect::make('pipeline_stage_id')
                ->label('Stage')
                ->tableConfiguration(ProjectPipelinesStageTable::class)
                ->tableArguments(['pipelineId' => $pipeline?->getKey()])
                ->visible($isStageVisible)
                ->required(),
            DateTimePicker::make('due')
                ->label('Due Date'),
            ToggleButtons::make('assigned_to_type')
                ->label('Assigned To Type')
                ->options([
                    'none' => 'None',
                    Relation::getMorphAlias(User::class) => 'User',
                    Relation::getMorphAlias(Contact::class) => 'Contact',
                ])
                ->default('none')
                ->inline()
                ->live()
                ->afterStateHydrated(function (?string $state, Set $set) {
                    if ($state === null) {
                        $set('assigned_to_type', 'none');
                    }
                })
                ->afterStateUpdated(function (?string $state, Set $set) {
                    if ($state === 'none') {
                        $set('assigned_to_id', null);
                        $set('assigned_to_type', null);
                    }
                }),
            ModalTableSelect::make('assigned_to_id')
                ->label('Assigned To')
                ->tableConfiguration(fn (Get $get): string => match (Relation::getMorphedModel((string) $get('assigned_to_type'))) {
                    Contact::class => PipelineEntryAssignedToContactsTable::class,
                    default => PipelineEntryAssignedToUsersTable::class,
                })
                ->getOptionLabelUsing(function (Get $get, mixed $state): ?string {
                    $type = $get('assigned_to_type');

                    if (blank($type) || blank($state)) {
                        return null;
                    }

                    $modelClass = Relation::getMorphedModel($type) ?? $type;

                    $record = $modelClass::query()->find($state);

                    return $record instanceof Contact ? $record->full_name : $record?->name;
                })
                ->visible(fn (Get $get): bool => filled($get('assigned_to_type')) && $get('assigned_to_type') !== 'none')
                ->dehydrateStateUsing(fn (Get $get, mixed $state): mixed => filled($get('assigned_to_type')) ? $state : null)
                ->dehydrated()
                ->dehydratedWhenHidden(),
            Toggle::make('is_visible_to_guests')
                ->label('Visible to Guest')
                ->default(true),

            ToggleButtons::make('milestones_type')
                ->label('Milestones Type')
                ->options([
                    'none' => 'None',
                    'select' => 'Select',
                ])
                ->default('none')
                ->inline()
                ->live()
                ->afterStateHydrated(function (Set $set, ?PipelineEntry $record) {
                    $state = $record?->milestones()->exists();

                    if (filled($state)) {
                        $set('milestones_type', 'select');
                    } else {
                        $set('milestones_type', 'none');
                    }
                })
                ->dehydrated(false)
                ->afterStateUpdated(function (?string $state, Set $set) {
                    if ($state === 'none') {
                        $set('milestones', []);
                    }
                }),
            ModalTableSelect::make('milestones')
                ->label('Related Milestones')
                ->relationship(
                    name: 'milestones',
                    titleAttribute: 'title',
                    modifyQueryUsing: $pipeline
                        ? fn (Builder $query) => $query->where('project_id', $pipeline->project_id)
                        : null,
                )
                ->tableConfiguration(PipelineEntryMilestonesTable::class)
                ->tableArguments(['projectId' => $pipeline?->project_id])
                ->tableSelect(fn (TableSelect $tableSelect): TableSelect => $tableSelect->relationshipName(null))
                ->visible(fn (Get $get): bool => filled($get('milestones_type')) && $get('milestones_type') !== 'none')
                ->multiple()
                ->dehydrated()
                ->dehydratedWhenHidden(),

            ToggleButtons::make('assets_type')
                ->label('Assets Type')
                ->options([
                    'none' => 'None',
                    'select' => 'Select',
                ])
                ->default('none')
                ->inline()
                ->live()
                ->afterStateHydrated(function (Set $set, ?PipelineEntry $record) {
                    $state = $record?->assets()->exists();

                    if ($state) {
                        $set('assets_type', 'select');
                    } else {
                        $set('assets_type', 'none');
                    }
                })
                ->dehydrated(false)
                ->afterStateUpdated(function (?string $state, Set $set) {
                    if ($state === 'none') {
                        $set('assets', []);
                    }
                }),
            ModalTableSelect::make('assets')
                ->label('Related Assets')
                ->relationship(name: 'assets', titleAttribute: 'name')
                ->tableConfiguration(PipelineEntryAssetsTable::class)
                ->visible(fn (Get $get): bool => filled($get('assets_type')) && $get('assets_type') !== 'none')
                ->multiple()
                ->dehydrated()
                ->dehydratedWhenHidden(),
            ToggleButtons::make('service_requests_type')
                ->label('Service Requests Type')
                ->options([
                    'none' => 'None',
                    'select' => 'Select',
                ])
                ->default('none')
                ->inline()
                ->live()
                ->afterStateHydrated(function (Set $set, ?PipelineEntry $record) {
                    $state = $record?->serviceRequests()->exists();

                    if ($state) {
                        $set('service_requests_type', 'select');
                    } else {
                        $set('service_requests_type', 'none');
                    }
                })
                ->dehydrated(false)
                ->afterStateUpdated(function (?string $state, Set $set) {
                    if ($state === 'none') {
                        $set('serviceRequests', []);
                    }
                }),
            ModalTableSelect::make('serviceRequests')
                ->label('Related Service Requests')
                ->relationship(name: 'serviceRequests', titleAttribute: 'service_request_number')
                ->getOptionLabelFromRecordUsing(fn (ServiceRequest $record): string => self::serviceRequestLabel($record))
                ->visible(fn (Get $get): bool => filled($get('service_requests_type')) && $get('service_requests_type') !== 'none')
                ->tableConfiguration(PipelineEntryServiceRequestsTable::class)
                ->multiple()
                ->dehydrated()
                ->dehydratedWhenHidden(),
        ];
    }

    public static function serviceRequestLabel(ServiceRequest $serviceRequest): string
    {
        $title = filled($serviceRequest->title)
            ? ' ' . Str::limit($serviceRequest->title, 40)
            : '';

        return "({$serviceRequest->service_request_number}){$title}";
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (($data['milestones_type'] ?? null) === 'none') {
            $data['milestones'] = [];
        }

        if (($data['assets_type'] ?? null) === 'none') {
            $data['assets'] = [];
        }

        if (($data['service_requests_type'] ?? null) === 'none') {
            $data['serviceRequests'] = [];
        }

        return $data;
    }
}
