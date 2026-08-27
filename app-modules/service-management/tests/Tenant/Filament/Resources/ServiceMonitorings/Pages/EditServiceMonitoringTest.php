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
use AidingApp\Department\Models\Department;
use AidingApp\ServiceManagement\Enums\MonitorType;
use AidingApp\ServiceManagement\Filament\Resources\ServiceMonitorings\Pages\EditServiceMonitoring;
use AidingApp\ServiceManagement\Filament\Resources\ServiceMonitorings\ServiceMonitoringResource;
use AidingApp\ServiceManagement\Models\ServiceMonitoringTarget;
use AidingApp\ServiceManagement\Tests\Tenant\RequestFactories\ServiceMonitoringTargetRequestFactory;
use App\Filament\Forms\Components\UserSelect;
use App\Models\Authenticatable;
use App\Models\User;
use App\Settings\LicenseSettings;
use Filament\Actions\DeleteAction;
use Illuminate\Support\Facades\Config;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

test('EditServiceMonitoring is gated with proper access control', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->serviceMonitoring = false;
    $settings->save();

    $user = User::factory()->create();

    $serviceMonitoringTarget = ServiceMonitoringTarget::factory()->create();

    actingAs($user)
        ->get(
            ServiceMonitoringResource::getUrl('edit', [
                'record' => $serviceMonitoringTarget,
            ])
        )->assertForbidden();

    livewire(EditServiceMonitoring::class, [
        'record' => $serviceMonitoringTarget->getRouteKey(),
    ])
        ->assertForbidden();

    $user->givePermissionTo('service_monitoring.view-any');
    $user->givePermissionTo('service_monitoring.*.update');

    actingAs($user)
        ->get(
            ServiceMonitoringResource::getUrl('edit', [
                'record' => $serviceMonitoringTarget,
            ])
        )->assertForbidden();

    $settings->data->addons->serviceMonitoring = true;
    $settings->save();

    $request = collect(ServiceMonitoringTargetRequestFactory::new()->create());

    livewire(EditServiceMonitoring::class, [
        'record' => $serviceMonitoringTarget->getRouteKey(),
    ])
        ->fillForm($request->toArray())
        ->call('save')
        ->assertHasNoFormErrors();

    $serviceMonitoringTarget->refresh();

    expect($serviceMonitoringTarget->fresh()->name)->toEqual($request->get('name'))
        ->and($serviceMonitoringTarget->description)->toEqual($request->get('description'))
        ->and($serviceMonitoringTarget->domain)->toEqual($request->get('domain'))
        ->and($serviceMonitoringTarget->frequency)->toEqual($request->get('frequency'));
});

