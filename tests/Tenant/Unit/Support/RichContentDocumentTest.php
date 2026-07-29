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

use App\Support\RichContentDocument;

dataset('empty rich content documents', [
    'null' => [null],
    'empty string' => [''],
    'empty content array' => [['type' => 'doc', 'content' => []]],
    'no content key' => [['type' => 'doc']],
    'single empty paragraph' => [['type' => 'doc', 'content' => [['type' => 'paragraph']]]],
    'single paragraph with empty content' => [['type' => 'doc', 'content' => [['type' => 'paragraph', 'content' => []]]]],
    'multiple empty paragraphs' => [['type' => 'doc', 'content' => [['type' => 'paragraph'], ['type' => 'paragraph']]]],
    'paragraph with alignment but no content' => [['type' => 'doc', 'content' => [['type' => 'paragraph', 'attrs' => ['textAlign' => 'start']]]]],
    'whitespace only text' => [['type' => 'doc', 'content' => [['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => '   ']]]]]],
]);

dataset('populated rich content documents', [
    'text' => [['type' => 'doc', 'content' => [['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'Example Subject']]]]]],
    'merge tag only' => [['type' => 'doc', 'content' => [['type' => 'paragraph', 'content' => [['type' => 'mergeTag', 'attrs' => ['id' => 'title']]]]]]],
    'custom block only' => [['type' => 'doc', 'content' => [['type' => 'customBlock', 'attrs' => ['id' => 'service_request_type_email_template_button']]]]],
    'image only' => [['type' => 'doc', 'content' => [['type' => 'image', 'attrs' => ['src' => 'https://example.com/logo.png']]]]],
    'horizontal rule only' => [['type' => 'doc', 'content' => [['type' => 'horizontalRule']]]],
    'text nested in a list' => [['type' => 'doc', 'content' => [['type' => 'bulletList', 'content' => [['type' => 'listItem', 'content' => [['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'Item']]]]]]]]]],
    'text after an empty paragraph' => [['type' => 'doc', 'content' => [['type' => 'paragraph'], ['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'Body']]]]]],
]);

test('it treats an empty document as having no content', function (mixed $document) {
    expect(RichContentDocument::hasContent($document))->toBeFalse();
})->with('empty rich content documents');

test('it treats a populated document as having content', function (mixed $document) {
    expect(RichContentDocument::hasContent($document))->toBeTrue();
})->with('populated rich content documents');
