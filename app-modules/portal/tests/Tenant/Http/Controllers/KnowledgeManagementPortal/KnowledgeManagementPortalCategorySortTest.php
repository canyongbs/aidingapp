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

use AidingApp\KnowledgeBase\Models\KnowledgeBaseCategory;
use AidingApp\Portal\Settings\PortalSettings;

use function Pest\Laravel\getJson;

beforeEach(function () {
    $settings = app(PortalSettings::class);
    $settings->knowledge_management_portal_enabled = true;
    $settings->save();
});

it('returns parent categories ordered by sort on the portal', function () {
    KnowledgeBaseCategory::factory()->create(['name' => 'Zebra', 'sort' => 3]);
    KnowledgeBaseCategory::factory()->create(['name' => 'Apple', 'sort' => 1]);
    KnowledgeBaseCategory::factory()->create(['name' => 'Mango', 'sort' => 2]);

    $response = getJson(route('api.portal.category.index'));

    $response->assertOk();

    $names = collect($response->json())->pluck('name')->toArray();

    expect($names)->toBe(['Apple', 'Mango', 'Zebra']);
});

it('returns sub categories ordered by sort on the portal', function () {
    $parent = KnowledgeBaseCategory::factory()->create(['name' => 'Parent', 'sort' => 1]);

    KnowledgeBaseCategory::factory()->create(['name' => 'Zebra Sub', 'parent_id' => $parent->id, 'sort' => 3]);
    KnowledgeBaseCategory::factory()->create(['name' => 'Apple Sub', 'parent_id' => $parent->id, 'sort' => 1]);
    KnowledgeBaseCategory::factory()->create(['name' => 'Mango Sub', 'parent_id' => $parent->id, 'sort' => 2]);

    $response = getJson(route('api.portal.category.show', ['category' => $parent->slug]));

    $response->assertOk();

    $subNames = collect($response->json('category.subCategories'))->pluck('name')->toArray();

    expect($subNames)->toBe(['Apple Sub', 'Mango Sub', 'Zebra Sub']);
});
