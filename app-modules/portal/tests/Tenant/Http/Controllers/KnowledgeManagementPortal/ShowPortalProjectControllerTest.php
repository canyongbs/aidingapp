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

use AidingApp\Contact\Models\Contact;
use AidingApp\Portal\Settings\PortalSettings;
use AidingApp\Project\Enums\PipelineStageClassification;
use AidingApp\Project\Models\Pipeline;
use AidingApp\Project\Models\PipelineEntry;
use AidingApp\Project\Models\PipelineStage;
use AidingApp\Project\Models\Project;
use AidingApp\Project\Models\ProjectMilestone;
use App\Settings\LicenseSettings;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\getJson;

beforeEach(function () {
    $portalSettings = app(PortalSettings::class);
    $portalSettings->knowledge_management_portal_enabled = true;
    $portalSettings->save();

    $licenseSettings = app(LicenseSettings::class);
    $licenseSettings->data->addons->projectManagement = true;
    $licenseSettings->save();
});

it('shows guest-visible pipeline tasks grouped by milestone', function () {
    $contact = Contact::factory()->create();
    $project = Project::factory()->create(['name' => 'Office Renovation']);
    $project->guestContacts()->attach($contact);

    $pipeline = Pipeline::factory()->for($project)->create(['name' => 'Renovation Pipeline']);
    $planningStage = PipelineStage::factory()->for($pipeline)->create([
        'name' => 'Planning',
        'classification' => PipelineStageClassification::Planning,
    ]);
    $completeStage = PipelineStage::factory()->for($pipeline)->create([
        'name' => 'Complete',
        'classification' => PipelineStageClassification::Complete,
    ]);
    $activeMilestone = ProjectMilestone::factory()->for($project)->create(['title' => 'Interior Areas']);
    $emptyMilestone = ProjectMilestone::factory()->for($project)->create(['title' => 'Security Systems']);
    $hiddenMilestone = ProjectMilestone::factory()->for($project)->create(['title' => 'Internal Work']);

    $planningEntry = PipelineEntry::factory()
        ->for($planningStage, 'pipelineStage')
        ->for($activeMilestone, 'milestone')
        ->create([
            'name' => 'Install Door Locks',
            'start_date' => '2026-08-01 09:00:00',
            'due' => '2026-08-28 17:00:00',
        ]);
    $completeEntry = PipelineEntry::factory()
        ->for($completeStage, 'pipelineStage')
        ->for($activeMilestone, 'milestone')
        ->create(['name' => 'Measure Door Frames']);
    PipelineEntry::factory()
        ->for($completeStage, 'pipelineStage')
        ->for($activeMilestone, 'milestone')
        ->create([
            'name' => 'Internal Review',
            'is_visible_to_guests' => false,
        ]);
    PipelineEntry::factory()
        ->for($planningStage, 'pipelineStage')
        ->for($hiddenMilestone, 'milestone')
        ->create(['is_visible_to_guests' => false]);
    $unassignedEntry = PipelineEntry::factory()->for($planningStage, 'pipelineStage')->create([
        'name' => 'Order Coffee Maker',
        'project_milestone_id' => null,
    ]);

    actingAs($contact, 'contact');

    getJson(route('api.portal.projects.show', ['project' => $project->getKey()]))
        ->assertOk()
        ->assertJsonPath('data.id', $project->getKey())
        ->assertJsonPath('data.name', 'Office Renovation')
        ->assertJsonPath('data.pipelines.0.id', $pipeline->getKey())
        ->assertJsonPath('data.pipelines.0.name', 'Renovation Pipeline')
        ->assertJsonPath('data.pipelines.0.groups.0.milestone_id', $activeMilestone->getKey())
        ->assertJsonPath('data.pipelines.0.groups.0.milestone_title', 'Interior Areas')
        ->assertJsonPath('data.pipelines.0.groups.0.progress_percentage', 67)
        ->assertJsonPath('data.pipelines.0.groups.0.entries.0.id', $planningEntry->getKey())
        ->assertJsonPath('data.pipelines.0.groups.0.entries.0.stage', 'Planning')
        ->assertJsonPath('data.pipelines.0.groups.0.entries.0.start_date', '2026-08-01')
        ->assertJsonPath('data.pipelines.0.groups.0.entries.0.due', '2026-08-28')
        ->assertJsonPath('data.pipelines.0.groups.0.entries.1.id', $completeEntry->getKey())
        ->assertJsonPath('data.pipelines.0.groups.1.milestone_id', $hiddenMilestone->getKey())
        ->assertJsonPath('data.pipelines.0.groups.1.progress_percentage', 0)
        ->assertJsonPath('data.pipelines.0.groups.1.entries', [])
        ->assertJsonPath('data.pipelines.0.groups.2.milestone_id', $emptyMilestone->getKey())
        ->assertJsonPath('data.pipelines.0.groups.2.progress_percentage', 0)
        ->assertJsonPath('data.pipelines.0.groups.2.entries', [])
        ->assertJsonPath('data.pipelines.0.groups.3.milestone_id', null)
        ->assertJsonPath('data.pipelines.0.groups.3.milestone_title', 'No Associated Milestone')
        ->assertJsonPath('data.pipelines.0.groups.3.progress_percentage', null)
        ->assertJsonPath('data.pipelines.0.groups.3.entries.0.id', $unassignedEntry->getKey())
        ->assertJsonMissing(['name' => 'Internal Review']);
});

