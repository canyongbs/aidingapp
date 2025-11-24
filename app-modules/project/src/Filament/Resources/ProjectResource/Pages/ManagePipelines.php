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

namespace AidingApp\Project\Filament\Resources\ProjectResource\Pages;

use AidingApp\Project\Filament\Resources\ProjectResource;
use AidingApp\Project\Models\Pipeline;
use AidingApp\Project\Models\Project;
use App\Features\ProjectPipelineFeature;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ManagePipelines extends ManageRelatedRecords
{
    protected static string $resource = ProjectResource::class;

    protected static string $relationship = 'pipelines';

    public static function getNavigationLabel(): string
    {
        return 'Pipelines';
    }

    public static function canAccess(array $arguments = []): bool
    {
        $user = auth()->user();

        return ProjectPipelineFeature::active() && $user->can('viewAny', [Pipeline::class, $arguments['record']]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Textarea::make('description')
                    ->required()
                    ->maxLength(65535),
                TextInput::make('default_stage')
                    ->required()
                    ->label('Default Pipeline Stage name'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('createdBy.name')->label('Created By'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->authorize('create', $this->getOwnerRecord())
                    ->using(function (array $data, string $model): Model {
                        $project = Project::find($this->getOwnerRecord()->getKey());

                        if ($project && (! auth()->user()->can('update', $project))) {
                            $project = null;
                        }

                        $data['user_id'] = auth()->id();
                        $data['project_id'] = $project?->getKey();

                        return $model::create($data);
                    }),
            ])
            ->filters([
                Filter::make('createdBy')
                    ->label('My Pipelines')
                    ->default()
                    ->query(fn (Builder $query) => $query->where('user_id', auth()->id())),
            ])
            ->actions([
                EditAction::make()
                    ->authorize('update', $this->getOwnerRecord()),
                DeleteAction::make()
                    ->authorize('update', $this->getOwnerRecord()),
            ]);
    }
}
