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

namespace AidingApp\Project\Filament\Resources\Projects\RelationManagers;

use AidingApp\Project\Filament\Resources\Pipelines\PipelineResource;
use AidingApp\Project\Models\Pipeline;
use App\Features\PipelineArchivingFeature;
use CanyonGBS\Common\Filament\Actions\ArchiveAction;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

// TODO: This relation manager (and its ManagePipelines page) is currently only
// reachable by direct URL. Evaluate whether it is still needed now that the
// pipeline switcher owns pipeline management; remove if redundant.
class PipelinesRelationManager extends RelationManager
{
    protected static string $relationship = 'pipelines';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->modifyQueryUsing(function (Builder $query): Builder {
                /** @var Builder<Pipeline> $query */
                return $query->when(
                    PipelineArchivingFeature::active(),
                    fn (Builder $query): Builder => $query->withoutArchived(),
                );
            })
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('createdBy.name')->label('Created By'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->url(fn (): string => PipelineResource::getUrl('create', ['project' => $this->getOwnerRecord()]))
                    ->authorize(fn (): bool => auth()->user()->can('create', [Pipeline::class, $this->getOwnerRecord()])),
            ])
            ->filters([
                Filter::make('createdBy')
                    ->label('My Pipelines')
                    ->default()
                    ->query(fn (Builder $query) => $query->where('created_by_id', auth()->id())),
            ])
            ->recordActions([
                ViewAction::make()
                    ->url(fn (Pipeline $record): string => PipelineResource::getUrl('view', [
                        'record' => $record,
                        'project' => $this->getOwnerRecord(),
                    ])),
                EditAction::make()
                    ->url(fn (Pipeline $record): string => PipelineResource::getUrl('edit', [
                        'record' => $record,
                        'project' => $this->getOwnerRecord(),
                    ])),
                ArchiveAction::make()
                    ->visible(fn (): bool => PipelineArchivingFeature::active())
                    ->authorize(fn (Pipeline $record): bool => auth()->user()?->can('delete', $record) ?? false)
                    ->modalHeading('Archive Pipeline')
                    ->modalDescription('Are you sure you want to archive this pipeline? All related milestones and tasks will also be archived.')
                    ->modalSubmitActionLabel('Archive')
                    ->using(fn (Pipeline $record): bool => DB::transaction(fn (): bool => $record->archive()))
                    ->successRedirectUrl(null),
            ]);
    }
}
