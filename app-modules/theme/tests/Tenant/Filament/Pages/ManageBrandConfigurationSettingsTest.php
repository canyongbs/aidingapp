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

use AidingApp\Theme\Filament\Pages\ManageBrandConfigurationSettings;
use AidingApp\Theme\Settings\ThemeSettings;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

it('allows a super admin to access the branding page', function () {
    asSuperAdmin();

    get(ManageBrandConfigurationSettings::getUrl())
        ->assertSuccessful();
});

it('can update the profile menu link and login and home target settings', function () {
    asSuperAdmin();

    livewire(ManageBrandConfigurationSettings::class)
        ->fillForm([
            'is_support_url_enabled' => true,
            'support_url' => 'https://example.com/support',
            'is_recent_updates_url_enabled' => true,
            'recent_updates_url' => 'https://example.com/updates',
            'changelog_url' => 'https://example.com/changelog',
            'product_resource_hub_url' => 'https://example.com/hub',
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $settings = app(ThemeSettings::class);
    $settings->refresh();

    expect($settings->is_support_url_enabled)->toBeTrue()
        ->and($settings->support_url)->toBe('https://example.com/support')
        ->and($settings->is_recent_updates_url_enabled)->toBeTrue()
        ->and($settings->recent_updates_url)->toBe('https://example.com/updates')
        ->and($settings->changelog_url)->toBe('https://example.com/changelog')
        ->and($settings->product_resource_hub_url)->toBe('https://example.com/hub');
});

it('hides the support url input when the support toggle is disabled', function () {
    asSuperAdmin();

    livewire(ManageBrandConfigurationSettings::class)
        ->fillForm(['is_support_url_enabled' => true])
        ->assertFormFieldVisible('support_url')
        ->fillForm(['is_support_url_enabled' => false])
        ->assertFormFieldHidden('support_url');
});

it('hides the recent updates url input when the recent updates toggle is disabled', function () {
    asSuperAdmin();

    livewire(ManageBrandConfigurationSettings::class)
        ->fillForm(['is_recent_updates_url_enabled' => true])
        ->assertFormFieldVisible('recent_updates_url')
        ->fillForm(['is_recent_updates_url_enabled' => false])
        ->assertFormFieldHidden('recent_updates_url');
});

describe('authorization', function () {
    it('denies access to the branding page for a non super admin', function () {
        actingAs(User::factory()->create());

        get(ManageBrandConfigurationSettings::getUrl())
            ->assertForbidden();
    });
});