test('EditServiceMonitoring validates the inputs', function ($data, $errors) {
    asSuperAdmin();

    $serviceMonitoringTarget = ServiceMonitoringTarget::factory()->create();

    $request = ServiceMonitoringTargetRequestFactory::new($data)->create();

    livewire(EditServiceMonitoring::class, [
        'record' => $serviceMonitoringTarget->getRouteKey(),
    ])
        ->fillForm($request)
        ->call('save')
        ->assertHasFormErrors($errors);
})->with(
    [
        'name required' => [
            ServiceMonitoringTargetRequestFactory::new()->state(['name' => null]),
            ['name' => 'required'],
        ],
        'name string' => [
            ServiceMonitoringTargetRequestFactory::new()->state(['name' => 1]),
            ['name' => 'string'],
        ],
        'name max' => [
            ServiceMonitoringTargetRequestFactory::new()->state(['name' => str()->random(256)]),
            ['name' => 'max'],
        ],
        'description max' => [
            ServiceMonitoringTargetRequestFactory::new()->state(['description' => str()->random(65536)]),
            ['description' => 'max'],
        ],
        'domain required' => [
            ServiceMonitoringTargetRequestFactory::new()->state(['domain' => null]),
            ['domain' => 'required'],
        ],
        'domain max' => [
            ServiceMonitoringTargetRequestFactory::new()->state(['domain' => str()->random(256)]),
            ['domain' => 'max'],
        ],
        // The domain url test is more extensively handle in saperate test below
        'frequency required' => [
            ServiceMonitoringTargetRequestFactory::new()->state(['frequency' => null]),
            ['frequency' => 'required'],
        ],
        'report frequency required when reporting is active' => [
            ServiceMonitoringTargetRequestFactory::new()->state([
                'is_reporting_active' => true,
                'report_frequency' => null,
            ]),
            ['report_frequency' => 'required'],
        ],
        'should contain does not have opening quote without closing quote' => [
            ServiceMonitoringTargetRequestFactory::new()->state([
                'monitor_type' => MonitorType::KeywordMatch,
                'should_contain' => 'test 1, "test 2',
            ]),
            ['should_contain'],
        ],
        'should not contain does not have opening quote without closing quote' => [
            ServiceMonitoringTargetRequestFactory::new()->state([
                'monitor_type' => MonitorType::KeywordMatch,
                'should_not_contain' => 'test 1, "test 2',
            ]),
            ['should_not_contain'],
        ],
        'should contain does not have empty double quote pair' => [
            ServiceMonitoringTargetRequestFactory::new()->state([
                'monitor_type' => MonitorType::KeywordMatch,
                'should_contain' => '""',
            ]),
            ['should_contain'],
        ],
        'should not contain does not have empty double quote pair' => [
            ServiceMonitoringTargetRequestFactory::new()->state([
                'monitor_type' => MonitorType::KeywordMatch,
                'should_not_contain' => '""',
            ]),
            ['should_not_contain'],
        ],
        'keyword match requires at least one keyword field' => [
            ServiceMonitoringTargetRequestFactory::new()->state([
                'monitor_type' => MonitorType::KeywordMatch,
                'should_contain' => null,
                'should_not_contain' => null,
            ]),
            ['should_contain', 'should_not_contain'],
        ],
        'should contain does not have double quote in the middle of an unquoted string' => [
            ServiceMonitoringTargetRequestFactory::new()->state([
                'monitor_type' => MonitorType::KeywordMatch,
                'should_contain' => 'test "1',
            ]),
            ['should_contain'],
        ],
        'should not contain does not have double quote in the middle of an unquoted string' => [
            ServiceMonitoringTargetRequestFactory::new()->state([
                'monitor_type' => MonitorType::KeywordMatch,
                'should_not_contain' => 'test "1',
            ]),
            ['should_not_contain'],
        ],
        'should contain does not have only commas' => [
            ServiceMonitoringTargetRequestFactory::new()->state([
                'monitor_type' => MonitorType::KeywordMatch,
                'should_contain' => ',,,',
            ]),
            ['should_contain'],
        ],
        'should not contain does not have only commas' => [
            ServiceMonitoringTargetRequestFactory::new()->state([
                'monitor_type' => MonitorType::KeywordMatch,
                'should_not_contain' => ',,,',
            ]),
            ['should_not_contain'],
        ],
        'should contain does not have only mixed empty characters' => [
            ServiceMonitoringTargetRequestFactory::new()->state([
                'monitor_type' => MonitorType::KeywordMatch,
                'should_contain' => '"", , ',
            ]),
            ['should_contain'],
        ],
        'should not contain does not have only mixed empty characters' => [
            ServiceMonitoringTargetRequestFactory::new()->state([
                'monitor_type' => MonitorType::KeywordMatch,
                'should_not_contain' => '"", , ',
            ]),
            ['should_not_contain'],
        ],
        'same value is not in both keyword lists' => [
            ServiceMonitoringTargetRequestFactory::new()->state([
                'monitor_type' => MonitorType::KeywordMatch,
                'should_contain' => 'test 1',
                'should_not_contain' => 'test 1',
            ]),
            ['should_contain', 'should_not_contain'],
        ],
        'same value with different casing is not in both keyword lists' => [
            ServiceMonitoringTargetRequestFactory::new()->state([
                'monitor_type' => MonitorType::KeywordMatch,
                'should_contain' => 'Error',
                'should_not_contain' => 'error',
            ]),
            ['should_contain', 'should_not_contain'],
        ],
        'should not contain value cannot be a substring of should contain value' => [
            ServiceMonitoringTargetRequestFactory::new()->state([
                'monitor_type' => MonitorType::KeywordMatch,
                'should_contain' => 'test 1',
                'should_not_contain' => 'test',
            ]),
            ['should_not_contain'],
        ],
        'keyword values cannot contain multiple pairs or an additional double quote' => [
            ServiceMonitoringTargetRequestFactory::new()->state([
                'monitor_type' => MonitorType::KeywordMatch,
                'should_contain' => 'test "1" "2"',
                'should_not_contain' => 'test "1" 2"',
            ]),
            ['should_contain', 'should_not_contain'],
        ],
    ]
);

