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

namespace AidingApp\Portal\Http\Controllers\KnowledgeManagementPortal;

use AidingApp\Portal\DataTransferObjects\AdvisoryData;
use AidingApp\Portal\DataTransferObjects\AdvisorySeverityData;
use AidingApp\Portal\DataTransferObjects\AdvisoryStatusData;
use AidingApp\Portal\Enums\AdvisorySort;
use AidingApp\ServiceManagement\Enums\SystemAdvisoryStatusClassification;
use AidingApp\ServiceManagement\Models\Advisory;
use AidingApp\ServiceManagement\Models\AdvisorySeverity;
use AidingApp\ServiceManagement\Models\AdvisoryStatus;
use AidingApp\ServiceManagement\Models\Scopes\WithLastUpdatedAt;
use App\Features\AdvisorySeveritySortFeature;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AdvisoryController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'classification' => ['nullable', Rule::enum(SystemAdvisoryStatusClassification::class)],
            'search' => ['nullable', 'string', 'max:255'],
            'severity' => ['nullable', 'uuid'],
            'status' => ['nullable', 'uuid'],
            'sort' => ['nullable', Rule::enum(AdvisorySort::class)],
        ]);

        $classification = SystemAdvisoryStatusClassification::tryFrom($validated['classification'] ?? '') ?? SystemAdvisoryStatusClassification::Open;
        $search = $validated['search'] ?? null;
        $severityId = $validated['severity'] ?? null;
        $statusId = $validated['status'] ?? null;
        $sort = AdvisorySort::tryFrom($validated['sort'] ?? '') ?? AdvisorySort::HighestSeverity;

        $advisories = Advisory::query()
            ->with(['severity', 'status'])
            ->tap(new WithLastUpdatedAt())
            ->whereHas('status', fn (Builder $query): Builder => $query->where('classification', $classification))
            ->when(filled($severityId), fn (Builder $query): Builder => $query->where('advisories.severity_id', $severityId))
            ->when(filled($statusId), fn (Builder $query): Builder => $query->where('advisories.status_id', $statusId))
            ->when(filled($search), function (Builder $query) use ($search): void {
                $pattern = '%' . addcslashes(Str::lower($search), '%_\\') . '%';

                $query->where(fn (Builder $query): Builder => $query
                    ->whereRaw('lower(advisories.title) like ?', [$pattern])
                    ->orWhereRaw('lower(advisories.description) like ?', [$pattern])
                    ->orWhereHas('assignedDepartment', fn (Builder $query): Builder => $query
                        ->whereRaw('lower(departments.name) like ?', [$pattern]))
                    ->orWhereHas('advisoryUpdates', fn (Builder $query): Builder => $query
                        ->where('advisory_updates.internal', false)
                        ->where(fn (Builder $query): Builder => $query
                            ->whereRaw('lower(advisory_updates.title) like ?', [$pattern])
                            ->orWhereRaw('lower(advisory_updates.update) like ?', [$pattern]))));
            })
            ->tap(fn (Builder $query) => $sort->apply($query))
            ->paginate(10);

        $advisories->through(fn (Advisory $advisory): AdvisoryData => AdvisoryData::fromModel($advisory));

        return response()->json([
            'data' => $advisories->items(),
            'meta' => [
                'current_page' => $advisories->currentPage(),
                'last_page' => $advisories->lastPage(),
                'from' => $advisories->firstItem() ?? 0,
                'to' => $advisories->lastItem() ?? 0,
                'total' => $advisories->total(),
                'per_page' => $advisories->perPage(),
            ],
            'counts' => $this->countByClassification(),
            'severities' => AdvisorySeverity::query()
                ->when(AdvisorySeveritySortFeature::active(), fn (Builder $query): Builder => $query->orderBy('sort'))
                ->orderBy('name')
                ->get()
                ->map(fn (AdvisorySeverity $severity): AdvisorySeverityData => AdvisorySeverityData::fromModel($severity))
                ->all(),
            'statuses' => AdvisoryStatus::query()
                ->orderBy('name')
                ->get()
                ->map(fn (AdvisoryStatus $status): AdvisoryStatusData => AdvisoryStatusData::fromModel($status))
                ->all(),
        ]);
    }

    /**
     * Counts every advisory in each status classification, regardless of any search or filter.
     *
     * @return array<string, int>
     */
    protected function countByClassification(): array
    {
        return collect(SystemAdvisoryStatusClassification::cases())
            ->mapWithKeys(fn (SystemAdvisoryStatusClassification $classification): array => [
                $classification->value => Advisory::query()
                    ->whereHas('status', fn (Builder $query): Builder => $query->where('classification', $classification))
                    ->count(),
            ])
            ->all();
    }
}
