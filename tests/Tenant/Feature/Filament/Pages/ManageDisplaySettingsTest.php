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

use App\Filament\Pages\ManageDisplaySettings;
use App\Models\User;
use App\Settings\DisplaySettings;
use App\Settings\EmailSettings;
use App\Settings\PresenceSettings;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

it('renders the dates and times, email, and presence tabs', function (): void {
    $user = User::factory()->create();
    $user->givePermissionTo('settings.view-any');

    actingAs($user);

    Livewire::test(ManageDisplaySettings::class)
        ->assertOk()
        ->assertSet('activeTab', 'dates-and-times')
        ->assertSeeText('Dates and Times')
        ->assertSeeText('Email')
        ->assertSeeText('Presence');
});

it('opens the requested tab when it is valid and visible', function (): void {
    $user = User::factory()->create();
    $user->givePermissionTo('settings.view-any');

    actingAs($user);

    Livewire::withQueryParams(['tab' => 'email'])
        ->test(ManageDisplaySettings::class)
        ->assertOk()
        ->assertSet('activeTab', 'email');
});

it('falls back to the first visible tab when the requested tab is unknown', function (): void {
    $user = User::factory()->create();
    $user->givePermissionTo('settings.view-any');

    actingAs($user);

    Livewire::withQueryParams(['tab' => 'does-not-exist'])
        ->test(ManageDisplaySettings::class)
        ->assertOk()
        ->assertSet('activeTab', 'dates-and-times');
});

it('loads the existing dates and times settings', function (): void {
    $user = User::factory()->create();
    $user->givePermissionTo('settings.view-any');

    actingAs($user);

    $settings = app(DisplaySettings::class);
    $settings->timezone = 'America/Chicago';
    $settings->save();

    Livewire::test(ManageDisplaySettings::class)
        ->assertSchemaStateSet(['timezone' => 'America/Chicago'], 'datesAndTimesForm');
});

it('can update the dates and times settings', function (): void {
    $user = User::factory()->create();
    $user->givePermissionTo(['settings.view-any', 'settings.*.update']);

    actingAs($user);

    Livewire::test(ManageDisplaySettings::class)
        ->fillForm(['timezone' => 'America/New_York'], 'datesAndTimesForm')
        ->call('saveDatesAndTimes')
        ->assertHasNoFormErrors(form: 'datesAndTimesForm');

    expect(app(DisplaySettings::class)->timezone)->toBe('America/New_York');
});

it('can update the presence settings', function (): void {
    $user = User::factory()->create();
    $user->givePermissionTo(['settings.view-any', 'settings.*.update']);

    actingAs($user);

    Livewire::test(ManageDisplaySettings::class)
        ->fillForm([
            'active_threshold' => 10,
            'idle_threshold' => 20,
            'inactive_threshold' => 40,
        ], 'presenceForm')
        ->call('savePresence')
        ->assertHasNoFormErrors(form: 'presenceForm');

    $settings = app(PresenceSettings::class);

    expect($settings->active_threshold)->toBe(10)
        ->and($settings->idle_threshold)->toBe(20)
        ->and($settings->inactive_threshold)->toBe(40);
});

it('can update the email settings', function (): void {
    $user = User::factory()->create();
    $user->givePermissionTo(['settings.view-any', 'settings.*.update']);

    actingAs($user);

    Livewire::test(ManageDisplaySettings::class)
        ->fillForm([
            'tenant_mail_from_name' => 'Example From Name',
            'background_color' => '#ffffff',
        ], 'emailForm')
        ->call('saveEmail')
        ->assertHasNoFormErrors(form: 'emailForm');

    expect(app(EmailSettings::class)->background_color)->toBe('#ffffff');
});

describe('authorization', function (): void {
    it('denies access to the display settings page without the `settings.view-any` permission', function (): void {
        $user = User::factory()->create();

        actingAs($user);

        Livewire::test(ManageDisplaySettings::class)->assertForbidden();
    });

    it('allows access to the display settings page with the `settings.view-any` permission', function (): void {
        $user = User::factory()->create();
        $user->givePermissionTo('settings.view-any');

        actingAs($user);

        Livewire::test(ManageDisplaySettings::class)->assertOk();
    });

    it('does not update the dates and times settings without the `settings.*.update` permission', function (): void {
        $user = User::factory()->create();
        $user->givePermissionTo('settings.view-any');

        actingAs($user);

        $settings = app(DisplaySettings::class);
        $settings->timezone = 'America/Chicago';
        $settings->save();

        Livewire::test(ManageDisplaySettings::class)
            ->set('datesAndTimesData.timezone', 'America/New_York')
            ->call('saveDatesAndTimes')
            ->assertForbidden();

        expect(app(DisplaySettings::class)->timezone)->toBe('America/Chicago');
    });

    it('does not update the presence settings without the `settings.*.update` permission', function (): void {
        $user = User::factory()->create();
        $user->givePermissionTo('settings.view-any');

        actingAs($user);

        $settings = app(PresenceSettings::class);
        $settings->active_threshold = 5;
        $settings->save();

        Livewire::test(ManageDisplaySettings::class)
            ->set('presenceData.active_threshold', 15)
            ->call('savePresence')
            ->assertForbidden();

        expect(app(PresenceSettings::class)->active_threshold)->toBe(5);
    });

    it('does not update the email settings without the `settings.*.update` permission', function (): void {
        $user = User::factory()->create();
        $user->givePermissionTo('settings.view-any');

        actingAs($user);

        $settings = app(EmailSettings::class);
        $settings->background_color = '#000000';
        $settings->save();

        Livewire::test(ManageDisplaySettings::class)
            ->set('emailData.background_color', '#ffffff')
            ->call('saveEmail')
            ->assertForbidden();

        expect(app(EmailSettings::class)->background_color)->toBe('#000000');
    });
});
