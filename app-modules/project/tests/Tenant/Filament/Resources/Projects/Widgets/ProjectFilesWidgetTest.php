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

use AidingApp\Project\Filament\Resources\Projects\Widgets\ProjectFilesWidget;
use AidingApp\Project\Models\Project;
use AidingApp\Project\Models\ProjectFile;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Tests\asSuperAdmin;

beforeEach(function (): void {
    Storage::fake('s3');
});

it('downloads a file by redirecting to its media url', function (): void {
    asSuperAdmin();

    $project = Project::factory()->create();
    $file = ProjectFile::factory()->state(['project_id' => $project->id])->create();
    $file->addMedia(UploadedFile::fake()->image('plan.png'))->usingName('Launch Plan')->toMediaCollection('file');

    Livewire::test(ProjectFilesWidget::class, ['record' => $project])
        ->callAction(TestAction::make('download')->table($file))
        ->assertRedirectContains($file->getFirstMedia('file')->file_name);
});

it('hides the download action for a file without media', function (): void {
    asSuperAdmin();

    $project = Project::factory()->create();
    $file = ProjectFile::factory()->state(['project_id' => $project->id])->create();

    Livewire::test(ProjectFilesWidget::class, ['record' => $project])
        ->assertActionHidden(TestAction::make('download')->table($file));
});

describe('authorization', function (): void {
    it('does not let a user without view access download a file', function (): void {
        $project = Project::factory()->create();
        $file = ProjectFile::factory()->state(['project_id' => $project->id])->create();
        $file->addMedia(UploadedFile::fake()->image('plan.png'))->toMediaCollection('file');

        $user = User::factory()->create();
        $user->givePermissionTo('project.view-any');

        actingAs($user);

        Livewire::test(ProjectFilesWidget::class, ['record' => $project])
            ->assertActionHidden(TestAction::make('download')->table($file));
    });
});
