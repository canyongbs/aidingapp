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

namespace AidingApp\Report\Enums;

use AidingApp\Report\Filament\Pages\AdvisoryManagement;
use AidingApp\Report\Filament\Pages\AiClarification;
use AidingApp\Report\Filament\Pages\AiResolution;
use AidingApp\Report\Filament\Pages\AiSupportAssistant;
use AidingApp\Report\Filament\Pages\AssetManagement;
use AidingApp\Report\Filament\Pages\ChangeManagement;
use AidingApp\Report\Filament\Pages\ContractManagement;
use AidingApp\Report\Filament\Pages\KnowledgeBase;
use AidingApp\Report\Filament\Pages\LicenseManagement;
use AidingApp\Report\Filament\Pages\Projects;
use AidingApp\Report\Filament\Pages\ServiceMonitoring;
use AidingApp\Report\Filament\Pages\ServiceRequestFeedback;
use AidingApp\Report\Filament\Pages\ServiceRequests;
use AidingApp\Report\Models\ReportDepartmentAccess;
use AidingApp\Report\Models\ReportUserAccess;
use App\Enums\Feature;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Gate;

enum ReportAccessKey: string
{
    case ServiceRequests = 'service-requests';
    case ServiceRequestFeedback = 'service-request-feedback';
    case Projects = 'projects';
    case KnowledgeBase = 'knowledge-base';
    case AssetManagement = 'asset-management';
    case AdvisoryManagement = 'advisory-management';
    case ContractManagement = 'contract-management';
    case LicenseManagement = 'license-management';
    case ChangeManagement = 'change-management';
    case ServiceMonitoring = 'service-monitoring';
    case AiSupportAssistant = 'ai-support-assistant';
    case AiClarification = 'ai-clarification';
    case AiResolution = 'ai-resolution';

    /**
     * @return class-string
     */
    public function getPageClass(): string
    {
        return match ($this) {
            self::ServiceRequests => ServiceRequests::class,
            self::ServiceRequestFeedback => ServiceRequestFeedback::class,
            self::Projects => Projects::class,
            self::KnowledgeBase => KnowledgeBase::class,
            self::AssetManagement => AssetManagement::class,
            self::AdvisoryManagement => AdvisoryManagement::class,
            self::ContractManagement => ContractManagement::class,
            self::LicenseManagement => LicenseManagement::class,
            self::ChangeManagement => ChangeManagement::class,
            self::ServiceMonitoring => ServiceMonitoring::class,
            self::AiSupportAssistant => AiSupportAssistant::class,
            self::AiClarification => AiClarification::class,
            self::AiResolution => AiResolution::class,
        };
    }

    public function getName(): string
    {
        return match ($this) {
            self::ServiceRequests => 'Service Requests',
            self::ServiceRequestFeedback => 'Service Request Feedback',
            self::Projects => 'Projects',
            self::KnowledgeBase => 'Knowledge Base',
            self::AssetManagement => 'Asset Management',
            self::AdvisoryManagement => 'Advisory Management',
            self::ContractManagement => 'Contract Management',
            self::LicenseManagement => 'License Management',
            self::ChangeManagement => 'Change Management',
            self::ServiceMonitoring => 'Service Monitoring',
            self::AiSupportAssistant => 'AI Support Assistant',
            self::AiClarification => 'AI Clarification',
            self::AiResolution => 'AI Resolution',
        };
    }

    public function getCategory(): string
    {
        return match ($this) {
            self::ServiceRequests,
            self::ServiceRequestFeedback,
            self::KnowledgeBase,
            self::AssetManagement,
            self::AdvisoryManagement,
            self::ContractManagement,
            self::LicenseManagement,
            self::ChangeManagement,
            self::ServiceMonitoring => 'Service Desk',

            self::Projects => 'Projects',

            self::AiSupportAssistant,
            self::AiClarification,
            self::AiResolution => 'Artificial Intelligence',
        };
    }

    public function isAvailableForTenant(): bool
    {
        return match ($this) {
            self::ServiceRequests => Gate::check(Feature::ServiceManagement->getGateName()),

            self::ServiceRequestFeedback => Gate::check(Feature::ServiceManagement->getGateName())
                && Gate::check(Feature::FeedbackManagement->getGateName()),

            self::KnowledgeBase => Gate::check(Feature::KnowledgeManagement->getGateName()),

            self::AssetManagement => Gate::check(Feature::AssetManagement->getGateName()),

            self::AdvisoryManagement => Gate::check(Feature::AdvisoryManagement->getGateName()),

            self::ContractManagement => Gate::check(Feature::ContractManagement->getGateName()),

            self::LicenseManagement => Gate::check(Feature::LicenseManagement->getGateName()),

            self::ChangeManagement => Gate::check(Feature::ChangeManagement->getGateName()),

            self::ServiceMonitoring => Gate::check(Feature::ServiceMonitoring->getGateName()),

            self::Projects => Gate::check(Feature::ProjectManagement->getGateName()),

            self::AiSupportAssistant,
            self::AiClarification,
            self::AiResolution => true,
        };
    }

    public static function fromPageClass(string $pageClass): ?self
    {
        foreach (self::cases() as $case) {
            if ($case->getPageClass() === $pageClass) {
                return $case;
            }
        }

        return null;
    }

    public function userCanAccess(User $user): bool
    {
        if ($user->isSuperAdmin() || $user->isPartnerAdmin()) {
            return true;
        }

        return ReportUserAccess::query()
            ->where('report_key', $this->value)
            ->where('user_id', $user->getKey())
            ->selectRaw('1')
            ->union(
                ReportDepartmentAccess::query()
                    ->where('report_key', $this->value)
                    ->where('department_id', $user->department_id)
                    ->selectRaw('1')
            )
            ->exists();
    }

    /**
     * The number of distinct users that have access to the report, counting both
     * direct user assignments and members of assigned departments (deduplicated).
     */
    public function accessCount(): int
    {
        return User::query()
            ->where(function (Builder $query) {
                $query->whereIn(
                    'id',
                    ReportUserAccess::query()
                        ->where('report_key', $this->value)
                        ->select('user_id')
                )
                    ->orWhereIn(
                        'department_id',
                        ReportDepartmentAccess::query()
                            ->where('report_key', $this->value)
                            ->select('department_id')
                    );
            })
            ->count();
    }
}
