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

use AidingApp\Contact\Listeners\SaveBouncedContactEmail;
use AidingApp\Contact\Models\Contact;
use AidingApp\IntegrationAwsSesEventHandling\DataTransferObjects\SesEventData;
use AidingApp\IntegrationAwsSesEventHandling\Events\SesBounceEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

use function Tests\loadFixtureFromModule;

function makeSesBounceEventData(string $bounceType, string $emailAddress): SesEventData
{
    $bounce = loadFixtureFromModule('integration-aws-ses-event-handling', 'Bounce');

    data_set($bounce, 'bounce.bounceType', $bounceType);
    data_set($bounce, 'bounce.bouncedRecipients.0.emailAddress', $emailAddress);
    data_set($bounce, 'mail.tags.app_message_id.0', (string) Str::uuid());

    $sns = loadFixtureFromModule('integration-aws-ses-event-handling', 'sns-notification');
    $sns['Message'] = json_encode($bounce);

    return SesEventData::fromRequest(Request::create('/', 'POST', content: json_encode($sns)));
}

it('flags a contact whose email permanently bounced', function () {
    $contact = Contact::factory()->create([
        'email' => 'recipient@example.com',
    ]);

    expect($contact->email_bounce)->toBeFalse();

    $data = makeSesBounceEventData('Permanent', 'recipient@example.com');

    (new SaveBouncedContactEmail())->handle(new SesBounceEvent($data));

    expect($contact->refresh()->email_bounce)->toBeTrue();
});

it('flags a contact whose email matches a differently cased address', function () {
    $contact = Contact::factory()->create([
        'email' => 'Recipient@Example.com',
    ]);

    expect($contact->email_bounce)->toBeFalse();

    $data = makeSesBounceEventData('Permanent', 'recipient@example.com');

    (new SaveBouncedContactEmail())->handle(new SesBounceEvent($data));

    expect($contact->refresh()->email_bounce)->toBeTrue();
});

it('does not flag a contact for a transient bounce', function () {
    $contact = Contact::factory()->create([
        'email' => 'recipient@example.com',
    ]);

    expect($contact->email_bounce)->toBeFalse();

    $data = makeSesBounceEventData('Transient', 'recipient@example.com');

    (new SaveBouncedContactEmail())->handle(new SesBounceEvent($data));

    expect($contact->refresh()->email_bounce)->toBeFalse();
});

it('does not flag a contact for an undetermined bounce', function () {
    $contact = Contact::factory()->create([
        'email' => 'recipient@example.com',
    ]);

    expect($contact->email_bounce)->toBeFalse();

    $data = makeSesBounceEventData('Undetermined', 'recipient@example.com');

    (new SaveBouncedContactEmail())->handle(new SesBounceEvent($data));

    expect($contact->refresh()->email_bounce)->toBeFalse();
});

it('does nothing when no contact matches the bounced address', function () {
    Contact::factory()->create([
        'email' => 'someone-else@example.com',
    ]);

    $data = makeSesBounceEventData('Permanent', 'unknown@example.com');

    (new SaveBouncedContactEmail())->handle(new SesBounceEvent($data));

    expect(Contact::query()->where('email', 'unknown@example.com')->exists())->toBeFalse();
});

it('does not flag a soft-deleted contact with the bounced address', function () {
    $contact = Contact::factory()->create([
        'email' => 'recipient@example.com',
    ]);
    $contact->delete();

    expect($contact->email_bounce)->toBeFalse();

    $data = makeSesBounceEventData('Permanent', 'recipient@example.com');

    (new SaveBouncedContactEmail())->handle(new SesBounceEvent($data));

    expect($contact->refresh()->email_bounce)->toBeFalse();
});

it('does not flag the new address of a contact who already replaced the bounced address', function () {
    $contact = Contact::factory()->create([
        'email' => 'old-address@example.com',
    ]);

    $contact->email = 'new-address@example.com';
    $contact->save();

    expect($contact->email_bounce)->toBeFalse();

    $data = makeSesBounceEventData('Permanent', 'old-address@example.com');

    (new SaveBouncedContactEmail())->handle(new SesBounceEvent($data));

    expect($contact->refresh()->email_bounce)->toBeFalse();
});

it('flags the contact when the bounce event is dispatched through the event system', function () {
    $contact = Contact::factory()->create([
        'email' => 'recipient@example.com',
    ]);

    expect($contact->email_bounce)->toBeFalse();

    $data = makeSesBounceEventData('Permanent', 'recipient@example.com');

    SesBounceEvent::dispatch($data);

    expect($contact->refresh()->email_bounce)->toBeTrue();
});
