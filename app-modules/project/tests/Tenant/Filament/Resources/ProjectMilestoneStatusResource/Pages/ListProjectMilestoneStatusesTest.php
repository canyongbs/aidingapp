<?php

use AidingApp\Authorization\Enums\LicenseType;
use AidingApp\Project\Filament\Resources\ProjectMilestoneStatusResource\Pages\ListProjectMilestoneStatuses;
use App\Models\User;
use App\Settings\LicenseSettings;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('is gated with proper access control', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->projectManagement = false;
    $settings->save();

    $user = User::factory()->licensed(LicenseType::cases())->create();

    $user->givePermissionTo('settings.view-any');

    actingAs($user);

    get(ListProjectMilestoneStatuses::getUrl())->assertForbidden();

    $settings->data->addons->projectManagement = true;
    $settings->save();

    $user->revokePermissionTo('settings.view-any');

    get(ListProjectMilestoneStatuses::getUrl())->assertForbidden();

    $user->givePermissionTo('settings.view-any');

    get(ListProjectMilestoneStatuses::getUrl())->assertSuccessful();
});