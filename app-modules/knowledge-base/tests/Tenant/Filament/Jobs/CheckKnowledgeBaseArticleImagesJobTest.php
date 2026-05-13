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

use AidingApp\KnowledgeBase\Jobs\CheckKnowledgeBaseArticleImagesJob;
use AidingApp\KnowledgeBase\Models\KnowledgeBaseItem;
use Illuminate\Support\Facades\Http;

test('it clears broken images when article has no content', function () {
    $knowledgeBaseItem = KnowledgeBaseItem::factory()
        ->state([
            'article_details' => null,
            'are_broken_images_detected' => true,
            'broken_images' => ['some-old-uuid'],
        ])
        ->create();

    Http::fake();

    (new CheckKnowledgeBaseArticleImagesJob($knowledgeBaseItem))->handle();

    $knowledgeBaseItem->refresh();

    expect($knowledgeBaseItem->are_broken_images_detected)->toBeFalse();
    expect($knowledgeBaseItem->broken_images)->toBeNull();
});

test('it clears broken images when article has no image nodes', function () {
    $knowledgeBaseItem = KnowledgeBaseItem::factory()
        ->state([
            'article_details' => [
                'type' => 'doc',
                'content' => [
                    ['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'Just plain text']]],
                ],
            ],
            'are_broken_images_detected' => true,
            'broken_images' => ['some-old-uuid'],
        ])
        ->create();

    Http::fake();

    (new CheckKnowledgeBaseArticleImagesJob($knowledgeBaseItem))->handle();

    $knowledgeBaseItem->refresh();

    expect($knowledgeBaseItem->are_broken_images_detected)->toBeFalse();
    expect($knowledgeBaseItem->broken_images)->toBeNull();

    Http::assertNothingSent();
});

test('it marks media image as not broken when file exists on disk', function () {
    $knowledgeBaseItem = KnowledgeBaseItem::factory()
        ->state([
            'article_details' => [
                'type' => 'doc',
                'content' => [],
            ],
        ])
        ->create();

    $media = $knowledgeBaseItem
        ->addMediaFromString('test image content')
        ->usingFileName('test.png')
        ->toMediaCollection('article_details');

    $knowledgeBaseItem->update([
        'article_details' => [
            'type' => 'doc',
            'content' => [
                [
                    'type' => 'image',
                    'attrs' => ['id' => $media->uuid],
                ],
            ],
        ],
    ]);

    Http::fake();

    (new CheckKnowledgeBaseArticleImagesJob($knowledgeBaseItem))->handle();

    $knowledgeBaseItem->refresh();

    expect($knowledgeBaseItem->are_broken_images_detected)->toBeFalse();
    expect($knowledgeBaseItem->broken_images)->toBeNull();
});

test('it ignores external images with relative paths', function () {
    $knowledgeBaseItem = KnowledgeBaseItem::factory()
        ->state([
            'article_details' => [
                'type' => 'doc',
                'content' => [
                    [
                        'type' => 'paragraph',
                        'content' => [
                            [
                                'type' => 'image',
                                'attrs' => ['src' => '/images/local.png'],
                            ],
                        ],
                    ],
                ],
            ],
        ])
        ->create();

    Http::fake();

    (new CheckKnowledgeBaseArticleImagesJob($knowledgeBaseItem))->handle();

    $knowledgeBaseItem->refresh();

    expect($knowledgeBaseItem->are_broken_images_detected)->toBeFalse();
    expect($knowledgeBaseItem->broken_images)->toBeNull();

    Http::assertNothingSent();
});

test('it detects broken external image when url returns error', function () {
    $knowledgeBaseItem = KnowledgeBaseItem::factory()
        ->state([
            'article_details' => [
                'type' => 'doc',
                'content' => [
                    [
                        'type' => 'paragraph',
                        'content' => [
                            [
                                'type' => 'image',
                                'attrs' => ['src' => 'https://example.com/broken-image.png'],
                            ],
                        ],
                    ],
                ],
            ],
        ])
        ->create();

    Http::fake([
        'https://example.com/broken-image.png' => Http::response('Not Found', 404),
    ]);

    (new CheckKnowledgeBaseArticleImagesJob($knowledgeBaseItem))->handle();

    $knowledgeBaseItem->refresh();

    expect($knowledgeBaseItem->are_broken_images_detected)->toBeTrue();
    expect($knowledgeBaseItem->broken_images)->toContain('https://example.com/broken-image.png');
});

test('it has correct unique id and unique for values', function () {
    $knowledgeBaseItem = KnowledgeBaseItem::factory()->create();

    $job = new CheckKnowledgeBaseArticleImagesJob($knowledgeBaseItem);

    expect($job->uniqueId())->toBe($knowledgeBaseItem->getKey());
    expect($job->uniqueFor())->toBe(600);
});
