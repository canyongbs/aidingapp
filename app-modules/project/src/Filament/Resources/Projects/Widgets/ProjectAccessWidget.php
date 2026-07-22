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

use AidingApp\Contact\Models\Contact;
use AidingApp\Contact\Models\Organization;
use AidingApp\Department\Models\Department;
use AidingApp\Project\Filament\Actions\ProjectManageAccessAction;
use AidingApp\Project\Models\Project;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;

class ProjectAccessWidget extends Widget implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    #[Locked]
    public Project $record;

    protected string $view = 'project::filament.resources.projects.widgets.project-access-widget';

    public static function canView(): bool
    {
        $user = auth()->user();

        return $user->can('viewAny', Project::class);
    }

    #[On('projectAccessUpdated')]
    public function refreshAccess(): void {}

    /**
     * @return Collection<int, User>
     */
    #[Computed]
    public function managers(): Collection
    {
        return $this->record->managerUsers
            ->concat($this->record->managerDepartments->flatMap(fn (Department $department): Collection => $department->users))
            ->unique('id')
            ->values();
    }

    /**
     * @return Collection<int, User>
     */
    #[Computed]
    public function auditors(): Collection
    {
        return $this->record->auditorUsers
            ->concat($this->record->auditorDepartments->flatMap(fn (Department $department): Collection => $department->users))
            ->unique('id')
            ->values();
    }

    /**
     * @return Collection<int, Contact | Organization>
     */
    #[Computed]
    public function guests(): Collection
    {
        /** @var Collection<int, Contact | Organization> $guests */
        $guests = $this->record->guestContacts
            ->concat($this->record->guestOrganizations);

        return $guests;
    }

    public function manageAccessAction(): Action
    {
        return ProjectManageAccessAction::make('manageAccess')
            ->record($this->record);
    }
}
