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

use AidingApp\Ai\Settings\AiSupportAssistantSettings;
use AidingApp\Portal\Filament\Pages\ManagePortalSettings;
use AidingApp\Portal\Settings\AiDisclaimerSettings;
use AidingApp\Portal\Settings\PortalSettings;
use App\Features\AiDisclaimerFeature;
use App\Models\User;
use App\Settings\LicenseSettings;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

it('is gated with proper access control', function (): void {
    $settings = app(LicenseSettings::class);
    $settings->data->addons->knowledgeManagement = false;
    $settings->save();

    $user = User::factory()->create();

    actingAs($user);

    Livewire::test(ManagePortalSettings::class)->assertForbidden();

    $user->givePermissionTo('settings.view-any');
    $user->refresh();

    Livewire::test(ManagePortalSettings::class)->assertForbidden();

    $settings->data->addons->knowledgeManagement = true;
    $settings->save();

    Livewire::test(ManagePortalSettings::class)->assertOk();
});

it('shows the AI disclaimer fields only when the AI Support Assistant is enabled', function (): void {
    $aiSettings = app(AiSupportAssistantSettings::class);
    $aiSettings->is_enabled = true;
    $aiSettings->save();

    $user = User::factory()->create();
    $user->givePermissionTo('settings.view-any');

    actingAs($user);

    $portalSettings = app(PortalSettings::class);
    $portalSettings->knowledge_management_portal_enabled = true;
    $portalSettings->ai_support_assistant = false;
    $portalSettings->save();

    Livewire::test(ManagePortalSettings::class)
        ->assertFormFieldHidden('ai_disclaimer_enabled')
        ->assertFormFieldHidden('ai_disclaimer_text');

    $portalSettings->ai_support_assistant = true;
    $portalSettings->save();

    Livewire::test(ManagePortalSettings::class)
        ->assertFormFieldVisible('ai_disclaimer_enabled')
        ->assertFormFieldVisible('ai_disclaimer_text')
        ->fillForm(['ai_disclaimer_enabled' => false])
        ->assertFormFieldHidden('ai_disclaimer_text');
});

it('saves a customized AI disclaimer text', function (): void {
    $aiSettings = app(AiSupportAssistantSettings::class);
    $aiSettings->is_enabled = true;
    $aiSettings->save();

    $licenseSettings = app(LicenseSettings::class);
    $licenseSettings->data->addons->knowledgeManagement = true;
    $licenseSettings->save();

    $user = User::factory()->create();
    $user->givePermissionTo(['settings.view-any', 'settings.*.update']);

    actingAs($user);

    $portalSettings = app(PortalSettings::class);
    $portalSettings->knowledge_management_portal_enabled = true;
    $portalSettings->ai_support_assistant = true;
    $portalSettings->save();

    Livewire::test(ManagePortalSettings::class)
        ->fillForm(['ai_disclaimer_text' => 'Custom disclaimer.'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect(app(AiDisclaimerSettings::class)->text)->toBe('Custom disclaimer.');

    Livewire::test(ManagePortalSettings::class)
        ->fillForm(['ai_disclaimer_text' => ''])
        ->call('save')
        ->assertHasFormErrors(['ai_disclaimer_text' => 'required']);
});

it('saves portal settings before the AI disclaimer settings exist', function (): void {
    AiDisclaimerFeature::deactivate();

    DB::table('settings')->where('group', 'portal_ai_disclaimer')->delete();

    $licenseSettings = app(LicenseSettings::class);
    $licenseSettings->data->addons->knowledgeManagement = true;
    $licenseSettings->save();

    $user = User::factory()->create();
    $user->givePermissionTo(['settings.view-any', 'settings.*.update']);

    actingAs($user);

    $portalSettings = app(PortalSettings::class);
    $portalSettings->knowledge_management_portal_enabled = true;
    $portalSettings->save();

    Livewire::test(ManagePortalSettings::class)
        ->assertFormFieldHidden('ai_disclaimer_enabled')
        ->fillForm(['page_title' => 'Updated Help Center'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect(app(PortalSettings::class)->page_title)->toBe('Updated Help Center');
});
