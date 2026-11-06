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
use AidingApp\Portal\Settings\PortalSettings;
use AidingApp\ServiceManagement\Models\Secret;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestForm;
use AidingApp\ServiceManagement\Models\ServiceRequestFormField;
use AidingApp\ServiceManagement\Models\ServiceRequestPriority;
use AidingApp\ServiceManagement\Models\ServiceRequestStatus;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use App\Features\CheckboxesFormFieldFeature;
use App\Settings\LicenseSettings;
use Illuminate\Support\Facades\Crypt;

use function Pest\Laravel\actingAs;

beforeEach(function (): void {
    $portalSettings = app(PortalSettings::class);
    $portalSettings->knowledge_management_portal_enabled = true;
    $portalSettings->knowledge_management_portal_service_management = true;
    $portalSettings->save();

    $licenseSettings = app(LicenseSettings::class);
    $licenseSettings->data->addons->serviceManagement = true;
    $licenseSettings->save();

    ServiceRequestStatus::firstOrCreate(
        ['name' => 'New'],
        ServiceRequestStatus::factory()->open()->systemProtected()->make()->getAttributes(),
    );
});

it('prohibits a submitted priority when a default priority is configured', function (): void {
    $type = ServiceRequestType::factory()->create();
    $defaultPriority = ServiceRequestPriority::factory()->for($type, 'type')->create();
    $submittedPriority = ServiceRequestPriority::factory()->for($type, 'type')->create();
    $type->update(['default_priority_id' => $defaultPriority->getKey()]);

    $contact = Contact::factory()->create();

    $response = actingAs($contact, 'contact')->postJson(
        route('api.portal.service-request.store', ['type' => $type]),
        [
            'Main' => [
                'title' => 'Portal priority test',
                'description' => 'Configured priority should be enforced.',
                'priority' => $submittedPriority->getKey(),
            ],
        ],
    );

    $response->assertUnprocessable()
        ->assertJsonValidationErrors('Main.priority');

    expect(ServiceRequest::query()->count())->toBe(0);
});

it('requires a submitted priority when the type has no configured default priority', function (): void {
    $type = ServiceRequestType::factory()->create();
    ServiceRequestPriority::factory()->for($type, 'type')->create();

    $contact = Contact::factory()->create();

    $response = actingAs($contact, 'contact')->postJson(
        route('api.portal.service-request.store', ['type' => $type]),
        [
            'Main' => [
                'title' => 'Portal priority test',
                'description' => 'A priority is required.',
            ],
        ],
    );

    $response->assertUnprocessable()
        ->assertJsonValidationErrors('Main.priority');

    expect(ServiceRequest::query()->count())->toBe(0);
});

it('assigns the configured default priority when no priority is submitted', function (): void {
    $type = ServiceRequestType::factory()->create();
    $priority = ServiceRequestPriority::factory()->for($type, 'type')->create();
    $type->update(['default_priority_id' => $priority->getKey()]);
    $contact = Contact::factory()->create();

    $response = actingAs($contact, 'contact')->postJson(
        route('api.portal.service-request.store', ['type' => $type]),
        ['Main' => ['title' => 'Portal priority test', 'description' => 'A priority is required.']],
    );

    $response->assertSuccessful();

    expect(ServiceRequest::query()->sole()->priority_id)->toBe($priority->getKey());
});

it('attaches submitted password secrets to the service request', function (): void {
    $type = ServiceRequestType::factory()->create();
    $priority = ServiceRequestPriority::factory()->for($type, 'type')->create();
    $contact = Contact::factory()->create();
    $field = new ServiceRequestFormField([
        'label' => 'Password',
        'type' => 'password',
        'is_required' => true,
        'config' => [],
    ]);
    $form = ServiceRequestForm::factory()->for($type, 'type')->create();
    $field->submissible()->associate($form);
    $field->save();

    $form->update([
        'content' => [
            'type' => 'doc',
            'content' => [[
                'type' => 'customBlock',
                'attrs' => [
                    'id' => 'password',
                    'config' => [
                        'fieldId' => $field->getKey(),
                        'label' => 'Password',
                        'isRequired' => true,
                    ],
                ],
            ]],
        ],
    ]);

    $secret = Secret::factory()
        ->for($contact, 'author')
        ->create(['value' => Crypt::encryptString('service-request-password')]);

    $response = actingAs($contact, 'contact')->postJson(
        route('api.portal.service-request.store', ['type' => $type]),
        [
            'Main' => [
                'title' => 'Password submission test',
                'description' => 'Testing secret attachment.',
                'priority' => $priority->getKey(),
            ],
            'Details' => [
                $field->getKey() => $secret->getKey(),
            ],
        ],
    );

    $response->assertSuccessful();

    $serviceRequest = ServiceRequest::query()->sole();

    expect($secret->refresh()->related->is($serviceRequest))->toBeTrue();
});

