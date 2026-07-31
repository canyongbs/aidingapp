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

use AidingApp\Engagement\Models\Engagement;

if (! function_exists('richContentWith')) {
    /**
     * @param array<int, mixed> $nodes
     *
     * @return array<string, mixed>
     */
    function richContentWith(array $nodes): array
    {
        return [
            'type' => 'doc',
            'content' => [[
                'type' => 'paragraph',
                'content' => $nodes,
            ]],
        ];
    }
}

if (! function_exists('richContentText')) {
    /**
     * @return array<string, mixed>
     */
    function richContentText(string $text): array
    {
        return richContentWith([['type' => 'text', 'text' => $text]]);
    }
}

it('converts a literal merge tag when creating', function () {
    $engagement = Engagement::factory()->create([
        'body' => richContentText('Hello {{ contact full name }}!'),
    ]);

    expect($engagement->body)->toEqual(richContentWith([
        ['type' => 'text', 'text' => 'Hello '],
        ['type' => 'mergeTag', 'attrs' => ['id' => 'contact full name']],
        ['type' => 'text', 'text' => '!'],
    ]));
});

it('converts a literal merge tag when updating', function () {
    $engagement = Engagement::factory()->create();

    $engagement->update([
        'body' => richContentText('Reach you at {{ contact email }}?'),
    ]);

    expect($engagement->refresh()->body)->toEqual(richContentWith([
        ['type' => 'text', 'text' => 'Reach you at '],
        ['type' => 'mergeTag', 'attrs' => ['id' => 'contact email']],
        ['type' => 'text', 'text' => '?'],
    ]));
});

it('leaves text that does not match a merge tag untouched', function () {
    $body = richContentText('Hello {{ not a merge tag }}!');

    $engagement = Engagement::factory()->create([
        'body' => $body,
    ]);

    expect($engagement->refresh()->body)->toEqual($body);
});

it('leaves an existing merge tag node untouched', function () {
    $body = richContentWith([
        ['type' => 'text', 'text' => 'Hello '],
        ['type' => 'mergeTag', 'attrs' => ['id' => 'contact full name']],
    ]);

    $engagement = Engagement::factory()->create([
        'body' => $body,
    ]);

    expect($engagement->refresh()->body)->toEqual($body);
});
