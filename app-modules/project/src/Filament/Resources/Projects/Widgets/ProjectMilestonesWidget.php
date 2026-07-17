<?php

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

class ProjectMilestonesWidget extends TableWidget
{
    public Project $record;

    protected int | string | array $columnSpan = 'full';

    protected string $view = 'project::filament.resources.projects.widgets.project-milestones-widget';

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => $this->record->milestones()->getQuery())
            ->recordTitleAttribute('title')
            ->heading(null)
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
                    ->default('N/A')
                    ->label('Created By'),
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

    public function manageMilestoneCreateAction(): Action
    {
        return Action::make('manageMilestoneCreate')
            ->label('Create Milestone')
            ->slideOver()
            ->schema($this->formSchema())
            ->authorize('create', $this->record)
            ->action(function (array $data): void {
                $this->record->milestones()->create($data);

                Notification::make()
                    ->success()
                    ->title('Milestone created')
                    ->send();
            });
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
