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

namespace AidingApp\Portal\Actions;

use AidingApp\Contact\Models\Contact;
use AidingApp\ServiceManagement\Actions\ResolveServiceRequestSecretEncrypter;
use AidingApp\ServiceManagement\Models\Secret;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use Illuminate\Support\Facades\Crypt;
use Symfony\Component\HttpFoundation\Response;

class AttachServiceRequestSecrets
{
    /** @param array<int, string> $secretIds */
    public function execute(ServiceRequest $serviceRequest, array $secretIds, Contact $author): void
    {
        if ($secretIds === []) {
            return;
        }

        $serviceRequestEncrypter = app(ResolveServiceRequestSecretEncrypter::class)($serviceRequest);

        foreach (array_unique($secretIds) as $secretId) {
            $secret = Secret::query()
                ->whereKey($secretId)
                ->whereNull('related_id')
                ->where('author_type', $author->getMorphClass())
                ->where('author_id', $author->getKey())
                ->first();

            abort_if(is_null($secret), Response::HTTP_FORBIDDEN);

            $secret->value = $serviceRequestEncrypter->encryptString(
                Crypt::decryptString($secret->value)
            );

            $secret->related()->associate($serviceRequest);

            $secret->save();
        }
    }
}
