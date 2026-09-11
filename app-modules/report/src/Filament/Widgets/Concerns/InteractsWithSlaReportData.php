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

namespace AidingApp\Report\Filament\Widgets\Concerns;

use AidingApp\ServiceManagement\Enums\SlaComplianceStatus;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * Shared query building and in-memory SLA compliance aggregation for the SLA report widgets.
 *
 * SLA compliance is derived from Eloquent model methods (response/resolution seconds versus the
 * configured SLA), so it cannot be expressed purely in SQL. These helpers eager load the required
 * relationships and reduce a filtered collection of service requests to the metrics the widgets need.
 */
trait InteractsWithSlaReportData
{
    use InteractsWithPageFilters;

    /**
     * The relationships required to compute SLA compliance in memory.
     *
     * @return array<int, string>
     */
    protected function slaEagerLoads(): array
    {
        return [
            'status',
            'priority.sla',
            'priority.type',
            'assignedTo.user',
            'latestInboundServiceRequestUpdate.createdBy',
            'latestOutboundServiceRequestUpdate.createdBy',
            'statusPeriods',
        ];
    }

    /**
     * Base query of the service requests that match the currently selected report filters.
     *
     * @return Builder<ServiceRequest>
     */
    protected function slaServiceRequestsQuery(): Builder
    {
        return $this->applySlaFilters(ServiceRequest::query());
    }

    /**
     * Apply the report filters (types, date range, classification and assigned agent) to a query.
     *
     * @param Builder<ServiceRequest> $query
     *
     * @return Builder<ServiceRequest>
     */
    protected function applySlaFilters(Builder $query, bool $applyDateRange = true): Builder
    {
        $startDate = $this->getStartDate();
        $endDate = $this->getEndDate();
        $types = $this->getServiceRequestTypes();
        $classification = $this->getClassification();
        $assignedAgents = $this->getAssignedAgents();

        return $query
            ->when(
                $applyDateRange && $startDate && $endDate,
                fn (Builder $query): Builder => $query->whereBetween('created_at', [$startDate, $endDate])
            )
            ->when(
                $types,
                fn (Builder $query): Builder => $query->whereHas('priority.type', fn (Builder $query): Builder => $query->whereIn('id', $types))
            )
            ->when(
                $classification,
                fn (Builder $query): Builder => $query->where('category', $classification->value)
            )
            ->when(
                $assignedAgents,
                fn (Builder $query): Builder => $query->whereHas('assignedTo', fn (Builder $query): Builder => $query->whereIn('user_id', $assignedAgents))
            );
    }

    /**
     * Summarize SLA compliance for a filtered collection of service requests.
     *
     * @param Collection<int, ServiceRequest> $serviceRequests
     *
     * @return array{
     *     total: int,
     *     sla_total: int,
     *     response_sla_total: int,
     *     resolution_sla_total: int,
     *     response_breaches: int,
     *     resolution_breaches: int,
     *     breaches: int,
     *     response_breach_percentage: float,
     *     resolution_breach_percentage: float,
     *     response_compliance_percentage: float,
     *     resolution_compliance_percentage: float,
     *     average_response_seconds: int|null,
     *     average_resolution_seconds: int|null
     * }
     */
    protected function summarizeSlaMetrics(Collection $serviceRequests): array
    {
        $total = $serviceRequests->count();
        $slaTotal = 0;
        $responseSlaTotal = 0;
        $resolutionSlaTotal = 0;
        $responseBreaches = 0;
        $resolutionBreaches = 0;
        $breaches = 0;
        $responseSecondsSum = 0;
        $resolutionSecondsSum = 0;
        $resolutionSecondsCount = 0;

        foreach ($serviceRequests as $serviceRequest) {
            $responseStatus = $serviceRequest->getResponseSlaComplianceStatus();
            $resolutionStatus = $serviceRequest->getResolutionSlaComplianceStatus();

            $hasResponseSla = $responseStatus !== null;
            $hasResolutionSla = $resolutionStatus !== null;

            if ($hasResponseSla) {
                $responseSlaTotal++;
                $responseSecondsSum += $serviceRequest->getLatestResponseSeconds();

                if ($responseStatus === SlaComplianceStatus::NonCompliant) {
                    $responseBreaches++;
                }
            }

            if ($hasResolutionSla) {
                $resolutionSlaTotal++;

                if ($serviceRequest->isResolved()) {
                    $resolutionSecondsSum += $serviceRequest->getResolutionSeconds();
                    $resolutionSecondsCount++;
                }

                if ($resolutionStatus === SlaComplianceStatus::NonCompliant) {
                    $resolutionBreaches++;
                }
            }

            if ($hasResponseSla || $hasResolutionSla) {
                $slaTotal++;
            }

            if ($responseStatus === SlaComplianceStatus::NonCompliant || $resolutionStatus === SlaComplianceStatus::NonCompliant) {
                $breaches++;
            }
        }

        return [
            'total' => $total,
            'sla_total' => $slaTotal,
            'response_sla_total' => $responseSlaTotal,
            'resolution_sla_total' => $resolutionSlaTotal,
            'response_breaches' => $responseBreaches,
            'resolution_breaches' => $resolutionBreaches,
            'breaches' => $breaches,
            'response_breach_percentage' => $this->slaPercentage($responseBreaches, $responseSlaTotal),
            'resolution_breach_percentage' => $this->slaPercentage($resolutionBreaches, $resolutionSlaTotal),
            'response_compliance_percentage' => $this->slaPercentage($responseSlaTotal - $responseBreaches, $responseSlaTotal),
            'resolution_compliance_percentage' => $this->slaPercentage($resolutionSlaTotal - $resolutionBreaches, $resolutionSlaTotal),
            'average_response_seconds' => $responseSlaTotal > 0 ? (int) round($responseSecondsSum / $responseSlaTotal) : null,
            'average_resolution_seconds' => $resolutionSecondsCount > 0 ? (int) round($resolutionSecondsSum / $resolutionSecondsCount) : null,
        ];
    }

    protected function slaPercentage(int $value, int $total): float
    {
        return $total > 0 ? round(($value / $total) * 100, 1) : 0.0;
    }

    protected function formatSlaDuration(?int $seconds): string
    {
        if ($seconds === null) {
            return '—';
        }

        $days = intdiv($seconds, 86400);
        $hours = intdiv($seconds % 86400, 3600);
        $minutes = intdiv($seconds % 3600, 60);

        if ($days > 0) {
            return "{$days}d {$hours}h";
        }

        if ($hours > 0) {
            return "{$hours}h {$minutes}m";
        }

        return "{$minutes}m";
    }
}
