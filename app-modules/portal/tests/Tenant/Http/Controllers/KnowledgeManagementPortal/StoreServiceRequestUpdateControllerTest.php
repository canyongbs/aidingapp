<?php

use AidingApp\Contact\Models\Contact;
use AidingApp\Portal\Settings\PortalSettings;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use App\Settings\LicenseSettings;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
    ])
    ->only();