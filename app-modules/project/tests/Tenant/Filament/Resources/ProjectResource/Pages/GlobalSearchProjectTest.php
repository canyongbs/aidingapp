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
      of the licensor in the software. Any use of the licensor's trademarks is subject
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

use AidingApp\Department\Models\Department;
use AidingApp\Project\Filament\Resources\Projects\ProjectResource;
use AidingApp\Project\Models\Project;
use App\Models\User;
use Illuminate\Contracts\Support\Htmlable;

use function Pest\Laravel\actingAs;
use function Tests\asSuperAdmin;

test('projects are globally searchable by name only', function () {
    expect(ProjectResource::getGloballySearchableAttributes())->toBe(['name']);
});

test('global search returns projects created by the acting user', function () {
    $user = User::factory()->create();

    actingAs($user);

    $project = Project::factory()->create();

    $results = ProjectResource::getGlobalSearchEloquentQuery()->get();

    expect($results)->toHaveCount(1);
    expect($results->first()->getKey())->toBe($project->getKey());
});

test('global search returns projects where the user is a manager user', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    actingAs($otherUser);
    $project = Project::factory()->create();

    $project->managerUsers()->attach($user->getKey());

    actingAs($user);

    $results = ProjectResource::getGlobalSearchEloquentQuery()->get();

    expect($results)->toHaveCount(1);
    expect($results->first()->getKey())->toBe($project->getKey());
});

test('global search returns projects where the user is an auditor user', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    actingAs($otherUser);
    $project = Project::factory()->create();

    $project->auditorUsers()->attach($user->getKey());

    actingAs($user);

    $results = ProjectResource::getGlobalSearchEloquentQuery()->get();

    expect($results)->toHaveCount(1);
    expect($results->first()->getKey())->toBe($project->getKey());
});

test('global search returns projects where the user is in a manager department', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $department = Department::factory()->create();

    $user->department()->associate($department)->save();
    $user->refresh();

    actingAs($otherUser);
    $project = Project::factory()->create();

    $project->managerDepartments()->attach($department->getKey());

    actingAs($user);

    $results = ProjectResource::getGlobalSearchEloquentQuery()->get();

    expect($results)->toHaveCount(1);
    expect($results->first()->getKey())->toBe($project->getKey());
});

test('global search returns projects where the user is in an auditor department', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $department = Department::factory()->create();

    $user->department()->associate($department)->save();
    $user->refresh();

    actingAs($otherUser);
    $project = Project::factory()->create();

    $project->auditorDepartments()->attach($department->getKey());

    actingAs($user);

    $results = ProjectResource::getGlobalSearchEloquentQuery()->get();

    expect($results)->toHaveCount(1);
    expect($results->first()->getKey())->toBe($project->getKey());
});

test('global search returns all projects for a super admin', function () {
    $creator = User::factory()->create();

    actingAs($creator);
    Project::factory()->count(2)->create();

    asSuperAdmin();

    $results = ProjectResource::getGlobalSearchEloquentQuery()->get();

    expect($results)->toHaveCount(2);
});

test('global search does not return projects the user has no relation to', function () {
    $creator = User::factory()->create();
    $unrelatedUser = User::factory()->create();

    actingAs($creator);
    Project::factory()->create();

    actingAs($unrelatedUser);

    $results = ProjectResource::getGlobalSearchEloquentQuery()->get();

    expect($results)->toHaveCount(0);
});

test('global search result URL points to the project view page', function () {
    $user = User::factory()->create();

    $user->givePermissionTo('project.view-any');
    $user->givePermissionTo('project.*.view');

    actingAs($user);

    $project = Project::factory()->create();

    $url = ProjectResource::getGlobalSearchResultUrl($project);

    expect($url)->toContain('/projects/' . $project->getKey());
    expect($url)->not->toContain('/edit');
});

test('global search result details include department, start date, and target go-live', function () {
    $user = User::factory()->create();

    actingAs($user);

    $department = Department::factory()->create(['name' => 'Information Technology']);

    $project = Project::factory()->create([
        'department_id' => $department->getKey(),
        'start_date' => '2026-01-15',
        'target_completion_date' => '2026-06-30',
    ]);

    $details = ProjectResource::getGlobalSearchResultDetails($project);

    expect($details)->toBe([
        'Department' => 'Information Technology',
        'Start Date' => 'Jan 15, 2026',
        'Target Go-Live' => 'Jun 30, 2026',
    ]);
});

test('global search result details fall back to N/A and Indefinite when fields are empty', function () {
    $user = User::factory()->create();

    actingAs($user);

    $project = Project::factory()->create([
        'department_id' => null,
        'start_date' => null,
        'target_completion_date' => null,
    ]);

    $details = ProjectResource::getGlobalSearchResultDetails($project);

    expect($details)->toBe([
        'Department' => 'N/A',
        'Start Date' => 'N/A',
        'Target Go-Live' => 'Indefinite',
    ]);
});

test('global search result title renders the project icon and name', function () {
    $user = User::factory()->create();

    actingAs($user);

    $project = Project::factory()->create([
        'name' => 'Campus Wifi Rollout',
        'icon' => 'heroicon-o-folder',
    ]);

    $title = ProjectResource::getGlobalSearchResultTitle($project);

    expect($title)->toBeInstanceOf(Htmlable::class);

    $html = $title->toHtml();

    expect($html)->toContain('Campus Wifi Rollout');
    expect($html)->toContain('heroicon-o-folder');
});
