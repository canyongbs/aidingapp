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
use AidingApp\Contact\Tests\Tenant\Contact\RequestFactories\EditContactRequestFactory;
use AidingApp\Engagement\Filament\Resources\EngagementFiles\RelationManagers\EngagementFilesRelationManager;
use App\Models\User;
use Dom\HTMLDocument;
use Dom\Node;
use Dom\XPath;
use Filament\Actions\Testing\TestAction;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Callout;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
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
        'last_activity_at' => now(),
    ]);

    $component = Livewire::test(ViewContact::class, ['record' => $contact->getRouteKey()])
        ->assertSuccessful();

    $expected = [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'full_name' => 'Mr. John Doe',
        'preferred' => 'Johnny',
        'presence_status' => $contact->presenceStatus(),
        'job_title' => 'Manager',
        'email' => 'john.doe@example.com',
        'city' => 'Springfield',
        'type.name' => 'Student',
        'organization.name' => 'Acme Inc',
    ];

    expect(\Tests\infolistState($component->instance(), array_keys($expected)))->toEqual($expected);
});

test('The primary information sections are organized into tabs on the ViewContact page', function () {
    asSuperAdmin();

    $contact = Contact::factory()->create();

    $component = Livewire::test(ViewContact::class, ['record' => $contact->getRouteKey()])
        ->assertSuccessful();

    $document = HTMLDocument::createFromString($component->html(), LIBXML_NOERROR);
    $tabs = (new XPath($document))->query('(//*[contains(concat(" ", normalize-space(@class), " "), " fi-tabs ")])[1]//*[@role="tab"]');

    expect(array_map(fn (Node $tab): string => trim($tab->textContent), iterator_to_array($tabs)))
        ->toBe(['Demographic', 'Contact', 'Employment', 'Academic', 'Address', 'Affiliation', 'System']);
});

