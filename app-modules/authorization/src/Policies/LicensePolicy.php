<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

    Aiding App™ is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Aiding App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    <https://www.canyongbs.com> or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AidingApp\Authorization\Policies;

use AidingApp\Authorization\Models\License;
use App\Models\Authenticatable;
use Illuminate\Auth\Access\Response;

class LicensePolicy
{
    public function viewAny(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'license.view-any',
            denyResponse: 'You do not have permission to view licenses.'
        );
    }

    public function view(Authenticatable $authenticatable, License $license): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['license.*.view', "license.{$license->id}.view"],
            denyResponse: 'You do not have permission to view this license.'
        );
    }

    public function create(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'license.create',
            denyResponse: 'You do not have permission to create licenses.'
        );
    }

    public function update(Authenticatable $authenticatable, License $license): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['license.*.update', "license.{$license->id}.update"],
            denyResponse: 'You do not have permission to update this license.'
        );
    }

    public function delete(Authenticatable $authenticatable, License $license): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['license.*.delete', "license.{$license->id}.delete"],
            denyResponse: 'You do not have permission to delete this license.'
        );
    }

    public function restore(Authenticatable $authenticatable, License $license): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['license.*.restore', "license.{$license->id}.restore"],
            denyResponse: 'You do not have permission to restore this license.'
        );
    }

    public function forceDelete(Authenticatable $authenticatable, License $license): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['license.*.force-delete', "license.{$license->id}.force-delete"],
            denyResponse: 'You do not have permission to permanently delete this license.'
        );
    }
}
