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

use AidingApp\ServiceManagement\Enums\ServiceRequestEmailTemplateType;
use AidingApp\ServiceManagement\Enums\ServiceRequestTypeEmailTemplateRole;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use AidingApp\ServiceManagement\Models\ServiceRequestTypeEmailTemplate;
use AidingApp\ServiceManagement\Notifications\Concerns\FetchServiceRequestTemplate;

function fetchServiceRequestTemplateDoc(string $text): array
{
    return ['type' => 'doc', 'content' => [['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => $text]]]]];
}

function fetchServiceRequestTemplateFetcher(): object
{
    return new class () {
        use FetchServiceRequestTemplate;
    };
}

test('it returns the per-type template when one exists', function () {
    $type = ServiceRequestType::factory()->create();

    $typeTemplate = ServiceRequestTypeEmailTemplate::query()->create([
        'service_request_type_id' => $type->getKey(),
        'type' => ServiceRequestEmailTemplateType::Created,
        'role' => ServiceRequestTypeEmailTemplateRole::Customer,
        'subject' => fetchServiceRequestTemplateDoc('Type subject'),
        'body' => fetchServiceRequestTemplateDoc('Type body'),
    ]);

    $result = fetchServiceRequestTemplateFetcher()->fetchTemplate($type, ServiceRequestEmailTemplateType::Created, ServiceRequestTypeEmailTemplateRole::Customer);

    expect($result?->getKey())->toBe($typeTemplate->getKey());
    expect($result?->subject)->toEqual(fetchServiceRequestTemplateDoc('Type subject'));
});

test('it returns null when no per-type template exists', function () {
    $type = ServiceRequestType::factory()->create();

    expect(fetchServiceRequestTemplateFetcher()->fetchTemplate($type, ServiceRequestEmailTemplateType::Created, ServiceRequestTypeEmailTemplateRole::Customer))->toBeNull();
});