test('ViewContact renders successfully when optional fields have no value', function () {
    asSuperAdmin();

    $contact = Contact::factory()->create([
        'job_title' => null,
        'organization_id' => null,
        'address_2' => null,
    ]);

    Livewire::test(ViewContact::class, ['record' => $contact->getRouteKey()])
        ->assertSuccessful();
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

describe('section edit actions', function () {
    test('the contact section edit actions are gated with proper access control', function () {
        $user = User::factory()->create();

        $contact = Contact::factory()->create();

        actingAs($user);

        $user->givePermissionTo('contact.view-any');
        $user->givePermissionTo('contact.*.view');

        Livewire::test(ViewContact::class, [
            'record' => $contact->getRouteKey(),
        ])
            ->assertActionDoesNotExist(TestAction::make('editDemographicInformation')->schemaComponent('demographicInformation'));

        $user->givePermissionTo('contact.*.update');

        Livewire::test(ViewContact::class, [
            'record' => $contact->getRouteKey(),
        ])
            ->assertActionVisible(TestAction::make('editDemographicInformation')->schemaComponent('demographicInformation'));
    });

    test('the contact sections can be updated', function () {
        $user = User::factory()->create();
        $user->givePermissionTo('contact.view-any');
        $user->givePermissionTo('contact.*.view');
        $user->givePermissionTo('contact.*.update');

        actingAs($user);

        $contact = Contact::factory()->create();

        $request = collect(EditContactRequestFactory::new()->create());

        Livewire::test(ViewContact::class, [
            'record' => $contact->getRouteKey(),
        ])
            ->callAction(TestAction::make('editDemographicInformation')->schemaComponent('demographicInformation'), data: [
                'first_name' => $request->get('first_name'),
                'last_name' => $request->get('last_name'),
                'preferred' => $request->get('preferred'),
                'type_id' => $request->get('type_id'),
            ])
            ->assertHasNoFormErrors()
            ->callAction(TestAction::make('editContactInformation')->schemaComponent('contactInformation'), data: [
                'email' => $request->get('email'),
                'mobile' => $request->get('mobile'),
                'phone' => $request->get('phone'),
            ])
            ->assertHasNoFormErrors();

        expect($contact->fresh()->type_id)->toEqual($request->get('type_id'))->and($contact->fresh()->first_name)->toEqual($request->get('first_name'))->and($contact->fresh()->last_name)->toEqual($request->get('last_name'))->and($contact->fresh()->full_name)->toEqual("{$request->get('first_name')} {$request->get('last_name')}")->and($contact->fresh()->preferred)->toEqual($request->get('preferred'))->and($contact->fresh()->email)->toEqual($request->get('email'))->and($contact->fresh()->mobile)->toEqual($request->get('mobile'))->and($contact->fresh()->phone)->toEqual($request->get('phone'));
    });

    test('the employment information section can be updated', function () {
        $user = User::factory()->create();
        $user->givePermissionTo('contact.view-any');
        $user->givePermissionTo('contact.*.view');
        $user->givePermissionTo('contact.*.update');

        actingAs($user);

        $contact = Contact::factory()->create([
            'job_title' => 'Old Title',
            'employee_id' => 'OLD-EMP',
            'work_number' => null,
            'work_extension' => 'OLD-EXT',
        ]);

        $request = collect(EditContactRequestFactory::new()->create());

        Livewire::test(ViewContact::class, [
            'record' => $contact->getRouteKey(),
        ])
            ->callAction(TestAction::make('editEmploymentInformation')->schemaComponent('employmentInformation'), data: [
                'job_title' => $request->get('job_title'),
                'employee_id' => $request->get('employee_id'),
                'work_number' => $request->get('work_number'),
                'work_extension' => $request->get('work_extension'),
            ])
            ->assertHasNoFormErrors();

        expect($contact->fresh()->job_title)->toEqual($request->get('job_title'))->and($contact->fresh()->employee_id)->toEqual($request->get('employee_id'))->and($contact->fresh()->work_number)->toEqual($request->get('work_number'))->and($contact->fresh()->work_extension)->toEqual($request->get('work_extension'));
    });

    test('the academic information section can be updated', function () {
        $user = User::factory()->create();
        $user->givePermissionTo('contact.view-any');
        $user->givePermissionTo('contact.*.view');
        $user->givePermissionTo('contact.*.update');

        actingAs($user);

        $contact = Contact::factory()->create([
            'student_id' => 'OLD-STU',
            'school' => 'Old School',
            'academic_department' => 'Old Department',
            'program' => 'Old Program',
        ]);

        $request = collect(EditContactRequestFactory::new()->create());

        Livewire::test(ViewContact::class, [
            'record' => $contact->getRouteKey(),
        ])
            ->callAction(TestAction::make('editAcademicInformation')->schemaComponent('academicInformation'), data: [
                'student_id' => $request->get('student_id'),
                'school' => $request->get('school'),
                'academic_department' => $request->get('academic_department'),
                'program' => $request->get('program'),
            ])
            ->assertHasNoFormErrors();

        expect($contact->fresh()->student_id)->toEqual($request->get('student_id'))->and($contact->fresh()->school)->toEqual($request->get('school'))->and($contact->fresh()->academic_department)->toEqual($request->get('academic_department'))->and($contact->fresh()->program)->toEqual($request->get('program'));
    });

    test('the address information section can be updated', function () {
        $user = User::factory()->create();
        $user->givePermissionTo('contact.view-any');
        $user->givePermissionTo('contact.*.view');
        $user->givePermissionTo('contact.*.update');

        actingAs($user);

        $contact = Contact::factory()->create([
            'address' => 'Old Address',
            'address_2' => 'Old Address 2',
            'city' => 'Old City',
            'state' => 'Old State',
            'postal' => 'Old Postal',
            'country' => 'Old Country',
        ]);

        $request = collect(EditContactRequestFactory::new()->create());

        Livewire::test(ViewContact::class, [
            'record' => $contact->getRouteKey(),
        ])
            ->callAction(TestAction::make('editAddressInformation')->schemaComponent('addressInformation'), data: [
                'address' => $request->get('address'),
                'address_2' => $request->get('address_2'),
                'city' => $request->get('city'),
                'state' => $request->get('state'),
                'postal' => $request->get('postal'),
                'country' => $request->get('country'),
            ])
            ->assertHasNoFormErrors();

        expect($contact->fresh()->address)->toEqual($request->get('address'))->and($contact->fresh()->address_2)->toEqual($request->get('address_2'))->and($contact->fresh()->city)->toEqual($request->get('city'))->and($contact->fresh()->state)->toEqual($request->get('state'))->and($contact->fresh()->postal)->toEqual($request->get('postal'))->and($contact->fresh()->country)->toEqual($request->get('country'));
    });

    test('the customer information section can be updated', function () {
        $user = User::factory()->create();
        $user->givePermissionTo('contact.view-any');
        $user->givePermissionTo('contact.*.view');
        $user->givePermissionTo('contact.*.update');

        actingAs($user);

        $contact = Contact::factory()->create(['organization_id' => null]);

        $organization = Organization::factory()->create();

        Livewire::test(ViewContact::class, [
            'record' => $contact->getRouteKey(),
        ])
            ->callAction(TestAction::make('editCustomerInformation')->schemaComponent('customerInformation'), data: [
                'organization_id' => $organization->getKey(),
            ])
            ->assertHasNoFormErrors();

        expect($contact->fresh()->organization_id)->toEqual($organization->getKey());
    });

    test('the contact information section keeps its own email but rejects another contact\'s email case-insensitively', function () {
        $user = User::factory()->create();
        $user->givePermissionTo('contact.view-any');
        $user->givePermissionTo('contact.*.view');
        $user->givePermissionTo('contact.*.update');

        Contact::factory()->create(['email' => 'other@example.com']);
        $contact = Contact::factory()->create(['email' => 'mine@example.com']);

        actingAs($user);

        Livewire::test(ViewContact::class, [
            'record' => $contact->getRouteKey(),
        ])
            ->callAction(TestAction::make('editContactInformation')->schemaComponent('contactInformation'), data: [
                'email' => 'Mine@Example.com',
            ])
            ->assertHasNoFormErrors();

        Livewire::test(ViewContact::class, [
            'record' => $contact->getRouteKey(),
        ])
            ->callAction(TestAction::make('editContactInformation')->schemaComponent('contactInformation'), data: [
                'email' => 'Other@Example.com',
            ])
            ->assertHasFormErrors(['email']);
    });
});

describe('email health', function () {
    it('shows the email health callout and the bounced hint icon for a bounced contact', function () {
        asSuperAdmin();

        $contact = Contact::factory()->bounced()->create();

        $component = Livewire::test(ViewContact::class, ['record' => $contact->getRouteKey()])
            ->assertSuccessful()
            ->assertSchemaComponentVisible('email-health-callout');

        $callout = $component->instance()
            ->getSchema('infolist')
            ->getComponent('email-health-callout');

        expect($callout)->toBeInstanceOf(Callout::class)
            ->and($callout->getDescription())->toBe('The email provider has reported that the email address for this contact is invalid. Future emails will not be sent to this contact.');

        $component
            ->mountAction(TestAction::make('editContactInformation')->schemaComponent('contactInformation'))
            ->assertFormFieldExists('email', 'mountedActionSchema0', function (TextInput $field): bool {
                expect($field->getHintIcon())->toBe('heroicon-m-exclamation-triangle')
                    ->and($field->getHintColor())->toBe('warning')
                    ->and($field->getHintIconTooltip())->toBe('Bounced. Email delivery failed and a bounce was received from our email provider.');

                return true;
            });
    });

    it('hides the email health callout and shows the healthy hint icon for a healthy contact', function () {
        asSuperAdmin();

        $contact = Contact::factory()->create(['email_bounce' => false]);

        Livewire::test(ViewContact::class, ['record' => $contact->getRouteKey()])
            ->assertSuccessful()
            ->assertSchemaComponentHidden('email-health-callout')
            ->mountAction(TestAction::make('editContactInformation')->schemaComponent('contactInformation'))
            ->assertFormFieldExists('email', 'mountedActionSchema0', function (TextInput $field): bool {
                expect($field->getHintIcon())->toBe('heroicon-m-check-circle')
                    ->and($field->getHintColor())->toBe('success')
                    ->and($field->getHintIconTooltip())->toBe('Healthy. No delivery issues detected.');

                return true;
            });
    });

    it('hides the email health callout as soon as a bounced contact\'s email is updated, without requiring a fresh page load', function () {
        asSuperAdmin();

        $contact = Contact::factory()->bounced()->create();

        Livewire::test(ViewContact::class, ['record' => $contact->getRouteKey()])
            ->assertSchemaComponentVisible('email-health-callout')
            ->callAction(TestAction::make('editContactInformation')->schemaComponent('contactInformation'), data: [
                'email' => 'updated@example.com',
            ])
            ->assertHasNoFormErrors()
            ->assertSchemaComponentHidden('email-health-callout');
    });
});

describe('tabs', function () {
    test('renders successfully with no tabs visible when the user has no relation manager abilities', function () {
        $user = User::factory()->create();
        $user->givePermissionTo('contact.view-any', 'contact.*.view');
        actingAs($user);

        $contact = Contact::factory()->create();

        \Pest\Laravel\get(ContactResource::getUrl('view', ['record' => $contact]))
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

        \Pest\Laravel\get(ContactResource::getUrl('view', ['record' => $contact]))
            ->assertDontSeeLivewire(ServiceRequestsRelationManager::class);

        $user->givePermissionTo('service_request.view-any');

        \Pest\Laravel\get(ContactResource::getUrl('view', ['record' => $contact]))
            ->assertSuccessful()
            ->assertSeeLivewire(ServiceRequestsRelationManager::class);
    });

    test('shows the Assets tab only with an asset_check_out/asset_check_in.view-any ability', function () {
        $user = User::factory()->create();
        $user->givePermissionTo('contact.view-any', 'contact.*.view');
        actingAs($user);

        $contact = Contact::factory()->create();

        \Pest\Laravel\get(ContactResource::getUrl('view', ['record' => $contact]))
            ->assertDontSeeLivewire(AssetCheckOutRelationManager::class)
            ->assertDontSeeLivewire(AssetCheckInRelationManager::class);

        $user->givePermissionTo('asset_check_out.view-any');

        \Pest\Laravel\get(ContactResource::getUrl('view', ['record' => $contact]))
            ->assertSuccessful()
            ->assertSeeLivewire(AssetCheckOutRelationManager::class)
            ->assertDontSeeLivewire(AssetCheckInRelationManager::class);

        $user->givePermissionTo('asset_check_in.view-any');

        \Pest\Laravel\get(ContactResource::getUrl('view', ['record' => $contact]))
            ->assertSuccessful()
            ->assertSeeLivewire(AssetCheckOutRelationManager::class)
            ->assertSeeLivewire(AssetCheckInRelationManager::class);
    });

    test('shows the Files tab only with the engagement_file.view-any ability', function () {
        $user = User::factory()->create();
        $user->givePermissionTo('contact.view-any', 'contact.*.view');
        actingAs($user);

        $contact = Contact::factory()->create();

        \Pest\Laravel\get(ContactResource::getUrl('view', ['record' => $contact]))
            ->assertDontSeeLivewire(EngagementFilesRelationManager::class);

        $user->givePermissionTo('engagement_file.view-any');

        \Pest\Laravel\get(ContactResource::getUrl('view', ['record' => $contact]))
            ->assertSuccessful()
            ->assertSeeLivewire(EngagementFilesRelationManager::class);
    });

    test('shows the Emails tab only with an engagement/engagement_response.view-any ability', function () {
        $user = User::factory()->create();
        $user->givePermissionTo('contact.view-any', 'contact.*.view');
        actingAs($user);

        $contact = Contact::factory()->create();

        \Pest\Laravel\get(ContactResource::getUrl('view', ['record' => $contact]))
            ->assertDontSeeLivewire(EngagementsRelationManager::class);

        $user->givePermissionTo('engagement.view-any');

        \Pest\Laravel\get(ContactResource::getUrl('view', ['record' => $contact]))
            ->assertSuccessful()
            ->assertSeeLivewire(EngagementsRelationManager::class);
    });
});
