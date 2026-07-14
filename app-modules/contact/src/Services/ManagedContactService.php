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

namespace AidingApp\Contact\Services;

use AidingApp\Contact\Models\Contact;
use App\Models\User;

class ManagedContactService
{
    /**
     * Turn a User into a managed contact (or update the existing link), keeping
     * the Contact's data synchronized with the User. If a Contact already exists
     * for the User's email address it is linked and overridden instead of creating
     * a duplicate. Soft-deleted matches are restored.
     */
    public function enable(User $user, string $contactTypeId): Contact
    {
        $contact = $this->resolveContactFor($user) ?? new Contact();

        if ($contact->trashed()) {
            $contact->deleted_at = null;
        }

        $contact->fill($this->mapUserAttributes($user));
        $contact->type_id = $contactTypeId;
        $contact->user_id = $user->getKey();
        $contact->save();

        return $contact;
    }

    /**
     * Unlink the managed contact from the User, leaving the Contact in place and
     * editable again.
     */
    public function disable(User $user): void
    {
        $contact = $user->managedContact()->first();

        if (is_null($contact)) {
            return;
        }

        $contact->user_id = null;
        $contact->save();
    }

    /**
     * Re-synchronize the linked managed contact's data from the User. Does nothing
     * when the User has no managed contact. The Contact Type is intentionally left
     * untouched here — it is only set when managing is enabled.
     */
    public function sync(User $user): void
    {
        $contact = $user->managedContact()->first();

        if (is_null($contact)) {
            return;
        }

        $contact->fill($this->mapUserAttributes($user));
        $contact->save();
    }

    protected function resolveContactFor(User $user): ?Contact
    {
        $linked = $user->managedContact()->first();

        if (! is_null($linked)) {
            return $linked;
        }

        if (blank($user->email)) {
            return null;
        }

        return Contact::withTrashed()
            ->where('email', $user->email)
            ->where(function ($query) use ($user) {
                $query->whereNull('user_id')
                    ->orWhere('user_id', $user->getKey());
            })
            ->first();
    }

    /**
     * @return array<string, mixed>
     */
    protected function mapUserAttributes(User $user): array
    {
        [$firstName, $lastName] = $this->splitName($user->name);

        return [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'full_name' => trim($user->name),
            'email' => $user->email,
            'job_title' => $user->job_title,
            'phone' => $user->work_number,
            'mobile' => $user->mobile,
        ];
    }

    /**
     * @return array{0: string, 1: string}
     */
    protected function splitName(string $name): array
    {
        $name = trim($name);

        $parts = explode(' ', $name, 2);

        return [$parts[0], $parts[1] ?? ''];
    }
}
