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
use AidingApp\ServiceManagement\Actions\AttachServiceRequestSecrets;
use AidingApp\ServiceManagement\Actions\ResolveServiceRequestSecretEncrypter;
use AidingApp\ServiceManagement\Models\Secret;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\ValidationException;

it('re-encrypts a secret with the service request key and associates it to the service request', function () {
    $contact = Contact::factory()->create();
    $serviceRequest = ServiceRequest::factory()->create();
    $secret = Secret::factory()->for($contact, 'author')->create([
        'value' => Crypt::encryptString('service-request-password'),
    ]);

    app(AttachServiceRequestSecrets::class)($serviceRequest, [$secret->getKey()], $contact);

    $secret->refresh();

    expect($secret->related->is($serviceRequest))->toBeTrue()
        ->and($serviceRequest->refresh()->secret_key)->toBeString()->not->toBeEmpty()
        ->and(fn (): string => Crypt::decryptString($secret->value))->toThrow(DecryptException::class)
        ->and(app(ResolveServiceRequestSecretEncrypter::class)($serviceRequest)->decryptString($secret->value))
        ->toBe('service-request-password');
});

it('rejects duplicate secret ids', function () {
    $contact = Contact::factory()->create();
    $serviceRequest = ServiceRequest::factory()->create();
    $secret = Secret::factory()->for($contact, 'author')->create();

    expect(fn () => app(AttachServiceRequestSecrets::class)(
        $serviceRequest,
        [$secret->getKey(), $secret->getKey()],
        $contact,
    ))->toThrow(ValidationException::class);

    expect($serviceRequest->refresh()->secret_key)->toBeNull()
        ->and($secret->refresh()->related_id)->toBeNull();
});

it('rejects secrets that are not available to the author', function () {
    $contact = Contact::factory()->create();
    $otherContact = Contact::factory()->create();
    $serviceRequest = ServiceRequest::factory()->create();
    $secret = Secret::factory()->for($otherContact, 'author')->create();

    expect(fn () => app(AttachServiceRequestSecrets::class)(
        $serviceRequest,
        [$secret->getKey()],
        $contact,
    ))->toThrow(ValidationException::class);

    expect($serviceRequest->refresh()->secret_key)->toBeNull()
        ->and($secret->refresh()->related_id)->toBeNull();
});
