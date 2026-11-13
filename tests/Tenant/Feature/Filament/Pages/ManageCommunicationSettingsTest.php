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

use AidingApp\Engagement\Models\EmailTemplate;
use App\Filament\Pages\ManageCommunicationSettings;
use App\Models\User;
use App\Settings\NotificationSettings;
use CanyonGBS\Common\Enums\Color;
use Filament\Actions\Testing\TestAction;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

it('renders the notifications and email templates tabs', function (): void {
    $user = User::factory()->create();
    $user->givePermissionTo('settings.view-any');

    actingAs($user);

    Livewire::test(ManageCommunicationSettings::class)
        ->assertOk()
        ->assertSet('activeTab', 'notifications')
        ->assertSeeText('Notifications')
        ->assertSeeText('Email Templates');
});

it('loads the existing notification settings', function (): void {
    $user = User::factory()->create();
    $user->givePermissionTo('settings.view-any');

    actingAs($user);

    $settings = app(NotificationSettings::class);
    $settings->from_name = 'Existing From Name';
    $settings->save();

    Livewire::test(ManageCommunicationSettings::class)
        ->assertSchemaStateSet(['from_name' => 'Existing From Name'], 'notificationsForm');
});

it('validates the notification settings inputs', function (): void {
    $user = User::factory()->create();
    $user->givePermissionTo(['settings.view-any', 'settings.*.update']);

    actingAs($user);

    Livewire::test(ManageCommunicationSettings::class)
        ->fillForm(['from_name' => str_repeat('a', 151)], 'notificationsForm')
        ->call('saveNotifications')
        ->assertHasFormErrors(['from_name' => 'max'], 'notificationsForm');
});

it('can update the notification settings', function (): void {
    $user = User::factory()->create();
    $user->givePermissionTo(['settings.view-any', 'settings.*.update']);

    actingAs($user);

    Livewire::test(ManageCommunicationSettings::class)
        ->fillForm([
            'from_name' => 'New From Name',
            'primary_color' => Color::Blue->value,
        ], 'notificationsForm')
        ->call('saveNotifications')
        ->assertHasNoFormErrors(form: 'notificationsForm');

    $settings = app(NotificationSettings::class);

    expect($settings->from_name)->toBe('New From Name')
        ->and($settings->primary_color)->toBe(Color::Blue);
});

it('only shows the email templates bulk delete action to a user with the settings.*.delete permission', function (): void {
    EmailTemplate::factory(3)->create();

    $user = User::factory()->create();
    $user->givePermissionTo(['settings.view-any', 'settings.*.view']);

    actingAs($user);

    Livewire::test(ManageCommunicationSettings::class)
        ->set('activeTab', 'email-templates')
        ->assertActionHidden(TestAction::make('delete')->table()->bulk());

    $user->givePermissionTo('settings.*.delete');

    Livewire::test(ManageCommunicationSettings::class)
        ->set('activeTab', 'email-templates')
        ->assertActionVisible(TestAction::make('delete')->table()->bulk());
});

describe('authorization', function (): void {
    it('denies access to the communication settings page without the `settings.view-any` permission', function (): void {
        $user = User::factory()->create();

        actingAs($user);

        Livewire::test(ManageCommunicationSettings::class)->assertForbidden();
    });

    it('allows access to the communication settings page with the `settings.view-any` permission', function (): void {
        $user = User::factory()->create();
        $user->givePermissionTo('settings.view-any');

        actingAs($user);

        Livewire::test(ManageCommunicationSettings::class)->assertOk();
    });

    it('does not update the notification settings without the `settings.*.update` permission', function (): void {
        $user = User::factory()->create();
        $user->givePermissionTo('settings.view-any');

        actingAs($user);

        $settings = app(NotificationSettings::class);
        $settings->from_name = 'Existing From Name';
        $settings->save();

        Livewire::test(ManageCommunicationSettings::class)
            ->set('notificationsData.from_name', 'New From Name')
            ->call('saveNotifications');

        expect(app(NotificationSettings::class)->from_name)->toBe('Existing From Name');
    });
});
