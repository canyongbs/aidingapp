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

namespace AidingApp\Contact\Policies;

use AidingApp\Contact\Models\Contact;
use App\Models\Authenticatable;
use Filament\Support\Authorization\DenyResponse;
use Illuminate\Auth\Access\Response;

class ContactPolicy
{
    public function viewAny(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'contact.view-any',
            denyResponse: 'You do not have permission to view contacts.'
        );
    }

    public function view(Authenticatable $authenticatable, Contact $contact): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['contact.*.view'],
            denyResponse: 'You do not have permission to view this contact.'
        );
    }

    public function create(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'contact.create',
            denyResponse: 'You do not have permission to create contacts.'
        );
    }

    public function import(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'contact.import',
            denyResponse: 'You do not have permission to import contacts.',
        );
    }

    public function update(Authenticatable $authenticatable, Contact $contact): Response
    {
        if ($contact->isManaged()) {
            return DenyResponse::make(
                'managed_contact',
                message: function (int $failureCount, int $totalCount): string {
                    if ($failureCount === 1 && $totalCount === 1) {
                        return 'This contact is managed and synchronized from a user, so it cannot be edited.';
                    }

                    if ($failureCount === $totalCount) {
                        return 'All of the selected contacts are managed and cannot be edited.';
                    }

                    if ($failureCount === 1) {
                        return 'One of the selected contacts is managed and cannot be edited.';
                    }

                    return "{$failureCount} of the selected contacts are managed and cannot be edited.";
                },
            );
        }

        return $authenticatable->canOrElse(
            abilities: ['contact.*.update'],
            denyResponse: 'You do not have permission to update this contact.'
        );
    }

    public function delete(Authenticatable $authenticatable, Contact $contact): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['contact.*.delete'],
            denyResponse: 'You do not have permission to delete this contact.'
        );
    }

    public function deleteAny(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['contact.*.delete'],
            denyResponse: 'You do not have permission to delete any contact.'
        );
    }

    public function restore(Authenticatable $authenticatable, Contact $contact): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['contact.*.restore'],
            denyResponse: 'You do not have permission to restore this contact.'
        );
    }

    public function restoreAny(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['contact.*.restore'],
            denyResponse: 'You do not have permission to restore any contact.'
        );
    }

    public function forceDelete(Authenticatable $authenticatable, Contact $contact): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['contact.*.force-delete'],
            denyResponse: 'You do not have permission to force delete this contact.'
        );
    }

    public function forceDeleteAny(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['contact.*.force-delete'],
            denyResponse: 'You do not have permission to force delete any contact.'
        );
    }
}
