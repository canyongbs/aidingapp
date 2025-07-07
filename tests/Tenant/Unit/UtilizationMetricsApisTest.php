<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

    Aiding App™ is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Aiding App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    <https://www.canyongbs.com> or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

use AidingApp\InventoryManagement\Models\Asset;
use AidingApp\KnowledgeBase\Models\KnowledgeBaseItem;
use AidingApp\ServiceManagement\Models\ChangeRequest;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\Task\Models\Task;
use App\Models\User;
use App\Multitenancy\Http\Middleware\CheckOlympusKey;

use function Pest\Laravel\get;
use function Pest\Laravel\withoutMiddleware;

beforeEach(function () {
    withoutMiddleware(CheckOlympusKey::class);
});

it('checks the API returns users', function () {
    $randomRecords = random_int(1, 10);

    User::factory()->count($randomRecords)->create();

    $softDeleteUser = User::factory()->create();
    $softDeleteUser->delete();

    $response = get('/api/utilization-metrics');

    $data = $response->json('data');

    $response->assertStatus(200);

    expect($data['users'])->toBe($randomRecords);
});

it('checks the API returns service requests', function () {
    $randomRecords = random_int(1, 10);

    ServiceRequest::factory()->count($randomRecords)->create();

    $softDeleteServiceRequest = ServiceRequest::factory()->create();
    $softDeleteServiceRequest->delete();

    $response = get('/api/utilization-metrics');

    $data = $response->json('data');

    $response->assertStatus(200);

    expect($data['service_requests'])->toBe($randomRecords);
});

it('checks the API returns assets', function () {
    $randomRecords = random_int(1, 10);

    Asset::factory()->count($randomRecords)->create();

    $softDeleteAsset = Asset::factory()->create();
    $softDeleteAsset->delete();

    $response = get('/api/utilization-metrics');

    $data = $response->json('data');

    $response->assertStatus(200);

    expect($data['assets'])->toBe($randomRecords);
});

it('checks the API returns changes', function () {
    $randomRecords = random_int(1, 10);

    ChangeRequest::factory()->count($randomRecords)->create();

    $response = get('/api/utilization-metrics');

    $data = $response->json('data');

    $response->assertStatus(200);

    expect($data['changes'])->toBe($randomRecords);
});

it('checks the API returns knowledge base articles', function () {
    $randomRecords = random_int(1, 10);

    KnowledgeBaseItem::factory()->count($randomRecords)->create();

    $softDeleteKnowledgeBaseItem = KnowledgeBaseItem::factory()->create();
    $softDeleteKnowledgeBaseItem->delete();

    $response = get('/api/utilization-metrics');

    $data = $response->json('data');

    $response->assertStatus(200);

    expect($data['knowledge_base_articles'])->toBe($randomRecords);
});

it('checks the API returns tasks', function () {
    $randomRecords = random_int(1, 10);

    Task::factory()->count($randomRecords)->create();

    $softDeleteTask = Task::factory()->create();
    $softDeleteTask->delete();

    $response = get('/api/utilization-metrics');

    $data = $response->json('data');

    $response->assertStatus(200);

    expect($data['tasks'])->toBe($randomRecords);
});
