<?php

namespace AidingApp\Project\Filament\Resources\Projects\Pages\Concerns;

use AidingApp\Project\Filament\Resources\Projects\ProjectResource;
use AidingApp\Project\Models\Project;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Illuminate\Contracts\View\View;

trait HasProjectDashboardHeader
{
    public function getHeader(): ?View
    {
        $project = $this->getRecord();
        assert($project instanceof Project);

        return view('project::filament.resources.projects.view-project.header', [
            'actions' => $this->getCachedHeaderActions(),
            'breadcrumbs' => $this->getBreadcrumbs(),
            'project' => $project,
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->label('Edit Project')
                ->icon('heroicon-o-pencil')
                ->outlined()
                ->color('info'),
            Action::make('manageAccess')
                ->label('Manage Access')
                ->icon('heroicon-o-user-group')
                ->url(fn (): string => ProjectResource::getUrl('manage-managers', ['record' => $this->getRecord()]))
                ->outlined()
                ->color('info'),
            Action::make('settings')
                ->label('Settings')
                ->icon('heroicon-o-cog-6-tooth')
                ->url(fn (): string => ProjectResource::getUrl('edit', ['record' => $this->getRecord()]))
                ->outlined()
                ->color('info'),
        ];
    }
}
