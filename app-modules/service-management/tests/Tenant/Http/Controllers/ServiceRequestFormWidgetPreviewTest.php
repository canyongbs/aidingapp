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

use AidingApp\ServiceManagement\Models\ServiceRequestForm;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use App\Models\User;
use App\Settings\LicenseSettings;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

beforeEach(function () {
    $settings = app(LicenseSettings::class);
    $settings->data->addons->serviceManagement = true;
    $settings->save();
});

it('redirects unauthenticated users to the login page when accessing the preview-entry endpoint', function () {
    $type = ServiceRequestType::factory()->create();

    $form = new ServiceRequestForm(['name' => 'Test Form']);
    $form->type()->associate($type);
    $form->save();

    get(route('service-request-forms.preview-entry', ['serviceRequestForm' => $form]))
        ->assertRedirect();
});

it('is forbidden when service management is not enabled on the preview-entry endpoint', function () {
    $settings = app(LicenseSettings::class);
    $settings->data->addons->serviceManagement = false;
    $settings->save();

    $user = User::factory()->create();

    $type = ServiceRequestType::factory()->create();

    $form = new ServiceRequestForm(['name' => 'Test Form']);
    $form->type()->associate($type);
    $form->save();

    actingAs($user)
        ->get(route('service-request-forms.preview-entry', ['serviceRequestForm' => $form]))
        ->assertForbidden();
});

it('always returns is_authenticated as false in preview mode regardless of the form setting', function () {
    $user = User::factory()->create();

    $type = ServiceRequestType::factory()->create();

    // Form explicitly requires authentication
    $form = new ServiceRequestForm([
        'name' => 'Auth Required Form',
        'is_authenticated' => true,
    ]);
    $form->type()->associate($type);
    $form->save();

    $response = actingAs($user)
        ->getJson(route('service-request-forms.preview-entry', ['serviceRequestForm' => $form]))
        ->assertSuccessful()
        ->assertJsonPath('is_authenticated', false);

    // The response must not contain an authentication_url because the widget
    // should not prompt for an email/code when rendering in preview mode.
    $response->assertJsonMissingPath('authentication_url');
});

it('does not include a submission_url in the preview-entry response', function () {
    $user = User::factory()->create();

    $type = ServiceRequestType::factory()->create();

    $form = new ServiceRequestForm(['name' => 'Test Form']);
    $form->type()->associate($type);
    $form->save();

    // No submission_url means the widget cannot POST a real submission,
    // which is the mechanism that blocks submissions in preview mode.
    actingAs($user)
        ->getJson(route('service-request-forms.preview-entry', ['serviceRequestForm' => $form]))
        ->assertSuccessful()
        ->assertJsonMissingPath('submission_url');
});

it('returns the form name and schema in the preview-entry response', function () {
    $user = User::factory()->create();

    $type = ServiceRequestType::factory()->create();

    $form = new ServiceRequestForm(['name' => 'My Preview Form']);
    $form->type()->associate($type);
    $form->save();

    actingAs($user)
        ->getJson(route('service-request-forms.preview-entry', ['serviceRequestForm' => $form]))
        ->assertSuccessful()
        ->assertJsonPath('name', 'My Preview Form')
        ->assertJsonStructure(['schema', 'primary_color']);
});

it('strips the internal version suffix from the name in the preview-entry response', function () {
    $user = User::factory()->create();

    $type = ServiceRequestType::factory()->create();

    $form = new ServiceRequestForm(['name' => 'Password Reset Form (3)']);
    $form->type()->associate($type);
    $form->save();

    actingAs($user)
        ->getJson(route('service-request-forms.preview-entry', ['serviceRequestForm' => $form]))
        ->assertSuccessful()
        ->assertJsonPath('name', 'Password Reset Form');
});
