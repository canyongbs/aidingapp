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

namespace AidingApp\Project\Filament\Resources\Projects\Pages;

use AidingApp\Project\Enums\PipelineStageClassification;
use AidingApp\Project\Filament\Resources\Projects\ProjectResource;
use AidingApp\Project\Models\PipelineEntry;
use AidingApp\Project\Models\Project;
use App\Filament\Tables\Columns\IdColumn;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ListProjects extends ListRecords
{
    protected static string $resource = ProjectResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                IdColumn::make(),
                TextColumn::make('name')
                    ->label('Project Name')
                    ->description(fn (Project $record): ?string => $record->description)
                    ->searchable()
                    ->sortable(),
                ViewColumn::make('managers')
                    ->label('Manager(s)')
                    ->view('project::filament.tables.columns.project.managers')
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query
                            ->whereHas(
                                'managerUsers',
                                fn (Builder $query) => $query->where('name', 'ilike', "%{$search}%"),
                            )
                            ->orWhereHas(
                                'managerDepartments.users',
                                fn (Builder $query) => $query->where('name', 'ilike', "%{$search}%"),
                            );
                    }),
                TextColumn::make('department.name')
                    ->label('Department')
                    ->placeholder('N/A')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('start_date')
                    ->label('Start Date')
                    ->date()
                    ->sortable()
                    ->placeholder('N/A'),
                TextColumn::make('target_completion_date')
                    ->label('Target Date')
                    ->date()
                    ->sortable()
                    ->placeholder('Indefinite'),
                ViewColumn::make('progress')
                    ->label('Progress')
                    ->view('project::filament.tables.columns.project.progress'),
            ])
            ->modifyQueryUsing(function (Builder $query, ListRecords $livewire): Builder {
                $query->with(['managerUsers', 'managerDepartments.users', 'department']);

                $query->addSelect([
                    'total_pipeline_entries_count' => PipelineEntry::query()
                        ->join('pipeline_stages', 'pipeline_stages.id', '=', 'pipeline_entries.pipeline_stage_id')
                        ->join('pipelines', 'pipelines.id', '=', 'pipeline_stages.pipeline_id')
                        ->whereColumn('pipelines.project_id', 'projects.id')
                        ->selectRaw('count(*)'),
                    'complete_pipeline_entries_count' => PipelineEntry::query()
                        ->join('pipeline_stages', 'pipeline_stages.id', '=', 'pipeline_entries.pipeline_stage_id')
                        ->join('pipelines', 'pipelines.id', '=', 'pipeline_stages.pipeline_id')
                        ->whereColumn('pipelines.project_id', 'projects.id')
                        ->where('pipeline_stages.classification', PipelineStageClassification::Complete->value)
                        ->selectRaw('count(*)'),
                ]);

                $search = $livewire->getTableSearch();

                if (blank($search)) {
                    return $query;
                }

                $likeSearch = "%{$search}%";

                return $query
                    ->selectRaw(
                        <<<'SQL'
                        CASE
                            WHEN projects.name ILIKE ? THEN 0
                            WHEN EXISTS (
                                SELECT 1
                                FROM project_manager_users
                                INNER JOIN users ON users.id = project_manager_users.user_id
                                WHERE project_manager_users.project_id = projects.id
                                AND users.name ILIKE ?
                            ) THEN 1
                            WHEN EXISTS (
                                SELECT 1
                                FROM project_manager_departments
                                INNER JOIN users ON users.department_id = project_manager_departments.department_id
                                WHERE project_manager_departments.project_id = projects.id
                                AND users.name ILIKE ?
                            ) THEN 1
                            WHEN (
                                SELECT name
                                FROM departments
                                WHERE departments.id = projects.department_id
                            ) ILIKE ? THEN 2
                            ELSE 3
                        END AS search_rank
                        SQL,
                        [$likeSearch, $likeSearch, $likeSearch, $likeSearch],
                    )
                    ->orderBy('search_rank');
            })
            ->filters([
                SelectFilter::make('department')
                    ->relationship('department', 'name')
                    ->multiple()
                    ->preload()
                    ->label('Department'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->authorizeIndividualRecords('delete'),
                ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
