<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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

namespace App\Policies;

use App\Models\Tag;
use Laravel\Pennant\Feature;
use App\Models\Authenticatable;
use Illuminate\Auth\Access\Response;

class TagPolicy
{
    public function before(): ?bool
    {
        return Feature::inactive('tags') ? false : null;
    }

    public function viewAny(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['tag.view-any'],
            denyResponse: 'You do not have permission to view tags.'
        );
    }

    public function view(Authenticatable $authenticatable, Tag $tag): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['tag.*.view', "tag.{$tag->id}.view"],
            denyResponse: 'You do not have permission to view this tag.'
        );
    }

    public function create(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'tag.create',
            denyResponse: 'You do not have permission to create tags.'
        );
    }

    public function update(Authenticatable $authenticatable, Tag $tag): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['tag.*.update', "tag.{$tag->id}.update"],
            denyResponse: 'You do not have permission to update this tag.'
        );
    }

    public function delete(Authenticatable $authenticatable, Tag $tag): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['tag.*.delete', "tag.{$tag->id}.delete"],
            denyResponse: 'You do not have permission to delete this tag.'
        );
    }

    public function restore(Authenticatable $authenticatable, Tag $tag): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['tag.*.restore', "tag.{$tag->id}.restore"],
            denyResponse: 'You do not have permission to restore this tag.'
        );
    }

    public function forceDelete(Authenticatable $authenticatable, Tag $tag): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['tag.*.force-delete', "tag.{$tag->id}.force-delete"],
            denyResponse: 'You do not have permission to permanently delete this tag.'
        );
    }
}
