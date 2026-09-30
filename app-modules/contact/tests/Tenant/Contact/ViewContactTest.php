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

use AidingApp\Contact\Filament\Resources\ContactResource;
use AidingApp\Contact\Filament\Resources\ContactResource\Pages\ViewContact;
use AidingApp\Contact\Filament\Resources\ContactResource\RelationManagers\AssetCheckInRelationManager;
use AidingApp\Contact\Filament\Resources\ContactResource\RelationManagers\AssetCheckOutRelationManager;
use AidingApp\Contact\Filament\Resources\ContactResource\RelationManagers\EngagementsRelationManager;
use AidingApp\Contact\Filament\Resources\ContactResource\RelationManagers\ServiceRequestsRelationManager;
use AidingApp\Contact\Models\Contact;
use AidingApp\Contact\Models\ContactType;
use AidingApp\Contact\Models\Organization;
use AidingApp\Engagement\Filament\Resources\EngagementFiles\RelationManagers\EngagementFilesRelationManager;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

test('The correct details are displayed on the ViewContact page', function () {
    asSuperAdmin();

    $contactType = ContactType::factory()->create(['name' => 'Student']);
    $organization = Organization::factory()->create(['name' => 'Acme Inc']);

    $contact = Contact::factory()->create([
        'first_name' => 'John',
        'last_name' => 'Doe',
        'full_name' => 'Mr. John Doe',
        'preferred' => 'Johnny',
        'job_title' => 'Manager',
        'email' => 'john.doe@example.com',
        'city' => 'Springfield',
        'type_id' => $contactType->getKey(),
        'organization_id' => $organization->getKey(),
    ]);

    livewire(ViewContact::class, ['record' => $contact->getRouteKey()])
        ->assertSuccessful()
        ->assertSchemaStateSet([
            'first_name' => 'John',
            'last_name' => 'Doe',
            Contact::displayNameKey() => 'Mr. John Doe',
            'preferred' => 'Johnny',
            'job_title' => 'Manager',
            'email' => 'john.doe@example.com',
            'city' => 'Springfield',
            'type_id' => $contactType->getKey(),
            'organization_id' => $organization->getKey(),
        ]);
});

test('ViewContact renders successfully when optional fields have no value', function () {
    asSuperAdmin();

    $contact = Contact::factory()->create([
        'job_title' => null,
        'organization_id' => null,
        'address_2' => null,
    ]);

    livewire(ViewContact::class, ['record' => $contact->getRouteKey()])
        ->assertSuccessful()
        ->assertSchemaStateSet([
            'job_title' => null,
            'organization_id' => null,
            'address_2' => null,
        ]);
});

// Permission Tests

test('ViewContact is gated with proper access control', function () {
    $user = User::factory()->create();

    $contact = Contact::factory()->create();

    actingAs($user)
        ->get(
            ContactResource::getUrl('view', [
                'record' => $contact,
            ])
        )->assertForbidden();

    $user->givePermissionTo('contact.view-any');
    $user->givePermissionTo('contact.*.view');

    actingAs($user)
        ->get(
            ContactResource::getUrl('view', [
                'record' => $contact,
            ])
        )->assertSuccessful();
});

describe('tabs', function () {
    test('renders successfully with no tabs visible when the user has no relation manager abilities', function () {
        $user = User::factory()->create();
        $user->givePermissionTo('contact.view-any', 'contact.*.view');
        actingAs($user);

        $contact = Contact::factory()->create();

        livewire(ViewContact::class, ['record' => $contact->getRouteKey()])
            ->assertSuccessful()
            ->assertDontSeeLivewire(ServiceRequestsRelationManager::class)
            ->assertDontSeeLivewire(AssetCheckOutRelationManager::class)
            ->assertDontSeeLivewire(AssetCheckInRelationManager::class)
            ->assertDontSeeLivewire(EngagementFilesRelationManager::class)
            ->assertDontSeeLivewire(EngagementsRelationManager::class);
    });

    test('shows the Service Requests tab only with the service_request.view-any ability', function () {
        $user = User::factory()->create();
        $user->givePermissionTo('contact.view-any', 'contact.*.view');
        actingAs($user);

        $contact = Contact::factory()->create();

        livewire(ViewContact::class, ['record' => $contact->getRouteKey()])
            ->assertDontSeeLivewire(ServiceRequestsRelationManager::class);

        $user->givePermissionTo('service_request.view-any');

        livewire(ViewContact::class, ['record' => $contact->getRouteKey()])
            ->assertSuccessful()
            ->assertSeeLivewire(ServiceRequestsRelationManager::class);
    });

    test('shows the Assets tab only with an asset_check_out/asset_check_in.view-any ability', function () {
        $user = User::factory()->create();
        $user->givePermissionTo('contact.view-any', 'contact.*.view');
        actingAs($user);

        $contact = Contact::factory()->create();

        livewire(ViewContact::class, ['record' => $contact->getRouteKey()])
            ->assertDontSeeLivewire(AssetCheckOutRelationManager::class)
            ->assertDontSeeLivewire(AssetCheckInRelationManager::class);

        $user->givePermissionTo('asset_check_out.view-any');

        livewire(ViewContact::class, ['record' => $contact->getRouteKey()])
            ->assertSuccessful()
            ->assertSeeLivewire(AssetCheckOutRelationManager::class)
            ->assertDontSeeLivewire(AssetCheckInRelationManager::class);

        $user->givePermissionTo('asset_check_in.view-any');

        livewire(ViewContact::class, ['record' => $contact->getRouteKey()])
            ->assertSuccessful()
            ->assertSeeLivewire(AssetCheckOutRelationManager::class)
            ->assertSeeLivewire(AssetCheckInRelationManager::class);
    });

    test('shows the Files tab only with the engagement_file.view-any ability', function () {
        $user = User::factory()->create();
        $user->givePermissionTo('contact.view-any', 'contact.*.view');
        actingAs($user);

        $contact = Contact::factory()->create();

        livewire(ViewContact::class, ['record' => $contact->getRouteKey()])
            ->assertDontSeeLivewire(EngagementFilesRelationManager::class);

        $user->givePermissionTo('engagement_file.view-any');

        livewire(ViewContact::class, ['record' => $contact->getRouteKey()])
            ->assertSuccessful()
            ->assertSeeLivewire(EngagementFilesRelationManager::class);
    });

    test('shows the Emails tab only with an engagement/engagement_response.view-any ability', function () {
        $user = User::factory()->create();
        $user->givePermissionTo('contact.view-any', 'contact.*.view');
        actingAs($user);

        $contact = Contact::factory()->create();

        livewire(ViewContact::class, ['record' => $contact->getRouteKey()])
            ->assertDontSeeLivewire(EngagementsRelationManager::class);

        $user->givePermissionTo('engagement.view-any');

        livewire(ViewContact::class, ['record' => $contact->getRouteKey()])
            ->assertSuccessful()
            ->assertSeeLivewire(EngagementsRelationManager::class);
    });
});