test('EditServiceMonitoring hydrates keyword values as comma-separated text', function () {
    asSuperAdmin();

    $serviceMonitoringTarget = ServiceMonitoringTarget::factory()->create([
        'monitor_type' => MonitorType::KeywordMatch,
        'should_contain' => ['test 1', 'test 2'],
        'should_not_contain' => ['test 3', 'test 4'],
    ]);

    livewire(EditServiceMonitoring::class, [
        'record' => $serviceMonitoringTarget->getRouteKey(),
    ])
        ->assertSchemaStateSet([
            'should_contain' => 'test 1, test 2',
            'should_not_contain' => 'test 3, test 4',
        ]);
});

test('EditServiceMonitoring restores quotes for ambiguous keyword values', function () {
    asSuperAdmin();

    $serviceMonitoringTarget = ServiceMonitoringTarget::factory()->create([
        'monitor_type' => MonitorType::KeywordMatch,
        'should_contain' => ['test1', 'test 2', 'test 3, "test 4"', '"test 5, test 6"'],
    ]);

    livewire(EditServiceMonitoring::class, [
        'record' => $serviceMonitoringTarget->getRouteKey(),
    ])->assertSchemaStateSet([
        'should_contain' => 'test1, test 2, "test 3, "test 4"", ""test 5, test 6""',
    ]);
});

