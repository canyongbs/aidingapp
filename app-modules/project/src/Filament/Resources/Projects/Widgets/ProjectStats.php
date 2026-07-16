<?php

namespace AidingApp\Project\Filament\Resources\Projects\Widgets;

use AidingApp\Project\Models\Project;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ProjectStats extends BaseWidget
{
    public Project $record;

    protected function getStats(): array
    {
        $project = $this->record;

        return [
            Stat::make('files', $project->files()->count())
                ->label('Files'),
            Stat::make('tasks', $project->tasks()->count())
                ->label('Pipeline Tasks'),
            Stat::make('milestones', $project->milestones()->count())
                ->label('Milestones'),
            Stat::make('recent_activities', 0)
                ->label('Recent Activities'),
        ];
    }
}
