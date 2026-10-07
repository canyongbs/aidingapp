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
use AidingApp\Portal\Actions\GeneratePortalEmbedCode;
use AidingApp\Portal\Enums\PortalType;
use AidingApp\Portal\Settings\PortalSettings;
use App\Http\Middleware\TrackContactPresence;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\getJson;

it('updates contact last_activity_at on a standard page request', function () {
    $contact = Contact::factory()->create(['last_activity_at' => null]);

    $request = Request::create('/portal', 'GET');
    $request->setUserResolver(fn (?string $guard = null): ?Contact => $guard === 'contact' ? $contact : null);

    $middleware = new TrackContactPresence();
    $middleware->handle($request, fn () => new Response());

    $contact->refresh();

    expect($contact->last_activity_at)->not->toBeNull();
});

it('tracks contacts on portal page requests', function () {
    $portalSettings = app(PortalSettings::class);
    $portalSettings->knowledge_management_portal_enabled = true;
    $portalSettings->save();

    $embedCode = Mockery::mock(GeneratePortalEmbedCode::class);
    $embedCode
        ->shouldReceive('handle')
        ->once()
        ->with(PortalType::KnowledgeManagement)
        ->andReturn('<div></div>');
    app()->instance(GeneratePortalEmbedCode::class, $embedCode);

    $portalContact = Contact::factory()->create(['last_activity_at' => null]);

    actingAs($portalContact, 'contact');

    get(route('portal.show'))->assertSuccessful();

    expect($portalContact->refresh()->last_activity_at)->not->toBeNull();
});

it('tracks the portal contact when both contact and web guards are authenticated', function () {
    $portalSettings = app(PortalSettings::class);
    $portalSettings->knowledge_management_portal_enabled = true;
    $portalSettings->save();

    $embedCode = Mockery::mock(GeneratePortalEmbedCode::class);
    $embedCode
        ->shouldReceive('handle')
        ->once()
        ->with(PortalType::KnowledgeManagement)
        ->andReturn('<div></div>');
    app()->instance(GeneratePortalEmbedCode::class, $embedCode);

    $portalContact = Contact::factory()->create(['last_activity_at' => null]);
    $portalUser = User::factory()->create();
    $portalUser->managedContact()->save($portalContact);

    actingAs($portalContact, 'contact');
    // Keep the existing web session active alongside the portal's contact session.
    actingAs($portalUser, 'web');

    get(route('portal.show'))->assertSuccessful();

    expect($portalContact->refresh()->last_activity_at)->not->toBeNull();
});

it('tracks contacts on authenticated portal API requests', function () {
    $portalSettings = app(PortalSettings::class);
    $portalSettings->knowledge_management_portal_enabled = true;
    $portalSettings->save();

    $contact = Contact::factory()->create(['last_activity_at' => null]);
    $token = $contact->createToken('external-portal-widget')->plainTextToken;

    getJson(route('api.portal.define'), [
        'Authorization' => "Bearer {$token}",
    ])->assertSuccessful();

    expect($contact->refresh()->last_activity_at)->not->toBeNull();
});

it('does not update a managed contact when its user visits the admin panel', function () {
    $adminContact = Contact::factory()->create(['last_activity_at' => null]);
    $adminUser = User::factory()->create();
    $adminUser->managedContact()->save($adminContact);

    actingAs($adminUser, 'web');

    get('/')->assertSuccessful();

    expect($adminContact->refresh()->last_activity_at)->toBeNull();
});
