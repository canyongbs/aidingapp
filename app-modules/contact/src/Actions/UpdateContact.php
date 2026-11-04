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

namespace AidingApp\Contact\Actions;

use AidingApp\Contact\Models\Contact;
use AidingApp\Contact\Models\ContactType;
use AidingApp\Contact\Models\Organization;
use AidingApp\Contact\Support\ContactFullName;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class UpdateContact
{
    /**
     * Update a contact. The contact is re-read under a row lock, so the full name is derived
     * from the current first and last name and concurrent partial updates cannot leave the
     * name columns inconsistent.
     *
     * @param array<string, mixed> $attributes
     */
    public function __invoke(
        Contact $contact,
        array $attributes,
        ?ContactType $type,
        ?Organization $organization,
        bool $updateOrganization,
    ): Contact {
        return DB::transaction(function () use ($contact, $attributes, $type, $organization, $updateOrganization): Contact {
            $contact = Contact::query()
                ->withoutArchived()
                ->whereKey($contact->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            if (array_key_exists('first_name', $attributes) || array_key_exists('last_name', $attributes)) {
                $firstName = (string) ($attributes['first_name'] ?? $contact->first_name);
                $lastName = (string) ($attributes['last_name'] ?? $contact->last_name);

                if (ContactFullName::exceedsMaxLength($firstName, $lastName)) {
                    throw ValidationException::withMessages(
                        collect(['first_name', 'last_name'])
                            ->filter(fn (string $field): bool => array_key_exists($field, $attributes))
                            ->mapWithKeys(fn (string $field): array => [$field => ContactFullName::tooLongMessage()])
                            ->all(),
                    );
                }

                $attributes['full_name'] = ContactFullName::make($firstName, $lastName);
            }

            $contact->fill($attributes);

            if ($type !== null) {
                $contact->type()->associate($type);
            }

            if ($updateOrganization) {
                $contact->organization()->associate($organization);
            }

            $contact->shouldAutomaticallyMatchOrganization = ! $updateOrganization;
            $contact->save();

            return $contact;
        });
    }
}