it('rejects a password secret owned by another contact', function (): void {
    $type = ServiceRequestType::factory()->create();
    $priority = ServiceRequestPriority::factory()->for($type, 'type')->create();
    $contact = Contact::factory()->create();
    $field = new ServiceRequestFormField([
        'label' => 'Password',
        'type' => 'password',
        'is_required' => true,
        'config' => [],
    ]);
    $form = ServiceRequestForm::factory()->for($type, 'type')->create();
    $field->submissible()->associate($form);
    $field->save();

    $form->update([
        'content' => [
            'type' => 'doc',
            'content' => [[
                'type' => 'customBlock',
                'attrs' => [
                    'id' => 'password',
                    'config' => [
                        'fieldId' => $field->getKey(),
                        'label' => 'Password',
                        'isRequired' => true,
                    ],
                ],
            ]],
        ],
    ]);

    $secret = Secret::factory()
        ->for(Contact::factory(), 'author')
        ->create();

    actingAs($contact, 'contact')->postJson(
        route('api.portal.service-request.store', ['type' => $type]),
        [
            'Main' => [
                'title' => 'Invalid password submission test',
                'description' => 'Testing secret ownership.',
                'priority' => $priority->getKey(),
            ],
            'Details' => [
                $field->getKey() => $secret->getKey(),
            ],
        ],
    )
        ->assertUnprocessable()
        ->assertJsonValidationErrors("Details.{$field->getKey()}");

    expect(ServiceRequest::query()->exists())->toBeFalse()
        ->and($secret->refresh()->related_id)->toBeNull();
});

describe('checkbox form fields', function (): void {
    $setUp = function (string $fieldType, array $config = []): array {
        $type = ServiceRequestType::factory()->create();
        $priority = ServiceRequestPriority::factory()->for($type, 'type')->create();
        $field = new ServiceRequestFormField([
            'label' => 'Agree',
            'type' => $fieldType,
            'is_required' => true,
            'config' => $config,
        ]);
        $form = ServiceRequestForm::factory()->for($type, 'type')->create();
        $field->submissible()->associate($form);
        $field->save();

        $form->update([
            'content' => [
                'type' => 'doc',
                'content' => [[
                    'type' => 'customBlock',
                    'attrs' => ['id' => $fieldType, 'config' => ['fieldId' => $field->getKey(), 'label' => 'Agree', 'isRequired' => true, ...$config]],
                ]],
            ],
        ]);

        return [$type, $priority, $field];
    };

    // TODO: Cleanup Task (convert-checkbox-form-fields): delete this test.
    it('submits a legacy checkbox field and shows its answer while the feature flag is inactive', function () use ($setUp): void {
        CheckboxesFormFieldFeature::deactivate();

        [$type, $priority, $field] = $setUp('checkbox');
        $contact = Contact::factory()->create();

        actingAs($contact, 'contact')->postJson(
            route('api.portal.service-request.store', ['type' => $type]),
            [
                'Main' => ['title' => 'Legacy checkbox', 'description' => 'Legacy checkbox field.', 'priority' => $priority->getKey()],
                'Details' => [$field->getKey() => true],
            ],
        )->assertSuccessful();

        $submission = ServiceRequest::query()->sole()->serviceRequestFormSubmission;

        expect($submission->fields()->whereKey($field)->firstOrFail()->pivot->getAttribute('response'))->toBeTruthy();

        $html = view('service-management::submission', ['submission' => $submission->load('submissible', 'fields')])->render();

        expect($html)->toContain('Agree')->and($html)->not->toContain('No response');
    });

    it('submits a checkboxes field and shows its answer while the feature flag is active', function () use ($setUp): void {
        [$type, $priority, $field] = $setUp('checkboxes', ['hasOtherOption' => false, 'options' => [['label' => 'Red', 'value' => 'red'], ['label' => 'Blue', 'value' => 'blue']]]);
        $contact = Contact::factory()->create();

        actingAs($contact, 'contact')->postJson(
            route('api.portal.service-request.store', ['type' => $type]),
            [
                'Main' => ['title' => 'Checkboxes', 'description' => 'Checkboxes field.', 'priority' => $priority->getKey()],
                'Details' => [$field->getKey() => ['red']],
            ],
        )->assertSuccessful();

        $submission = ServiceRequest::query()->sole()->serviceRequestFormSubmission;

        expect($submission->fields()->whereKey($field)->firstOrFail()->pivot->getAttribute('response'))->toBe('["red"]');

        $html = view('service-management::submission', ['submission' => $submission->load('submissible', 'fields')])->render();

        expect($html)->toContain('Red')->and($html)->not->toContain('No response');
    });

    it('ignores a field with an unknown type instead of failing the submission', function () use ($setUp): void {
        [$type, $priority, $field] = $setUp('removed_block_type');
        $contact = Contact::factory()->create();

        actingAs($contact, 'contact')->postJson(
            route('api.portal.service-request.store', ['type' => $type]),
            ['Main' => ['title' => 'Unknown type', 'description' => 'Unknown field type.', 'priority' => $priority->getKey()]],
        )->assertSuccessful();

        expect(ServiceRequest::query()->count())->toBe(1);
    });
});
