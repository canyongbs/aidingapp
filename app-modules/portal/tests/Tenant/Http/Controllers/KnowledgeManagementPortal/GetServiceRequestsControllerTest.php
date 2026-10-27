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
use AidingApp\ServiceManagement\Models\ServiceRequest;
use App\Settings\LicenseSettings;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\getJson;
use function Tests\asSuperAdmin;

beforeEach(function (): void {
    $portalSettings = app(PortalSettings::class);
    $portalSettings->knowledge_management_portal_enabled = true;
    $portalSettings->knowledge_management_portal_service_management = true;
    $portalSettings->save();

    $licenseSettings = app(LicenseSettings::class);
    $licenseSettings->data->addons->serviceManagement = true;
    $licenseSettings->save();

    asSuperAdmin();
});

it('displays the service request data', function (): void {
    $contact = Contact::factory()->create();
    $serviceRequest = ServiceRequest::factory(['respondent_id' => $contact->getKey()])->create();

    actingAs($contact, 'contact');

    getJson(route('api.portal.service-request.show', ['serviceRequest' => $serviceRequest->getKey()]))
        ->assertOk()
        ->assertJsonPath('serviceRequestDetails.id', $serviceRequest->getKey())
        ->assertJsonPath('serviceRequestDetails.serviceRequestNumber', $serviceRequest->service_request_number);
});

describe('authorization', function (): void {
    it('does not show another contact\'s service request', function (): void {
        $serviceRequest = ServiceRequest::factory(['respondent_id' => Contact::factory()->create()->getKey()])->create();

        actingAs(Contact::factory()->create(), 'contact');

        getJson(route('api.portal.service-request.show', ['serviceRequest' => $serviceRequest->getKey()]))
            ->assertNotFound();
    });

    it('denies access to guests', function (): void {
        $serviceRequest = ServiceRequest::factory()->create();

        auth()->logout();

        getJson(route('api.portal.service-request.show', ['serviceRequest' => $serviceRequest->getKey()]))
            ->assertUnauthorized();
    });
});
