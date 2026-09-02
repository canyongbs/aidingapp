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

use App\Support\ScrubSensitiveRequestData;
use Sentry\Event;

it('scrubs password values from secret storage request events', function (Event $event, string $url) {
    $event->setRequest([
        'url' => $url,
        'data' => [
            'value' => 'service-request-password',
            'previous_secret_id' => 'previous-secret-id',
        ],
    ]);

    $scrubbedEvent = ScrubSensitiveRequestData::handle($event);

    assert($scrubbedEvent instanceof Event);

    $request = $scrubbedEvent->getRequest();

    expect($request['data'])->toBe([
        'value' => '[Filtered]',
        'previous_secret_id' => 'previous-secret-id',
    ]);
})->with([
    'portal error event' => fn (): array => [
        Event::createEvent(),
        'https://example.test/api/portal/service-request/store-secret',
    ],
    'portal transaction event' => fn (): array => [
        Event::createTransaction(),
        'https://example.test/api/portal/service-request/store-secret',
    ],
    'assistant error event' => fn (): array => [
        Event::createEvent(),
        'https://example.test/widgets/assistant/api/service-request/store-secret',
    ],
    'assistant transaction event' => fn (): array => [
        Event::createTransaction(),
        'https://example.test/widgets/assistant/api/service-request/store-secret',
    ],
    'staff error event' => fn (): array => [
        Event::createEvent(),
        'https://example.test/service-request/store-secret',
    ],
    'staff transaction event' => fn (): array => [
        Event::createTransaction(),
        'https://example.test/service-request/store-secret',
    ],
]);

it('does not change unrelated request events', function () {
    $event = Event::createEvent();
    $event->setRequest([
        'url' => 'https://example.test/api/portal/service-request/create/type-id',
        'data' => ['value' => 'ordinary-value'],
    ]);

    $scrubbedEvent = ScrubSensitiveRequestData::handle($event);

    assert($scrubbedEvent instanceof Event);

    expect($scrubbedEvent->getRequest()['data'])
        ->toBe(['value' => 'ordinary-value']);
});

it('drops secret reveal request events', function (Event $event) {
    $event->setRequest([
        'url' => 'https://example.test/service-request/reveal-secret',
        'data' => ['secret_id' => 'secret-id'],
    ]);

    expect(ScrubSensitiveRequestData::handle($event))->toBeNull();
})->with([
    'error event' => fn (): Event => Event::createEvent(),
    'transaction event' => fn (): Event => Event::createTransaction(),
]);
