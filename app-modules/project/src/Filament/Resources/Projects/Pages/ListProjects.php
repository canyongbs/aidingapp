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

use AidingApp\Project\Filament\Resources\Projects\ProjectResource;
use AidingApp\Project\Models\Project;
use AidingApp\Project\Models\Scopes\WithProgressCounts;
use App\Features\ProjectArchivingFeature;
use App\Filament\Tables\Columns\IdColumn;
use CanyonGBS\Common\Filament\Actions\ArchiveBulkAction;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

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
                    ->limit(80)
                    ->wrap()
                    ->description(fn (Project $record): ?string => $record->description)
                    ->searchable()
                    ->sortable(),
                ViewColumn::make('managers')
                    ->label('Manager(s)')
                    ->state(fn (Project $record): Collection => $record->allManagers())
                    ->default(fn (): Collection => new Collection())
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
                    ->placeholder('N/A')
                    ->date()
                    ->sortable(),
                TextColumn::make('target_completion_date')
                    ->label('Target Date')
                    ->date()
                    ->sortable()
                    ->placeholder('Indefinite'),
                ViewColumn::make('progress')
                    ->label('Progress')
                    ->state(fn (Project $record): int => $record->getProgressPercentage())
                    ->view('project::filament.tables.columns.project.progress'),
            ])
            ->modifyQueryUsing(function (Builder $query, ListRecords $livewire): Builder {
                $query
                    ->when(ProjectArchivingFeature::active(), fn (Builder $query): Builder => $query->withoutArchived())
                    ->with(['managerUsers.media', 'managerDepartments.users.media', 'department'])
                    ->tap(new WithProgressCounts());

                $search = $livewire->getTableSearch();

                if (blank($search) || filled($livewire->getTableSortColumn())) {
                    return $query;
                }

                $bindings = [];

                $searchWords = array_filter(
                    str_getcsv(
                        preg_replace('/(\s|\x{3164}|\x{1160})+/u', ' ', $search),
                        separator: ' ',
                        escape: '\\',
                    ),
                    fn (?string $word): bool => filled($word),
                );

                if (empty($searchWords)) {
                    return $query;
                }

                $matchesEveryWord = function (string $column) use ($searchWords, &$bindings): string {
                    $conditions = [];

                    foreach ($searchWords as $word) {
                        $conditions[] = "{$column} ILIKE ?";
                        $bindings[] = '%' . $word . '%';
                    }

                    return implode(' AND ', $conditions);
                };

                $nameMatch = $matchesEveryWord('projects.name');
                $managerUserMatch = $matchesEveryWord('users.name');
                $managerDepartmentUserMatch = $matchesEveryWord('users.name');
                $departmentMatch = $matchesEveryWord('departments.name');

                return $query
                    ->selectRaw(
                        <<<SQL
                        CASE
                            WHEN {$nameMatch} THEN 0
                            WHEN EXISTS (
                                SELECT 1
                                FROM project_manager_users
                                INNER JOIN users ON users.id = project_manager_users.user_id
                                WHERE project_manager_users.project_id = projects.id
                                AND users.deleted_at IS NULL
                                AND ({$managerUserMatch})
                            ) THEN 1
                            WHEN EXISTS (
                                SELECT 1
                                FROM project_manager_departments
                                INNER JOIN users ON users.department_id = project_manager_departments.department_id
                                WHERE project_manager_departments.project_id = projects.id
                                AND users.deleted_at IS NULL
                                AND ({$managerDepartmentUserMatch})
                            ) THEN 1
                            WHEN EXISTS (
                                SELECT 1
                                FROM departments
                                WHERE departments.id = projects.department_id
                                AND ({$departmentMatch})
                            ) THEN 2
                            ELSE 3
                        END AS search_rank
                        SQL,
                        $bindings,
                    )
                    ->orderBy('search_rank')
                    ->orderBy('projects.name');
            })
            ->filters([
                SelectFilter::make('department')
                    ->relationship('department', 'name')
                    ->multiple()
                    ->preload()
                    ->label('Department'),
            ])
            ->recordUrl(fn (Project $record): string => ProjectResource::getUrl('view', ['record' => $record]))
            ->recordActions([
                Action::make('archive')
                    ->label('Archive')
                    ->defaultColor('warning')
                    ->icon('heroicon-m-archive-box')
                    ->modalIcon('heroicon-o-archive-box')
                    ->modalSubmitActionLabel('Archive')
                    ->successNotificationTitle('Archived')
                    ->visible(fn (): bool => ProjectArchivingFeature::active())
                    ->hidden(fn (Project $record): bool => $record->isArchived())
                    ->requiresConfirmation()
                    ->action(fn (Project $record): bool => $record->archive())
                    ->authorize(fn (Project $record): bool => ProjectResource::can('archive', $record)),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    ArchiveBulkAction::make()
                        ->visible(fn (): bool => ProjectArchivingFeature::active())
                        ->authorizeIndividualRecords('delete')
                        ->deselectRecordsAfterCompletion(),
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
