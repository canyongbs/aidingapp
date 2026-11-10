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
use AidingApp\Contact\Models\ContactType;
use AidingApp\ServiceManagement\Models\Advisory;
use AidingApp\ServiceManagement\Models\Scopes\VisibleToPortalContact;
use App\Features\AdvisoryVisibilityFeature;

it('shows public advisories to everyone', function (): void {
    $advisory = Advisory::factory()->create();

    expect(Advisory::query()->tap(new VisibleToPortalContact(null))->pluck('id')->all())->toBe([$advisory->getKey()])
        ->and(Advisory::query()->tap(new VisibleToPortalContact(Contact::factory()->create()))->pluck('id')->all())->toBe([$advisory->getKey()]);
});

it('shows authenticated advisories to signed-in contacts only', function (): void {
    $advisory = Advisory::factory()->authenticated()->create();

    expect(Advisory::query()->tap(new VisibleToPortalContact(null))->exists())->toBeFalse()
        ->and(Advisory::query()->tap(new VisibleToPortalContact(Contact::factory()->create()))->pluck('id')->all())->toBe([$advisory->getKey()]);
});

it('shows advisories restricted to specific contact types to contacts of those types only', function (): void {
    $allowedType = ContactType::factory()->create();

    $advisory = Advisory::factory()->restrictedToContactTypes($allowedType, ContactType::factory()->create())->create();

    expect(Advisory::query()->tap(new VisibleToPortalContact(null))->exists())->toBeFalse()
        ->and(Advisory::query()->tap(new VisibleToPortalContact(Contact::factory()->create()))->exists())->toBeFalse()
        ->and(Advisory::query()->tap(new VisibleToPortalContact(Contact::factory()->for($allowedType, 'type')->create()))->pluck('id')->all())->toBe([$advisory->getKey()]);
});

it('ignores the contact types of an advisory that is no longer restricted', function (): void {
    $advisory = Advisory::factory()->restrictedToContactTypes(ContactType::factory()->create())->create(['is_visibility_restricted' => false]);

    expect(Advisory::query()->tap(new VisibleToPortalContact(Contact::factory()->create()))->pluck('id')->all())->toBe([$advisory->getKey()]);
});

it('only shows advisories to signed-in contacts until `AdvisoryVisibilityFeature` is active', function (): void {
    AdvisoryVisibilityFeature::deactivate();

    $advisory = Advisory::factory()->authenticated()->create();

    expect(Advisory::query()->tap(new VisibleToPortalContact(null))->exists())->toBeFalse()
        ->and(Advisory::query()->tap(new VisibleToPortalContact(Contact::factory()->create()))->pluck('id')->all())->toBe([$advisory->getKey()]);
});
