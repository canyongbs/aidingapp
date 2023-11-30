<?php

/*
<COPYRIGHT>

    Copyright © 2022-2023, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

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
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace Assist\Division\Policies;

use App\Models\User;
use Assist\Division\Models\Division;
use Illuminate\Auth\Access\Response;

class DivisionPolicy
{
    public function viewAny(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'division.view-any',
            denyResponse: 'You do not have permission to view divisions.'
        );
    }

    public function view(User $user, Division $division): Response
    {
        return $user->canOrElse(
            abilities: ['division.*.view', "division.{$division->id}.view"],
            denyResponse: 'You do not have permission to view this division.'
        );
    }

    public function create(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'division.create',
            denyResponse: 'You do not have permission to create divisions.'
        );
    }

    public function update(User $user, Division $division): Response
    {
        return $user->canOrElse(
            abilities: ['division.*.update', "division.{$division->id}.update"],
            denyResponse: 'You do not have permission to update this division.'
        );
    }

    public function delete(User $user, Division $division): Response
    {
        return $user->canOrElse(
            abilities: ['division.*.delete', "division.{$division->id}.delete"],
            denyResponse: 'You do not have permission to delete this division.'
        );
    }

    public function restore(User $user, Division $division): Response
    {
        return $user->canOrElse(
            abilities: ['division.*.restore', "division.{$division->id}.restore"],
            denyResponse: 'You do not have permission to restore this division.'
        );
    }

    public function forceDelete(User $user, Division $division): Response
    {
        return $user->canOrElse(
            abilities: ['division.*.force-delete', "division.{$division->id}.force-delete"],
            denyResponse: 'You do not have permission to permanently delete this division.'
        );
    }
}
