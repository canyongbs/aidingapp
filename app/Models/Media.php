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

namespace App\Models;

use AidingApp\Contact\Models\Contact;
use App\Features\MediaCreatedByFeature;
use App\Observers\MediaObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Spatie\MediaLibrary\MediaCollections\Models\Media as SpatieMedia;

/**
 * @mixin IdeHelperMedia
 */
#[ObservedBy([MediaObserver::class])]
class Media extends SpatieMedia
{
    /**
     * @return MorphTo<Model, $this>
     */
    public function createdBy(): MorphTo
    {
        return $this->morphTo('createdBy');
    }

    public function getCreatedByNameAttribute(): string
    {
        if (! MediaCreatedByFeature::active() || ! ($creator = $this->createdBy)) {
            return 'N/A';
        }

        return match (true) {
            $creator instanceof User => (string) $creator->name,
            $creator instanceof Contact => trim("{$creator->first_name} {$creator->last_name}") ?: 'N/A',
            default => 'N/A',
        };
    }

    public function getCreatedBySubLabelAttribute(): ?string
    {
        if (! MediaCreatedByFeature::active() || ! ($creator = $this->createdBy)) {
            return null;
        }

        return match (true) {
            $creator instanceof User => $this->getUserSubLabel($creator),
            $creator instanceof Contact => $this->getContactSubLabel($creator),
            default => null,
        };
    }

    private function getUserSubLabel(User $creator): ?string
    {
        $jobTitle = (string) ($creator->job_title ?? '');
        $teamName = (string) ($creator->team->name ?? '');

        if ($jobTitle && $teamName) {
            return "{$jobTitle} ({$teamName})";
        }

        return $jobTitle ?: ($teamName ?: null);
    }

    private function getContactSubLabel(Contact $creator): ?string
    {
        $typeName = (string) ($creator->type->name ?? '');
        $orgName = (string) ($creator->organization->name ?? '');

        if ($typeName && $orgName) {
            return "{$typeName} - {$orgName}";
        }

        return $typeName ?: ($orgName ?: null);
    }
}
