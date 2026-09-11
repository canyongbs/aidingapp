<?php

use App\Filament\Pages\ManageNotificationSettings;
use App\Models\User;
use App\Settings\NotificationSettings;
use CanyonGBS\Common\Enums\Color;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

it('can render the manage notification settings page', function () {
    asSuperAdmin();

    get(ManageNotificationSettings::getUrl())
        ->assertOk();
});

it('requires proper permissions to access', function () {
    $user = User::factory()->create();

    actingAs($user);

    get(ManageNotificationSettings::getUrl())
        ->assertForbidden();

    $user->givePermissionTo('settings.view-any');

    get(ManageNotificationSettings::getUrl())
        ->assertOk();
});

it('validates the inputs', function (array $state, array $errors) {
    asSuperAdmin();

    livewire(ManageNotificationSettings::class)
        ->fillForm($state)
        ->call('save')
        ->assertHasFormErrors($errors);
})->with([
    'name required' => [['name' => null], ['name' => 'required']],
    'from_name max' => [['from_name' => str_repeat('a', 151)], ['from_name' => 'max']],
]);

it('loads existing data into the form', function () {
    asSuperAdmin();

    $settings = app(NotificationSettings::class);
    $settings->from_name = 'Existing From Name';
    $settings->save();

    livewire(ManageNotificationSettings::class)->assertSchemaStateSet(['from_name' => 'Existing From Name']);
});

it('can update the notification settings', function () {
    asSuperAdmin();

    livewire(ManageNotificationSettings::class)
        ->fillForm([
            'from_name' => 'New From Name',
            'primary_color' => Color::Blue->value,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $settings = app(NotificationSettings::class);

    expect($settings->from_name)->toBe('New From Name')
        ->and($settings->primary_color)->toBe(Color::Blue);
});

it('requires proper permissions to update settings', function () {
    $user = User::factory()->create();

    $user->givePermissionTo('settings.view-any');
    actingAs($user);

    $settings = app(NotificationSettings::class);

    livewire(ManageNotificationSettings::class)
        ->fillForm([
            'from_name' => 'New From Name',
            'primary_color' => Color::Blue->value,
        ])
        ->call('save');

    expect($settings->from_name)->not()->toBe('New From Name')
        ->and($settings->primary_color)->not()->toBe(Color::Blue);

    $user->givePermissionTo('settings.*.update');

    livewire(ManageNotificationSettings::class)
        ->fillForm([
            'from_name' => 'New From Name',
            'primary_color' => Color::Blue->value,
        ])
        ->call('save');

    expect($settings->from_name)->toBe('New From Name')
        ->and($settings->primary_color)->toBe(Color::Blue);
});