test('EditServiceMonitoring appends new keyword values to existing lists', function () {
    asSuperAdmin();

    $serviceMonitoringTarget = ServiceMonitoringTarget::factory()->create([
        'monitor_type' => MonitorType::KeywordMatch,
        'should_contain' => ['test 1'],
        'should_not_contain' => ['test 2'],
    ]);

    livewire(EditServiceMonitoring::class, [
        'record' => $serviceMonitoringTarget->getRouteKey(),
    ])
        ->fillForm([
            'should_contain' => 'test 1, test 3',
            'should_not_contain' => 'test 2, test 4',
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($serviceMonitoringTarget->refresh()->should_contain)->toBe(['test 1', 'test 3'])
        ->and($serviceMonitoringTarget->should_not_contain)->toBe(['test 2', 'test 4']);
});

test('EditServiceMonitoring hydrates report channels from persisted flags', function (array $attributes, array $expectedChannels) {
    asSuperAdmin();

    $serviceMonitoringTarget = ServiceMonitoringTarget::factory()->create([
        'is_reporting_active' => true,
        ...$attributes,
    ]);

    livewire(EditServiceMonitoring::class, [
        'record' => $serviceMonitoringTarget->getRouteKey(),
    ])
        ->assertSchemaStateSet([
            'report_channels' => $expectedChannels,
        ]);
})->with([
    'email only' => [
        ['is_reported_via_email' => true, 'is_reported_via_database' => false],
        ['is_reported_via_email'],
    ],
    'application only' => [
        ['is_reported_via_email' => false, 'is_reported_via_database' => true],
        ['is_reported_via_database'],
    ],
    'both channels' => [
        ['is_reported_via_email' => true, 'is_reported_via_database' => true],
        ['is_reported_via_email', 'is_reported_via_database'],
    ],
    'no channels' => [
        ['is_reported_via_email' => false, 'is_reported_via_database' => false],
        [],
    ],
]);

test('delete action visible with proper access control', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->serviceMonitoring = true;
    $settings->save();

    $user = User::factory()->create();

    $serviceMonitoringTarget = ServiceMonitoringTarget::factory()->create();

    actingAs($user);

    $user->givePermissionTo('service_monitoring.view-any');
    $user->givePermissionTo('service_monitoring.*.update');

    livewire(EditServiceMonitoring::class, [
        'record' => $serviceMonitoringTarget->getRouteKey(),
    ])
        ->assertActionHidden(DeleteAction::class);

    $user->givePermissionTo('service_monitoring.*.delete');

    livewire(EditServiceMonitoring::class, [
        'record' => $serviceMonitoringTarget->getRouteKey(),
    ])
        ->assertActionVisible(DeleteAction::class);
});

test('it will validate multiple valid forms of URL and IP Address', function () {
    asSuperAdmin();

    $serviceMonitoringTarget = ServiceMonitoringTarget::factory()->create();

    $validUrls = [
        'http://example.com',
        'https://test.com',
        'example.com',
        '192.168.0.1',
        '127.0.0.1',
        '192.0.2.10',
        '098.51.100.252',
        'http://[2001:db8::1]',
        'https://[fe80::1ff:fe23:4567:890a]:443',
        '2001:0db8:0000:0000:0000:0000:1234:5678',
    ];

    $invalidUrls = [
        'ftp://example.com',
        'example..com',
        '://missing.scheme.com',
        'http://example',
        '[2001:db8::1',
        '2001:db8::1]',
        '[gggg::1]',
    ];

    foreach ($validUrls as $url) {
        $request = ServiceMonitoringTarget::factory()->make(['domain' => $url])->toArray();

        livewire(EditServiceMonitoring::class, [
            'record' => $serviceMonitoringTarget->getRouteKey(),
        ])
            ->fillForm($request)
            ->call('save')
            ->assertHasNoFormErrors();
    }

    foreach ($invalidUrls as $url) {
        $request = ServiceMonitoringTarget::factory()->make(['domain' => $url])->toArray();

        livewire(EditServiceMonitoring::class, [
            'record' => $serviceMonitoringTarget->getRouteKey(),
        ])
            ->fillForm($request)
            ->call('save')
            ->assertHasFormErrors(['domain']);
    }
});

// UserSelect (user field) admin-filtering tests

test('user UserSelect does not show admin users in options by default on EditServiceMonitoring', function () {
    $actor = User::factory()->create();
    $actor->givePermissionTo('service_monitoring.view-any');
    $actor->givePermissionTo('service_monitoring.*.update');
    actingAs($actor);

    $regularUser = User::factory()->create();
    $adminUser = User::factory()->create();
    $adminUser->assignRole(Authenticatable::SUPER_ADMIN_ROLE);

    $serviceMonitoringTarget = ServiceMonitoringTarget::factory()->create();

    livewire(EditServiceMonitoring::class, ['record' => $serviceMonitoringTarget->getRouteKey()])
        ->assertSuccessful()
        ->assertFormFieldExists('user', function (UserSelect $field) use ($regularUser, $adminUser): bool {
            return ! empty($field->getSearchResults($regularUser->name))
                && empty($field->getSearchResults($adminUser->name));
        });
});

test('user UserSelect shows a pre-selected admin user so they can be deselected on EditServiceMonitoring', function () {
    $actor = User::factory()->create();
    $actor->givePermissionTo('service_monitoring.view-any');
    $actor->givePermissionTo('service_monitoring.*.update');
    actingAs($actor);

    $adminUser = User::factory()->create();
    $adminUser->assignRole(Authenticatable::SUPER_ADMIN_ROLE);

    $serviceMonitoringTarget = ServiceMonitoringTarget::factory()->create();
    $serviceMonitoringTarget->users()->attach($adminUser);

    livewire(EditServiceMonitoring::class, ['record' => $serviceMonitoringTarget->getRouteKey()])
        ->assertSuccessful()
        ->assertFormFieldExists('user', function (UserSelect $field) use ($adminUser): bool {
            return ! empty($field->getSearchResults($adminUser->name));
        });
});

test('user UserSelect shows all users when filter_admins_from_selection config is false on EditServiceMonitoring', function () {
    Config::set('app.filter_admins_from_selection', false);

    $actor = User::factory()->create();
    $actor->givePermissionTo('service_monitoring.view-any');
    $actor->givePermissionTo('service_monitoring.*.update');
    actingAs($actor);

    $adminUser = User::factory()->create();
    $adminUser->assignRole(Authenticatable::SUPER_ADMIN_ROLE);

    $serviceMonitoringTarget = ServiceMonitoringTarget::factory()->create();

    livewire(EditServiceMonitoring::class, ['record' => $serviceMonitoringTarget->getRouteKey()])
        ->assertSuccessful()
        ->assertFormFieldExists('user', function (UserSelect $field) use ($adminUser): bool {
            return ! empty($field->getSearchResults($adminUser->name));
        });
});

test('turning off confidentiality clears previously granted users, departments, and contacts', function () {
    asSuperAdmin();

    $serviceMonitoringTarget = ServiceMonitoringTarget::factory()->confidential()->create();
    $grantedUser = User::factory()->create();
    $grantedDepartment = Department::factory()->create();
    $grantedContact = Contact::factory()->create();

    livewire(EditServiceMonitoring::class, ['record' => $serviceMonitoringTarget->getRouteKey()])
        ->fillForm([
            'is_confidential' => true,
            'confidentialUsers' => [$grantedUser->getKey()],
            'confidentialDepartments' => [$grantedDepartment->getKey()],
            'confidentialContacts' => [$grantedContact->getKey()],
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($serviceMonitoringTarget->confidentialUsers()->count())->toBe(1)
        ->and($serviceMonitoringTarget->confidentialDepartments()->count())->toBe(1)
        ->and($serviceMonitoringTarget->confidentialContacts()->count())->toBe(1);

    livewire(EditServiceMonitoring::class, ['record' => $serviceMonitoringTarget->getRouteKey()])
        ->fillForm([
            'is_confidential' => false,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($serviceMonitoringTarget->confidentialUsers()->count())->toBe(0)
        ->and($serviceMonitoringTarget->confidentialDepartments()->count())->toBe(0)
        ->and($serviceMonitoringTarget->confidentialContacts()->count())->toBe(0);
});

test('the confidential users, departments, and contacts fields are only visible when confidentiality is enabled on the edit page', function () {
    asSuperAdmin();

    $serviceMonitoringTarget = ServiceMonitoringTarget::factory()->create(['is_confidential' => false]);

    livewire(EditServiceMonitoring::class, ['record' => $serviceMonitoringTarget->getRouteKey()])
        ->assertSchemaComponentHidden('confidentialUsers')
        ->assertSchemaComponentHidden('confidentialDepartments')
        ->assertSchemaComponentHidden('confidentialContacts')
        ->fillForm(['is_confidential' => true])
        ->assertSchemaComponentVisible('confidentialUsers')
        ->assertSchemaComponentVisible('confidentialDepartments')
        ->assertSchemaComponentVisible('confidentialContacts');
});

test('a service monitor cannot be made confidential while a notification recipient has no confidential access', function () {
    asSuperAdmin();

    $notifiedUser = User::factory()->create();
    $serviceMonitoringTarget = ServiceMonitoringTarget::factory()->create(['is_confidential' => false]);

    livewire(EditServiceMonitoring::class, ['record' => $serviceMonitoringTarget->getRouteKey()])
        ->fillForm([
            'user' => [$notifiedUser->getKey()],
            'is_confidential' => true,
        ])
        ->call('save')
        ->assertHasFormErrors(['is_confidential']);

    expect($serviceMonitoringTarget->refresh()->is_confidential)->toBeFalse();
});

test('the creator on the notification list satisfies the confidential access rule without an explicit grant', function () {
    asSuperAdmin();

    $creator = User::factory()->create();

    $serviceMonitoringTarget = ServiceMonitoringTarget::factory()
        ->for($creator, 'createdBy')
        ->create(['is_confidential' => false]);

    livewire(EditServiceMonitoring::class, ['record' => $serviceMonitoringTarget->getRouteKey()])
        ->fillForm([
            'user' => [$creator->getKey()],
            'is_confidential' => true,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($serviceMonitoringTarget->refresh()->is_confidential)->toBeTrue();
});

test('a notification recipient covered by a granted department satisfies the confidential access rule', function () {
    asSuperAdmin();

    $grantedDepartment = Department::factory()->create();
    $notifiedUser = User::factory()->create();
    $notifiedUser->department()->associate($grantedDepartment)->save();

    $serviceMonitoringTarget = ServiceMonitoringTarget::factory()->create(['is_confidential' => false]);

    livewire(EditServiceMonitoring::class, ['record' => $serviceMonitoringTarget->getRouteKey()])
        ->fillForm([
            'user' => [$notifiedUser->getKey()],
            'is_confidential' => true,
            'confidentialDepartments' => [$grantedDepartment->getKey()],
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($serviceMonitoringTarget->refresh()->is_confidential)->toBeTrue();
});

test('marking a legacy service monitor confidential backfills the editor as its creator', function () {
    $serviceMonitoringTarget = ServiceMonitoringTarget::factory()->create();

    expect($serviceMonitoringTarget->getAttribute('created_by_id'))->toBeNull();

    $editor = User::factory()->create();
    $editor->givePermissionTo('service_monitoring.view-any');
    $editor->givePermissionTo('service_monitoring.*.view');
    $editor->givePermissionTo('service_monitoring.*.update');
    actingAs($editor);

    livewire(EditServiceMonitoring::class, ['record' => $serviceMonitoringTarget->getRouteKey()])
        ->fillForm(['is_confidential' => true])
        ->call('save')
        ->assertHasNoFormErrors();

    get(ServiceMonitoringResource::getUrl('view', ['record' => $serviceMonitoringTarget]))
        ->assertSuccessful();
});