it('does not show archived pipelines tasks or milestones', function () {
    $contact = Contact::factory()->create();
    $project = Project::factory()->create();
    $project->guestContacts()->attach($contact);

    $pipeline = Pipeline::factory()->for($project)->create(['name' => 'Active Pipeline']);
    $stage = PipelineStage::factory()->for($pipeline)->create();
    $milestone = ProjectMilestone::factory()->for($project)->create(['title' => 'Active Milestone']);
    PipelineEntry::factory()
        ->for($stage, 'pipelineStage')
        ->for($milestone, 'milestone')
        ->create(['name' => 'Visible Task']);

    $archivedEntry = PipelineEntry::factory()->for($stage, 'pipelineStage')->create(['name' => 'Archived Task']);
    $archivedEntry->archive();

    $archivedMilestone = ProjectMilestone::factory()->for($project)->create(['title' => 'Archived Milestone']);
    PipelineEntry::factory()
        ->for($stage, 'pipelineStage')
        ->for($archivedMilestone, 'milestone')
        ->create(['name' => 'Task In Archived Milestone']);
    $archivedMilestone->archive();

    $archivedPipeline = Pipeline::factory()->for($project)->create(['name' => 'Archived Pipeline']);
    PipelineStage::factory()->for($archivedPipeline)->create();
    $archivedPipeline->archive();

    actingAs($contact, 'contact');

    getJson(route('api.portal.projects.show', ['project' => $project->getKey()]))
        ->assertOk()
        ->assertJsonCount(1, 'data.pipelines')
        ->assertJsonPath('data.pipelines.0.name', 'Active Pipeline')
        ->assertJsonPath('data.pipelines.0.groups.0.milestone_id', $milestone->getKey())
        ->assertJsonMissing(['name' => 'Archived Task'])
        ->assertJsonMissing(['name' => 'Task In Archived Milestone'])
        ->assertJsonMissing(['name' => 'Archived Pipeline']);
});

it('does not disclose an archived project', function () {
    $contact = Contact::factory()->create();
    $project = Project::factory()->create();
    $project->guestContacts()->attach($contact);
    $project->archive();

    actingAs($contact, 'contact');

    getJson(route('api.portal.projects.show', ['project' => $project->getKey()]))
        ->assertNotFound();
});

it('requires contact authentication', function () {
    $project = Project::factory()->create();

    getJson(route('api.portal.projects.show', ['project' => $project->getKey()]))
        ->assertUnauthorized();
});

it('denies access when Project Management is not licensed', function () {
    $licenseSettings = app(LicenseSettings::class);
    $licenseSettings->data->addons->projectManagement = false;
    $licenseSettings->save();

    $contact = Contact::factory()->create();
    $project = Project::factory()->create();
    $project->guestContacts()->attach($contact);

    actingAs($contact, 'contact');

    getJson(route('api.portal.projects.show', ['project' => $project->getKey()]))
        ->assertForbidden();
});

it('does not disclose an inaccessible project', function () {
    $contact = Contact::factory()->create();
    $project = Project::factory()->create();

    actingAs($contact, 'contact');

    getJson(route('api.portal.projects.show', ['project' => $project->getKey()]))
        ->assertNotFound();
});
