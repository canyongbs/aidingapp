<?php

namespace AidingApp\Project\Filament\Resources\Projects\Widgets;

use AidingApp\Project\Filament\Actions\ProjectManageAccessAction;
use AidingApp\Project\Models\Project;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;

class ProjectAccessWidget extends Widget implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public Project $record;

    protected string $view = 'project::filament.resources.projects.widgets.project-access-widget';

    #[Computed]
    public function getManagers(): Collection
    {
        return $this->record->managerUsers;
    }

    #[Computed]
    public function getAuditors(): Collection
    {
        return $this->record->auditorUsers;
    }

    #[Computed]
    public function getGuests(): Collection
    {
        return $this->record->guestContacts;
        // ->concat($this->record->guestOrganizations);
    }

    public function manageAccessAction(): Action
    {
        return ProjectManageAccessAction::make('manage_access')
            ->record($this->record);
    }
}
