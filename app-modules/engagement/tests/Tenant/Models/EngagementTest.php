<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

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

use AidingApp\Contact\Models\Contact;
use AidingApp\Engagement\Models\Engagement;
use Illuminate\Support\HtmlString;

/**
 * @param  array<int, array<string, mixed>>  $content
 *
 * @return array<string, mixed>
 */
function tiptapDoc(array $content): array
{
    return ['type' => 'doc', 'content' => $content];
}

/**
 * @param  array<int, array<string, mixed>>  $content
 *
 * @return array<string, mixed>
 */
function tiptapParagraph(array $content): array
{
    return ['type' => 'paragraph', 'content' => $content];
}

/** @return array<string, string> */
function tiptapText(string $text): array
{
    return ['type' => 'text', 'text' => $text];
}

/** @return array<string, mixed> */
function tiptapMergeTag(string $id): array
{
    return ['type' => 'mergeTag', 'attrs' => ['id' => $id]];
}

it('returns the body as html', function () {
    $engagement = Engagement::factory()->email()->create([
        'body' => tiptapDoc([
            tiptapParagraph([
                tiptapText('Hello world'),
            ]),
        ]),
    ]);

    $body = $engagement->getBody();

    expect($body)
        ->toBeInstanceOf(HtmlString::class)
        ->and((string) $body)
        ->toBe('<p>Hello world</p>');
});

it('resolves merge tags in the body html', function () {
    $contact = Contact::factory()->create([
        'full_name' => 'Jane Doe',
    ]);

    $engagement = Engagement::factory()->email()->create([
        'recipient_id' => $contact->getKey(),
        'recipient_type' => $contact->getMorphClass(),
        'body' => tiptapDoc([
            tiptapParagraph([
                tiptapText('Dear '),
                tiptapMergeTag('contact full name'),
                tiptapText(', welcome!'),
            ]),
        ]),
    ]);

    expect((string) $engagement->getBody())
        ->toBe('<p>Dear <span data-type="mergeTag" data-id="contact full name">Jane Doe</span>, welcome!</p>');
});

it('resolves contact merge tags in the body', function () {
    $contact = Contact::factory()->create();

    $engagement = Engagement::factory()->email()->create([
        'recipient_id' => $contact->getKey(),
        'recipient_type' => $contact->getMorphClass(),
        'body' => tiptapDoc([
            tiptapParagraph([
                tiptapText('To: '),
                tiptapMergeTag('contact full name'),
                tiptapText(' ('),
                tiptapMergeTag('contact email'),
                tiptapText(')'),
            ]),
        ]),
    ]);

    expect((string) $engagement->getBody())
        ->toContain($contact->full_name)
        ->toContain($contact->email);
});
