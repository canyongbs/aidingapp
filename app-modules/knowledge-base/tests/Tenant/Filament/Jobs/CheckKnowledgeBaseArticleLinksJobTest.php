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

use AidingApp\KnowledgeBase\Jobs\CheckKnowledgeBaseArticleLinksJob;
use AidingApp\KnowledgeBase\Models\KnowledgeBaseItem;
use Illuminate\Support\Facades\Http;

test('it clears broken links when article has no content', function () {
    $knowledgeBaseItem = KnowledgeBaseItem::factory()
        ->state([
            'article_details' => null,
            'are_broken_links_detected' => true,
            'broken_links' => ['https://example.com/old-broken'],
        ])
        ->create();

    Http::fake();

    (new CheckKnowledgeBaseArticleLinksJob($knowledgeBaseItem))->handle();

    $knowledgeBaseItem->refresh();

    expect($knowledgeBaseItem->are_broken_links_detected)->toBeFalse();
    expect($knowledgeBaseItem->broken_links)->toBeNull();

    Http::assertNothingSent();
});

test('it clears broken links when article has no urls', function () {
    $knowledgeBaseItem = KnowledgeBaseItem::factory()
        ->state([
            'article_details' => [
                'type' => 'doc',
                'content' => [
                    ['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'Just plain text with no links']]],
                ],
            ],
            'are_broken_links_detected' => true,
            'broken_links' => ['https://example.com/old-broken'],
        ])
        ->create();

    Http::fake();

    (new CheckKnowledgeBaseArticleLinksJob($knowledgeBaseItem))->handle();

    $knowledgeBaseItem->refresh();

    expect($knowledgeBaseItem->are_broken_links_detected)->toBeFalse();
    expect($knowledgeBaseItem->broken_links)->toBeNull();

    Http::assertNothingSent();
});

test('it detects broken links when url returns client error', function () {
    $knowledgeBaseItem = KnowledgeBaseItem::factory()
        ->state([
            'article_details' => [
                'type' => 'doc',
                'content' => [
                    [
                        'type' => 'paragraph',
                        'content' => [
                            [
                                'type' => 'text',
                                'marks' => [
                                    ['type' => 'link', 'attrs' => ['href' => 'https://example.com/broken-page']],
                                ],
                                'text' => 'Broken link',
                            ],
                        ],
                    ],
                ],
            ],
        ])
        ->create();

    Http::fake([
        'https://example.com/broken-page' => Http::response('Not Found', 404),
    ]);

    (new CheckKnowledgeBaseArticleLinksJob($knowledgeBaseItem))->handle();

    $knowledgeBaseItem->refresh();

    expect($knowledgeBaseItem->are_broken_links_detected)->toBeTrue();
    expect($knowledgeBaseItem->broken_links)->toContain('https://example.com/broken-page');
});

test('it detects broken links when url returns server error', function () {
    $knowledgeBaseItem = KnowledgeBaseItem::factory()
        ->state([
            'article_details' => [
                'type' => 'doc',
                'content' => [
                    [
                        'type' => 'paragraph',
                        'content' => [
                            [
                                'type' => 'text',
                                'marks' => [
                                    ['type' => 'link', 'attrs' => ['href' => 'https://example.com/server-error']],
                                ],
                                'text' => 'Server error link',
                            ],
                        ],
                    ],
                ],
            ],
        ])
        ->create();

    Http::fake([
        'https://example.com/server-error' => Http::response('Internal Server Error', 500),
    ]);

    (new CheckKnowledgeBaseArticleLinksJob($knowledgeBaseItem))->handle();

    $knowledgeBaseItem->refresh();

    expect($knowledgeBaseItem->are_broken_links_detected)->toBeTrue();
    expect($knowledgeBaseItem->broken_links)->toContain('https://example.com/server-error');
});

test('it marks no broken links when all urls return success', function () {
    $knowledgeBaseItem = KnowledgeBaseItem::factory()
        ->state([
            'article_details' => [
                'type' => 'doc',
                'content' => [
                    [
                        'type' => 'paragraph',
                        'content' => [
                            [
                                'type' => 'text',
                                'marks' => [
                                    ['type' => 'link', 'attrs' => ['href' => 'https://example.com/valid-page']],
                                ],
                                'text' => 'Valid link',
                            ],
                        ],
                    ],
                ],
            ],
        ])
        ->create();

    Http::fake([
        'https://example.com/valid-page' => Http::response('OK', 200),
    ]);

    (new CheckKnowledgeBaseArticleLinksJob($knowledgeBaseItem))->handle();

    $knowledgeBaseItem->refresh();

    expect($knowledgeBaseItem->are_broken_links_detected)->toBeFalse();
    expect($knowledgeBaseItem->broken_links)->toBeNull();
});

test('it detects only the broken urls when article has mix of valid and broken links', function () {
    $knowledgeBaseItem = KnowledgeBaseItem::factory()
        ->state([
            'article_details' => [
                'type' => 'doc',
                'content' => [
                    [
                        'type' => 'paragraph',
                        'content' => [
                            [
                                'type' => 'text',
                                'marks' => [
                                    ['type' => 'link', 'attrs' => ['href' => 'https://example.com/valid']],
                                ],
                                'text' => 'Valid link',
                            ],
                            [
                                'type' => 'text',
                                'marks' => [
                                    ['type' => 'link', 'attrs' => ['href' => 'https://example.com/broken']],
                                ],
                                'text' => 'Broken link',
                            ],
                        ],
                    ],
                ],
            ],
        ])
        ->create();

    Http::fake([
        'https://example.com/valid' => Http::response('OK', 200),
        'https://example.com/broken' => Http::response('Not Found', 404),
    ]);

    (new CheckKnowledgeBaseArticleLinksJob($knowledgeBaseItem))->handle();

    $knowledgeBaseItem->refresh();

    expect($knowledgeBaseItem->are_broken_links_detected)->toBeTrue();
    expect($knowledgeBaseItem->broken_links)->toContain('https://example.com/broken');
    expect($knowledgeBaseItem->broken_links)->not->toContain('https://example.com/valid');
});

test('it ignores links with ip address hosts', function () {
    $knowledgeBaseItem = KnowledgeBaseItem::factory()
        ->state([
            'article_details' => [
                'type' => 'doc',
                'content' => [
                    [
                        'type' => 'paragraph',
                        'content' => [
                            [
                                'type' => 'text',
                                'marks' => [
                                    ['type' => 'link', 'attrs' => ['href' => 'http://192.168.1.1/page']],
                                ],
                                'text' => 'IP link',
                            ],
                        ],
                    ],
                ],
            ],
        ])
        ->create();

    Http::fake();

    (new CheckKnowledgeBaseArticleLinksJob($knowledgeBaseItem))->handle();

    $knowledgeBaseItem->refresh();

    expect($knowledgeBaseItem->are_broken_links_detected)->toBeFalse();
    expect($knowledgeBaseItem->broken_links)->toBeNull();

    Http::assertNothingSent();
});

test('it ignores relative links without hosts', function () {
    $knowledgeBaseItem = KnowledgeBaseItem::factory()
        ->state([
            'article_details' => [
                'type' => 'doc',
                'content' => [
                    [
                        'type' => 'paragraph',
                        'content' => [
                            [
                                'type' => 'text',
                                'marks' => [
                                    ['type' => 'link', 'attrs' => ['href' => '/relative/path']],
                                ],
                                'text' => 'Relative link',
                            ],
                        ],
                    ],
                ],
            ],
        ])
        ->create();

    Http::fake();

    (new CheckKnowledgeBaseArticleLinksJob($knowledgeBaseItem))->handle();

    $knowledgeBaseItem->refresh();

    expect($knowledgeBaseItem->are_broken_links_detected)->toBeFalse();
    expect($knowledgeBaseItem->broken_links)->toBeNull();

    Http::assertNothingSent();
});

test('it has correct unique id and unique for values', function () {
    $knowledgeBaseItem = KnowledgeBaseItem::factory()->create();

    $job = new CheckKnowledgeBaseArticleLinksJob($knowledgeBaseItem);

    expect($job->uniqueId())->toBe($knowledgeBaseItem->getKey());
    expect($job->uniqueFor())->toBe(600);
});
