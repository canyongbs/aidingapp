<?php

namespace AidingApp\Project\Filament\Actions;

use Filament\Actions\Action;
use Illuminate\Contracts\View\View;

class ProjectManageAccessAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Manage Access')
            ->icon('heroicon-o-user-group')
            ->outlined()
            ->color('info')
            ->slideOver()
            ->modalHeading('Manage Project Access')
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Close')
            ->modalContent(fn (): View => view(
                'project::filament.resources.projects.widgets.manage-access-modal',
                ['project' => $this->record],
            ));
    }
}
