<?php

use App\Models\User;

use function Pest\Laravel\get;

use AidingApp\Task\Models\Task;
use App\Http\Middleware\CheckOlympusKey;

use function Pest\Laravel\withoutMiddleware;

use AidingApp\InventoryManagement\Models\Asset;
use AidingApp\KnowledgeBase\Models\KnowledgeBaseItem;
use AidingApp\ServiceManagement\Models\ChangeRequest;
use AidingApp\ServiceManagement\Models\ServiceRequest;

it('checks the API returns users', function () {
    withoutMiddleware(CheckOlympusKey::class);

    $randomRecords = random_int(1, 100);

    User::factory()->count($randomRecords)->create();

    $response = get('/api/utilization-metrics');

    $data = $response->json('data');

    $response->assertStatus(200);

    expect($data['users'])->toBe($randomRecords);
});

it('checks the API returns service requests', function () {
    withoutMiddleware(CheckOlympusKey::class);

    $randomRecords = random_int(1, 100);

    ServiceRequest::factory()->count($randomRecords)->create();

    $response = get('/api/utilization-metrics');

    $data = $response->json('data');

    $response->assertStatus(200);

    expect($data['service_requests'])->toBe($randomRecords);
});

it('checks the API returns assets', function () {
    withoutMiddleware(CheckOlympusKey::class);

    $randomRecords = random_int(1, 100);

    Asset::factory()->count($randomRecords)->create();

    $response = get('/api/utilization-metrics');

    $data = $response->json('data');

    $response->assertStatus(200);

    expect($data['assets'])->toBe($randomRecords);
});

it('checks the API returns changes', function () {
    withoutMiddleware(CheckOlympusKey::class);

    $randomRecords = random_int(1, 100);

    ChangeRequest::factory()->count($randomRecords)->create();

    $response = get('/api/utilization-metrics');

    $data = $response->json('data');

    $response->assertStatus(200);

    expect($data['changes'])->toBe($randomRecords);
});

it('checks the API returns knowledge base articles', function () {
    withoutMiddleware(CheckOlympusKey::class);

    $randomRecords = random_int(1, 100);

    KnowledgeBaseItem::factory()->count($randomRecords)->create();

    $response = get('/api/utilization-metrics');

    $data = $response->json('data');

    $response->assertStatus(200);

    expect($data['knowledge_base_articles'])->toBe($randomRecords);
});

it('checks the API returns tasks', function () {
    withoutMiddleware(CheckOlympusKey::class);

    $randomRecords = random_int(1, 100);

    Task::factory()->count($randomRecords)->create();

    $response = get('/api/utilization-metrics');

    $data = $response->json('data');

    $response->assertStatus(200);

    expect($data['tasks'])->toBe($randomRecords);
});
