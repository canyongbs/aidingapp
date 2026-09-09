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

use AidingApp\Ai\Models\AiMessageFile;
use AidingApp\IntegrationOpenAi\Models\OpenAiVectorStore;
use AidingApp\IntegrationOpenAi\Services\OpenAiGptTestService;
use AidingApp\KnowledgeBase\Models\KnowledgeBaseItem;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

use function Pest\Laravel\assertSoftDeleted;

it('skips uploading a file that has no parsing results instead of sending an empty file to OpenAI', function () {
    Http::fake([
        '*/files*' => Http::response([
            'id' => fake()->uuid(),
        ], 200),
        '*/vector_stores*' => Http::response([
            'id' => fake()->uuid(),
        ], 200),
    ]);

    $service = app(OpenAiGptTestService::class);

    $file = AiMessageFile::factory()->create([
        'parsing_results' => '',
    ]);

    expect($service->areFilesReady([$file]))
        ->toBeTrue();

    Http::assertNotSent(fn (Request $request): bool => str_contains($request->url(), '/files'));
    Http::assertNotSent(fn (Request $request): bool => str_contains($request->url(), '/vector_stores'));

    expect($file->openAiVectorStore)
        ->toBeNull();
});

it('uploads files with parsing results while excluding files that have none', function () {
    Http::fake([
        '*/files*' => Http::response([
            'id' => $fileId = fake()->uuid(),
        ], 200),
        '*/vector_stores*' => Http::response([
            'id' => $vectorStoreId = fake()->uuid(),
        ], 200),
    ]);

    $service = app(OpenAiGptTestService::class);

    $fileWithResults = AiMessageFile::factory()->create();
    $blankFile = AiMessageFile::factory()->create([
        'parsing_results' => '',
    ]);

    expect($service->areFilesReady([$fileWithResults, $blankFile]))
        ->toBeFalse();

    expect($fileWithResults->openAiVectorStore)
        ->vector_store_file_id->toBe($fileId)
        ->vector_store_id->toBe($vectorStoreId);

    expect($blankFile->openAiVectorStore)
        ->toBeNull();
});

it('removes an indexed knowledge base item when it no longer has parsing results', function () {
    Http::fake([
        '*/vector_stores/*/files*' => Http::response([
            'data' => [['id' => $fileId = (string) Str::uuid()]],
        ]),
        '*/files/*' => Http::response(),
        '*/vector_stores/*' => Http::response(),
    ]);

    $service = app(OpenAiGptTestService::class);

    $article = KnowledgeBaseItem::factory()->create();

    $vectorStore = OpenAiVectorStore::factory()->create([
        'deployment_hash' => $service->getDeploymentHash(),
        'vector_store_file_id' => $fileId,
        'file_type' => $article->getMorphClass(),
        'file_id' => $article->getKey(),
    ]);

    expect($service->getReadyVectorStoreId([$article]))
        ->toBe($vectorStore->vector_store_id);

    $article->update([
        'article_details' => [
            'type' => 'doc',
            'content' => [['type' => 'paragraph']],
        ],
    ]);

    expect($article->getParsingResults())
        ->toBeEmpty()
        ->and($service->areFilesReady([$article]))
        ->toBeTrue()
        ->and($service->getReadyVectorStoreId([$article]))
        ->toBeNull();

    assertSoftDeleted($vectorStore);

    Http::assertSent(fn (Request $request): bool => $request->method() === 'DELETE'
        && str_contains($request->url(), "/files/{$fileId}"));
});
