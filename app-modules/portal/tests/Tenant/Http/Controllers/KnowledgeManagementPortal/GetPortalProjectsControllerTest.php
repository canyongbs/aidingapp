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
use AidingApp\Contact\Models\Organization;
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

it('lists directly assigned projects with progress based on non-archived tasks', function () {
    $contact = Contact::factory()->create();
    $project = Project::factory()->create([
        'name' => 'Campus Network Upgrade',
        'description' => 'Upgrade the campus network.',
        'start_date' => '2026-06-15',
        'target_completion_date' => '2026-12-01',
    ]);
    $project->guestContacts()->attach($contact);

    $pipeline = Pipeline::factory()->for($project)->create();
    $planningStage = PipelineStage::factory()->for($pipeline)->create([
        'classification' => PipelineStageClassification::Planning,
    ]);
    $completeStage = PipelineStage::factory()->for($pipeline)->create([
        'classification' => PipelineStageClassification::Complete,
    ]);

    PipelineEntry::factory()->for($planningStage, 'pipelineStage')->create();
    PipelineEntry::factory()->for($completeStage, 'pipelineStage')->create();
    PipelineEntry::factory()->for($completeStage, 'pipelineStage')->create([
        'is_visible_to_guests' => false,
    ]);
    $archivedEntry = PipelineEntry::factory()->for($completeStage, 'pipelineStage')->create();
    $archivedEntry->archive();
    $archivedMilestone = ProjectMilestone::factory()->for($project)->create();
    PipelineEntry::factory()
        ->for($completeStage, 'pipelineStage')
        ->for($archivedMilestone, 'milestone')
        ->create();
    $archivedMilestone->archive();

    Project::factory()->create(['name' => 'Unrelated Project']);

    actingAs($contact, 'contact');

    getJson(route('api.portal.projects.index'))
        ->assertOk()
        ->assertJsonPath('data.0.id', $project->getKey())
        ->assertJsonPath('data.0.name', 'Campus Network Upgrade')
        ->assertJsonPath('data.0.description', 'Upgrade the campus network.')
        ->assertJsonPath('data.0.start_date', '2026-06-15')
        ->assertJsonPath('data.0.target_completion_date', '2026-12-01')
        ->assertJsonPath('data.0.progress_percentage', 67)
        ->assertJsonPath('meta.current_page', 1)
        ->assertJsonPath('meta.last_page', 1)
        ->assertJsonPath('meta.from', 1)
        ->assertJsonPath('meta.to', 1)
        ->assertJsonPath('meta.total', 1)
        ->assertJsonPath('meta.per_page', 10);
});

it('paginates assigned projects', function () {
    $contact = Contact::factory()->create();

    $projects = collect(range(1, 11))->map(function (int $number) use ($contact): Project {
        $project = Project::factory()->create([
            'name' => 'Project ' . str_pad((string) $number, 2, '0', STR_PAD_LEFT),
        ]);
        $project->guestContacts()->attach($contact);

        return $project;
    });

    actingAs($contact, 'contact');

    getJson(route('api.portal.projects.index', ['page' => 2]))
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $projects->last()->getKey())
        ->assertJsonPath('meta.current_page', 2)
        ->assertJsonPath('meta.last_page', 2)
        ->assertJsonPath('meta.from', 11)
        ->assertJsonPath('meta.to', 11)
        ->assertJsonPath('meta.total', 11)
        ->assertJsonPath('meta.per_page', 10);
});

it('does not list archived projects', function () {
    $contact = Contact::factory()->create();
    $project = Project::factory()->create();
    $project->guestContacts()->attach($contact);
    $project->archive();

    actingAs($contact, 'contact');

    getJson(route('api.portal.projects.index'))
        ->assertOk()
        ->assertJsonPath('data', []);
});

it('lists organization projects once when the contact is also a direct guest', function () {
    $organization = Organization::factory()->create();
    $contact = Contact::factory()->for($organization)->create();
    $project = Project::factory()->create();
    $project->guestContacts()->attach($contact);
    $project->guestOrganizations()->attach($organization);

    actingAs($contact, 'contact');

    getJson(route('api.portal.projects.index'))
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $project->getKey());
});

it('requires contact authentication', function () {
    getJson(route('api.portal.projects.index'))->assertUnauthorized();
});

it('denies access when Project Management is not licensed', function () {
    $licenseSettings = app(LicenseSettings::class);
    $licenseSettings->data->addons->projectManagement = false;
    $licenseSettings->save();

    actingAs(Contact::factory()->create(), 'contact');

    getJson(route('api.portal.projects.index'))->assertForbidden();
});
