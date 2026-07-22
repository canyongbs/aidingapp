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

namespace AidingApp\Project\Filament\Resources\Projects\Widgets;

use AidingApp\Project\Models\Project;
use AidingApp\Project\Models\ProjectMilestoneStatus;
use App\Filament\Tables\Columns\IdColumn;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Component;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Locked;

class ProjectMilestonesWidget extends TableWidget
{
    #[Locked]
    public Project $record;

    protected int | string | array $columnSpan = 'full';

    protected string $view = 'project::filament.resources.projects.widgets.project-milestones-widget';

    public static function canView(): bool
    {
        $user = auth()->user();

        return $user->can('viewAny', Project::class);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => $this->record->milestones()->getQuery())
            ->recordTitleAttribute('title')
            ->heading('Project Milestones')
            ->paginated([5, 10, 25])
            ->defaultPaginationPageOption(5)
            ->columns([
                IdColumn::make(),
                TextColumn::make('title'),
                TextColumn::make('description'),
                TextColumn::make('status.name')
                    ->label('Status')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'Completed', 'Verified', 'Closed' => 'success',
                        'In Progress', 'Planned' => 'info',
                        'At Risk', 'Delayed' => 'warning',
                        'Cancelled' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime(),
                TextColumn::make('createdBy.name')
                    ->label('Created By')
                    ->placeholder('N/A'),
            ])
            ->headerActions([
                Action::make('createMilestone')
                    ->label('New Milestone')
                    ->slideOver()
                    ->schema($this->formSchema())
                    ->authorize('create', $this->record)
                    ->action(function (array $data): void {
                        $this->record->milestones()->create($data);

                        Notification::make()
                            ->success()
                            ->title('Milestone created')
                            ->send();
                    }),
            ])
            ->recordActions([
                EditAction::make()
                    ->slideOver()
                    ->schema($this->formSchema())
                    ->authorize('update', $this->record),
                DeleteAction::make()
                    ->authorize('update', $this->record),
            ]);
    }

    /**
     * @return array<int, Component>
     */
    protected function formSchema(): array
    {
        return [
            TextInput::make('title')
                ->required()
                ->maxLength(255),
            Textarea::make('description')
                ->required()
                ->maxLength(65535),
            Select::make('status_id')
                ->label('Status')
                ->searchable()
                ->required()
                ->options(fn (): array => ProjectMilestoneStatus::query()
                    ->orderBy('name')
                    ->pluck('name', 'id')
                    ->all()),
            DatePicker::make('target_date'),
        ];
    }
}
