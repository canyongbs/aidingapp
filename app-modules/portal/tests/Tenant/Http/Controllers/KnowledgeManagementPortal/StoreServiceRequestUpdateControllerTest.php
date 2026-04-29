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
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\post;
use function Tests\asSuperAdmin;

it('validates the inputs', function (array $data, array $error) {
    $portalSettings = app(PortalSettings::class);
    $portalSettings->knowledge_management_portal_enabled = true;
    $portalSettings->knowledge_management_portal_service_management = true;
    $portalSettings->save();
    $settings = app(LicenseSettings::class);
    $settings->data->addons->serviceManagement = true;
    $settings->save();

    Storage::fake('s3');
    asSuperAdmin();

    $contact = Contact::factory()->create();

    $serviceRequestId = ServiceRequest::factory(['respondent_id' => $contact->getKey()])->create()->getKey();

    $contact->createToken('knowledge-management-portal-access-token');

    actingAs($contact, 'contact');

    $formData = [
        'description' => 'This is a sample description.',
        'serviceRequestId' => $serviceRequestId,
        'files' => [
            UploadedFile::fake()->create('testFile', 1000, 'text/plain'),
        ],
    ];

    $formData = array_merge($formData, $data);

    post(route('api.portal.service-request-update.storeServiceRequestUpdate', ['serviceRequest' => $serviceRequestId]), $formData)
        ->assertSessionHasErrors($error);
})
    ->with([
        'description is required' => [
            ['description' => null],
            ['description'],
        ],
        'description must be a string' => [
            ['description' => 10],
            ['description'],
        ],
        'files must be an array' => [
            ['files' => 10],
            ['files'],
        ],
        'each item within files must be a file' => [
            ['files' => [10]],
            ['files.0'],
        ],
    ]);
