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

namespace AidingApp\Engagement\Policies;

use AidingApp\Engagement\Models\EngagementFile;
use App\Models\Authenticatable;
use Illuminate\Auth\Access\Response;

class EngagementFilePolicy
{
    public function viewAny(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'engagement_file.view-any',
            denyResponse: 'You do not have permissions to view engagement files.'
        );
    }

    public function view(Authenticatable $authenticatable, EngagementFile $engagementFile): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['engagement_file.*.view', "engagement_file.{$engagementFile->id}.view"],
            denyResponse: 'You do not have permissions to view this engagement file.'
        );
    }

    public function create(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'engagement_file.create',
            denyResponse: 'You do not have permissions to create engagement files.'
        );
    }

    public function update(Authenticatable $authenticatable, EngagementFile $engagementFile): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['engagement_file.*.update', "engagement_file.{$engagementFile->id}.update"],
            denyResponse: 'You do not have permissions to update this engagement file.'
        );
    }

    public function delete(Authenticatable $authenticatable, EngagementFile $engagementFile): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['engagement_file.*.delete', "engagement_file.{$engagementFile->id}.delete"],
            denyResponse: 'You do not have permissions to delete this engagement file.'
        );
    }

    public function restore(Authenticatable $authenticatable, EngagementFile $engagementFile): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['engagement_file.*.restore', "engagement_file.{$engagementFile->id}.restore"],
            denyResponse: 'You do not have permissions to restore this engagement file.'
        );
    }

    public function forceDelete(Authenticatable $authenticatable, EngagementFile $engagementFile): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['engagement_file.*.force-delete', "engagement_file.{$engagementFile->id}.force-delete"],
            denyResponse: 'You do not have permissions to force delete this engagement file.'
        );
    }
}
