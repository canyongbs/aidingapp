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

use AidingApp\Contact\Models\Contact;
use AidingApp\Notification\Notifications\Channels\MailChannel;
use AidingApp\ServiceManagement\Enums\ServiceRequestEmailTemplateType;
use AidingApp\ServiceManagement\Enums\ServiceRequestTypeEmailTemplateRole;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestPriority;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use AidingApp\ServiceManagement\Models\ServiceRequestTypeEmailTemplate;
use AidingApp\ServiceManagement\Notifications\SendEducatableServiceRequestOpenedNotification;
use AidingApp\ServiceManagement\Notifications\ServiceRequestCreated;
use App\Models\User;

/**
 * @param array<int, string> $tagIds
 *
 * @return array<string, mixed>
 */
function mergeTagBody(array $tagIds): array
{
    $content = [['type' => 'text', 'text' => 'Hello ']];

    foreach ($tagIds as $tagId) {
        $content[] = ['type' => 'mergeTag', 'attrs' => ['id' => $tagId]];
        $content[] = ['type' => 'text', 'text' => ' | '];
    }

    return [
        'type' => 'doc',
        'content' => [['type' => 'paragraph', 'content' => $content]],
    ];
}

it('exposes the expected merge tags with stable keys and friendly labels in order', function () {
    $mergeTags = ServiceRequestTypeEmailTemplate::getMergeTags();

    expect(array_keys($mergeTags))->toBe([
        'recipient name',
        'contact name',
        'assigned staff name',
        'created date',
        'updated date',
        'service request number',
        'title',
        'description',
        'status',
        'type',
        'recent update',
    ]);

    // The new recipient tag carries an apostrophe only in its label, never in its key.
    expect($mergeTags['recipient name'])->toBe("recipient's name");
    expect(array_keys($mergeTags))->not->toContain("recipient's name");

    expect($mergeTags['contact name'])->toBe("contact's name");
    expect($mergeTags['assigned staff name'])->toBe('assigned manager');
});

it('substitutes the recipient name in the body and subject while the relabeled contact merge tag still resolves', function () {
    $contact = Contact::factory()->create();

    $serviceRequestType = ServiceRequestType::factory()->create();
    $priority = ServiceRequestPriority::factory()->for($serviceRequestType, 'type')->create();

    $serviceRequest = ServiceRequest::factory()
        ->for($priority, 'priority')
        ->for($contact, 'respondent')
        ->create();

    $template = ServiceRequestTypeEmailTemplate::factory()
        ->for($serviceRequestType, 'serviceRequestType')
        ->state([
            'type' => ServiceRequestEmailTemplateType::Created,
            'role' => ServiceRequestTypeEmailTemplateRole::Manager,
            'body' => mergeTagBody(['recipient name', 'contact name']),
            'subject' => mergeTagBody(['recipient name']),
        ])
        ->create();

    $mergeData = $serviceRequest->getTemplateMergeData(recipientName: 'Jane Recipient');

    $body = (string) $template->getBody($mergeData)?->toHtml();
    $subject = (string) $template->getSubject($mergeData);

    expect($body)
        ->toContain('Jane Recipient')
        ->toContain(e($contact->{$contact::displayNameKey()}));

    expect($subject)->toContain('Jane Recipient');
});

it('renders the staff recipient full name into a staff notification body', function () {
    $user = User::factory()->create(['name' => 'Staff Recipient']);

    $serviceRequestType = ServiceRequestType::factory()->create();
    $priority = ServiceRequestPriority::factory()->for($serviceRequestType, 'type')->create();
    $serviceRequest = ServiceRequest::factory()->for($priority, 'priority')->create();

    $template = ServiceRequestTypeEmailTemplate::factory()
        ->for($serviceRequestType, 'serviceRequestType')
        ->state([
            'type' => ServiceRequestEmailTemplateType::Created,
            'role' => ServiceRequestTypeEmailTemplateRole::Manager,
            'body' => mergeTagBody(['recipient name']),
        ])
        ->create();

    $message = (new ServiceRequestCreated($serviceRequest, $template, MailChannel::class))->toMail($user);

    expect($message->viewData['content'])->toContain('Staff Recipient');
});

it('renders the contact recipient full name into an educatable notification body', function () {
    $contact = Contact::factory()->create();

    $serviceRequestType = ServiceRequestType::factory()->create();
    $priority = ServiceRequestPriority::factory()->for($serviceRequestType, 'type')->create();
    $serviceRequest = ServiceRequest::factory()
        ->for($priority, 'priority')
        ->for($contact, 'respondent')
        ->create();

    $template = ServiceRequestTypeEmailTemplate::factory()
        ->for($serviceRequestType, 'serviceRequestType')
        ->state([
            'type' => ServiceRequestEmailTemplateType::Created,
            'role' => ServiceRequestTypeEmailTemplateRole::Customer,
            'body' => mergeTagBody(['recipient name']),
        ])
        ->create();

    $message = (new SendEducatableServiceRequestOpenedNotification($serviceRequest, $template))->toMail($contact);

    expect($message->viewData['content'])->toContain($contact->{$contact::displayNameKey()});
});
