<?php

use AidingApp\Project\Filament\Resources\ProjectResource\Pages\ManageTasks;
use AidingApp\Project\Models\Project;
use AidingApp\Task\Models\Task;
use Filament\Forms\Components\Select;
use Filament\Tables\Actions\AssociateAction;

use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

it('can list tasks', function () {
    asSuperAdmin();

    $project = Project::factory()->create();

    Task::factory()->count(5)->for($project)->create();

    livewire(ManageTasks::class, [
        'record' => $project->getRouteKey(),
    ])
        ->assertCanSeeTableRecords($project->tasks);
});

it('Task which already associated with project do not show up in task search results', function () {
    asSuperAdmin();

    $project = Project::factory()->create();

    $secondProject = Project::factory()->create();

    $task = Task::factory()->create();

    livewire(ManageTasks::class, [
        'record' => $project->getRouteKey(),
    ])
        ->mountTableAction(AssociateAction::class)
        ->assertFormFieldExists('recordId', 'mountedTableActionForm', function (Select $select) use ($task) {
            $options = $select->getSearchResults($task->title);

            return ! empty($options);
        })->assertSuccessful();

    $task->project()->associate($project)->save();

    livewire(ManageTasks::class, [
        'record' => $project->getRouteKey(),
    ])
        ->mountTableAction(AssociateAction::class)
        ->assertFormFieldExists('recordId', 'mountedTableActionForm', function (Select $select) use ($task) {
            $options = $select->getSearchResults($task->title);

            return empty($options);
        })->assertSuccessful();
});
