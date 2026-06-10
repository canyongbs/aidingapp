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
      of the licensor in the software. Any use of the licensor's trademarks is subject
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
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use AidingApp\ServiceManagement\Models\ServiceRequestTypeCategory;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\getJson;

beforeEach(function () {
    $portalSettings = app(PortalSettings::class);
    $portalSettings->knowledge_management_portal_enabled = true;
    $portalSettings->ai_support_assistant = true;
    $portalSettings->save();
});

function authenticatedWidgetRequest(string $url): \Illuminate\Testing\TestResponse
{
    $contact = Contact::factory()->create();
    $contact->createToken('assistant-widget-access-token');

    actingAs($contact, 'contact');

    return getJson($url, ['Origin' => config('app.url')]);
}

it('does not return categories that have no service request types', function () {
    $categoryWithType = ServiceRequestTypeCategory::factory()->create(['parent_id' => null]);
    ServiceRequestType::factory()->create(['category_id' => $categoryWithType->id]);

    $emptyCategory = ServiceRequestTypeCategory::factory()->create(['parent_id' => null]);

    $response = authenticatedWidgetRequest(route('widgets.assistant.api.service-request-types'));

    $response->assertOk();

    $categoryIds = collect($response->json('categories'))->pluck('id');

    expect($categoryIds)->toContain($categoryWithType->id)
        ->and($categoryIds)->not->toContain($emptyCategory->id);
});

it('returns a parent category when a child category has types', function () {
    $parentCategory = ServiceRequestTypeCategory::factory()->create(['parent_id' => null]);
    $childCategory = ServiceRequestTypeCategory::factory()->create(['parent_id' => $parentCategory->id]);
    ServiceRequestType::factory()->create(['category_id' => $childCategory->id]);

    $response = authenticatedWidgetRequest(route('widgets.assistant.api.service-request-types'));

    $response->assertOk();

    $categoryIds = collect($response->json('categories'))->pluck('id');

    expect($categoryIds)->toContain($parentCategory->id);
});

it('does not return a parent category when all child categories are empty', function () {
    $parentCategory = ServiceRequestTypeCategory::factory()->create(['parent_id' => null]);
    ServiceRequestTypeCategory::factory()->create(['parent_id' => $parentCategory->id]);

    $response = authenticatedWidgetRequest(route('widgets.assistant.api.service-request-types'));

    $response->assertOk();

    $categoryIds = collect($response->json('categories'))->pluck('id');

    expect($categoryIds)->not->toContain($parentCategory->id);
});

it('does not return archived service request types as part of a category', function () {
    $categoryWithOnlyArchivedType = ServiceRequestTypeCategory::factory()->create(['parent_id' => null]);
    ServiceRequestType::factory()->create([
        'category_id' => $categoryWithOnlyArchivedType->id,
        'archived_at' => now(),
    ]);

    $categoryWithActiveType = ServiceRequestTypeCategory::factory()->create(['parent_id' => null]);
    ServiceRequestType::factory()->create(['category_id' => $categoryWithActiveType->id]);

    $response = authenticatedWidgetRequest(route('widgets.assistant.api.service-request-types'));

    $response->assertOk();

    $categoryIds = collect($response->json('categories'))->pluck('id');

    expect($categoryIds)->toContain($categoryWithActiveType->id)
        ->and($categoryIds)->not->toContain($categoryWithOnlyArchivedType->id);
});
