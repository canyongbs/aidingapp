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

namespace AidingApp\KnowledgeBase\Policies;

use AidingApp\Authorization\Enums\LicenseType;
use AidingApp\KnowledgeBase\Models\KnowledgeBaseStatus;
use App\Concerns\PerformsFeatureChecks;
use App\Concerns\PerformsLicenseChecks;
use App\Enums\Feature;
use App\Features\SettingsPermissions;
use App\Models\Authenticatable;
use App\Policies\Contracts\PerformsChecksBeforeAuthorization;
use Illuminate\Auth\Access\Response;

class KnowledgeBaseStatusPolicy implements PerformsChecksBeforeAuthorization
{
    use PerformsLicenseChecks;
    use PerformsFeatureChecks;

    public function before(Authenticatable $authenticatable): ?Response
    {
        if (! is_null($response = $this->hasAnyLicense($authenticatable, [LicenseType::RecruitmentCrm]))) {
            return $response;
        }

        if (! is_null($response = $this->hasFeatures())) {
            return $response;
        }

        return null;
    }

    public function viewAny(Authenticatable $authenticatable): Response
    {
        if (SettingsPermissions::active()) {
            return $authenticatable->canOrElse(
                abilities: 'settings.view-any',
                denyResponse: 'You do not have permission to view any knowledge base statuses.'
            );
        }

        return $authenticatable->canOrElse(
            abilities: 'product_admin.view-any',
            denyResponse: 'You do not have permission to view any knowledge base statuses.'
        );
    }

    public function view(Authenticatable $authenticatable, KnowledgeBaseStatus $knowledgeBaseStatus): Response
    {
        if (SettingsPermissions::active()) {
            return $authenticatable->canOrElse(
                abilities: 'settings.*.view',
                denyResponse: 'You do not have permission to view this knowledge base status.'
            );
        }

        return $authenticatable->canOrElse(
            abilities: ["product_admin.{$knowledgeBaseStatus->getKey()}.view"],
            denyResponse: 'You do not have permission to view this knowledge base status.'
        );
    }

    public function create(Authenticatable $authenticatable): Response
    {
        if (SettingsPermissions::active()) {
            return $authenticatable->canOrElse(
                abilities: 'settings.create',
                denyResponse: 'You do not have permission to create knowledge base statuses.'
            );
        }

        return $authenticatable->canOrElse(
            abilities: 'product_admin.create',
            denyResponse: 'You do not have permission to create knowledge base statuses.'
        );
    }

    public function update(Authenticatable $authenticatable, KnowledgeBaseStatus $knowledgeBaseStatus): Response
    {
        if (SettingsPermissions::active()) {
            return $authenticatable->canOrElse(
                abilities: 'settings.*.update',
                denyResponse: 'You do not have permission to update this knowledge base status.'
            );
        }

        return $authenticatable->canOrElse(
            abilities: ["product_admin.{$knowledgeBaseStatus->getKey()}.update"],
            denyResponse: 'You do not have permission to update this knowledge base status.'
        );
    }

    public function delete(Authenticatable $authenticatable, KnowledgeBaseStatus $knowledgeBaseStatus): Response
    {
        if (SettingsPermissions::active()) {
            return $authenticatable->canOrElse(
                abilities: 'settings.*.delete',
                denyResponse: 'You do not have permission to delete this knowledge base status.'
            );
        }

        return $authenticatable->canOrElse(
            abilities: ["product_admin.{$knowledgeBaseStatus->getKey()}.delete"],
            denyResponse: 'You do not have permission to delete this knowledge base status.'
        );
    }

    public function restore(Authenticatable $authenticatable, KnowledgeBaseStatus $knowledgeBaseStatus): Response
    {
        if (SettingsPermissions::active()) {
            return $authenticatable->canOrElse(
                abilities: 'settings.*.restore',
                denyResponse: 'You do not have permission to restore this knowledge base status.'
            );
        }

        return $authenticatable->canOrElse(
            abilities: ["product_admin.{$knowledgeBaseStatus->getKey()}.restore"],
            denyResponse: 'You do not have permission to restore this knowledge base status.'
        );
    }

    public function forceDelete(Authenticatable $authenticatable, KnowledgeBaseStatus $knowledgeBaseStatus): Response
    {
        if (SettingsPermissions::active()) {
            return $authenticatable->canOrElse(
                abilities: 'settings.*.force-delete',
                denyResponse: 'You do not have permission to permanently delete this knowledge base status.'
            );
        }

        return $authenticatable->canOrElse(
            abilities: ["product_admin.{$knowledgeBaseStatus->getKey()}.force-delete"],
            denyResponse: 'You do not have permission to permanently delete this knowledge base status.'
        );
    }

    protected function requiredFeatures(): array
    {
        return [Feature::KnowledgeManagement];
    }
}
