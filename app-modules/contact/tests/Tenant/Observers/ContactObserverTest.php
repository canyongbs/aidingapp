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

use AidingApp\Contact\Actions\MatchContactToOrganization;
use AidingApp\Contact\Models\Contact;
use Mockery\MockInterface;

it('delegates organization matching when a contact is created', function () {
    $contact = Contact::factory()->make();

    $this->mock(MatchContactToOrganization::class, function (MockInterface $mock) use ($contact): void {
        $mock->shouldReceive('__invoke')
            ->once()
            ->with($contact);
    });

    $contact->save();
});

it('delegates organization matching when a contact is updated', function () {
    $contact = Contact::factory()->createQuietly();

    $this->mock(MatchContactToOrganization::class, function (MockInterface $mock) use ($contact): void {
        $mock->shouldReceive('__invoke')
            ->once()
            ->with($contact);
    });

    $contact->first_name = 'Updated';
    $contact->save();
});

it('clears the bounce flag when the email changes', function () {
    $contact = Contact::factory()->bounced()->create(['email' => 'original@example.com']);

    expect($contact->email_bounce)->toBeTrue();

    $contact->update(['email' => 'changed@example.com']);

    expect($contact->fresh()->email_bounce)->toBeFalse();
});

it('leaves the bounce flag true when another attribute changes', function () {
    $contact = Contact::factory()->bounced()->create(['first_name' => 'Original']);

    expect($contact->email_bounce)->toBeTrue();

    $contact->update(['first_name' => 'Changed']);

    expect($contact->fresh()->email_bounce)->toBeTrue();
});

it('leaves the bounce flag true when the email is set to the same value', function () {
    $contact = Contact::factory()->bounced()->create(['email' => 'same@example.com']);

    expect($contact->email_bounce)->toBeTrue();

    $contact->update(['email' => 'same@example.com']);

    expect($contact->fresh()->email_bounce)->toBeTrue();
});

it('keeps the bounce flag true when the email changes and email_bounce is explicitly set true in the same save', function () {
    $contact = Contact::factory()->create(['email' => 'original@example.com', 'email_bounce' => false]);

    expect($contact->email_bounce)->toBeFalse();

    $contact->update([
        'email' => 'changed@example.com',
        'email_bounce' => true,
    ]);

    expect($contact->fresh()->email_bounce)->toBeTrue();
});
