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

namespace AidingApp\Report\Filament\Pages;

use AidingApp\Report\Enums\ReportAccessKey;
use AidingApp\Report\Filament\Widgets\RefreshWidget;
use AidingApp\Report\Filament\Widgets\ServiceRequestCategoryDistributionDonutChart;
use AidingApp\Report\Filament\Widgets\ServiceRequestsOverTimeBarChart;
use AidingApp\Report\Filament\Widgets\ServiceRequestsStats;
use AidingApp\Report\Filament\Widgets\ServiceRequestsTable;
use AidingApp\Report\Filament\Widgets\ServiceRequestStatusDistributionDonutChart;
use AidingApp\Report\Filament\Widgets\ServiceRequestTypesTable;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use AidingApp\ServiceManagement\Models\ServiceRequestTypeCategory;
use App\Enums\Feature;
use App\Enums\ReportLibraryNavigationGroup;
use App\Filament\Clusters\ReportLibrary;
use App\Models\User;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Pages\Dashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Gate;
use UnitEnum;

class ServiceRequests extends Dashboard
{
    use HasFiltersForm;

    protected static ?string $cluster = ReportLibrary::class;

    protected static string | UnitEnum | null $navigationGroup = ReportLibraryNavigationGroup::ServiceDesk;

    protected static ?string $navigationLabel = 'Service Requests';

    protected static ?string $title = 'Service Requests';

    protected static string $routePath = 'service-requests';

    protected static ?int $navigationSort = 10;

    protected static string | BackedEnum | null $navigationIcon = '';

    protected string $cacheTag = 'report-service-requests';

    protected string $view = 'report::filament.pages.report';

    public static function canAccess(): bool
    {
        if (! Gate::check(Feature::ServiceManagement->getGateName())) {
            return false;
        }

        /** @var User $user */
        $user = auth()->user();

        return ReportAccessKey::fromPageClass(static::class)?->userCanAccess($user) ?? false;
    }

    public function filtersForm(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()
                ->schema([
                    Select::make('serviceRequestTypes')
                        ->label('Service Request Types')
                        ->options(fn (): array => $this->getServiceRequestTypeOptions())
                        ->multiple()
                        ->searchable()
                        ->live()
                        ->placeholder('All'),
                    Actions::make([
                        Action::make('loadAffiliatedServiceRequestTypes')
                            ->label('My Affiliated Types')
                            ->icon(Heroicon::UserGroup)
                            ->action(function (Set $set): void {
                                $set('serviceRequestTypes', $this->getAffiliatedServiceRequestTypeIds());
                            }),
                        Action::make('clearServiceRequestTypes')
                            ->label('Clear')
                            ->color('gray')
                            ->icon(Heroicon::XMark)
                            ->disabled(fn (Get $get): bool => blank($get('serviceRequestTypes')))
                            ->action(fn (Set $set) => $set('serviceRequestTypes', [])),
                    ])
                        ->key('serviceRequestTypeActions'),
                ])
                ->columns(1),
            Section::make()
                ->schema([
                    DatePicker::make('startDate')
                        ->native(false)
                        ->maxDate(fn (Get $get) => $get('endDate') ?: now())
                        ->afterStateUpdated(function (callable $set, mixed $state, Get $get) {
                            if (blank($get('endDate')) && filled($state)) {
                                $set('endDate', $state);
                            }
                        }),
                    DatePicker::make('endDate')
                        ->native(false)
                        ->minDate(fn (Get $get) => $get('startDate') ?: now())
                        ->maxDate(now())
                        ->afterStateUpdated(function (callable $set, mixed $state, Get $get) {
                            if (blank($get('startDate')) && filled($state)) {
                                $set('startDate', $state);
                            }
                        }),
                ])
                ->columns(2),
        ]);
    }

    /**
     * Build the Service Request Type options grouped by the service catalog hierarchy.
     *
     * @return array<string, array<string, string>>
     */
    public function getServiceRequestTypeOptions(): array
    {
        $categories = ServiceRequestTypeCategory::query()
            ->get(['id', 'name', 'parent_id', 'sort'])
            ->keyBy('id');

        $buildPath = function (ServiceRequestTypeCategory $category) use ($categories): string {
            $segments = [];
            $current = $category;

            while ($current instanceof ServiceRequestTypeCategory) {
                array_unshift($segments, $current->name);

                $current = filled($current->parent_id) ? $categories->get($current->parent_id) : null;
            }

            return implode(' › ', $segments);
        };

        $grouped = [];
        $uncategorized = [];

        ServiceRequestType::query()
            ->withoutArchived()
            ->with('categories:id')
            ->orderBy('name')
            ->get(['id', 'name'])
            ->each(function (ServiceRequestType $type) use (&$grouped, &$uncategorized, $categories, $buildPath): void {
                if ($type->categories->isEmpty()) {
                    $uncategorized[$type->getKey()] = $type->name;

                    return;
                }

                foreach ($type->categories as $category) {
                    $resolvedCategory = $categories->get($category->getKey());

                    if (! $resolvedCategory instanceof ServiceRequestTypeCategory) {
                        continue;
                    }

                    $grouped[$buildPath($resolvedCategory)][$type->getKey()] = $type->name;
                }
            });

        ksort($grouped);

        if (filled($uncategorized)) {
            $grouped['Uncategorized'] = $uncategorized;
        }

        return $grouped;
    }

    /**
     * The ids of every Service Request Type the current user manages or audits.
     *
     * @return array<int, string>
     */
    public function getAffiliatedServiceRequestTypeIds(): array
    {
        /** @var User $user */
        $user = auth()->user();

        $departmentId = $user->department?->getKey();

        return ServiceRequestType::query()
            ->where(function (Builder $query) use ($user): void {
                $query->whereHas('managerUsers', fn (Builder $query) => $query->whereKey($user->getKey()))
                    ->orWhereHas('auditorUsers', fn (Builder $query) => $query->whereKey($user->getKey()));
            })
            ->when($departmentId, function (Builder $query) use ($departmentId): void {
                $query->orWhere(function (Builder $query) use ($departmentId): void {
                    $query->whereHas('managerDepartments', fn (Builder $query) => $query->whereKey($departmentId))
                        ->orWhereHas('auditorDepartments', fn (Builder $query) => $query->whereKey($departmentId));
                });
            })
            ->orderBy('name')
            ->pluck('id')
            ->all();
    }

    public function getWidgets(): array
    {
        return [
            RefreshWidget::make(['cacheTag' => $this->cacheTag]),
            ServiceRequestsStats::make(['cacheTag' => $this->cacheTag]),
            ServiceRequestStatusDistributionDonutChart::make(['cacheTag' => $this->cacheTag]),
            ServiceRequestCategoryDistributionDonutChart::make(['cacheTag' => $this->cacheTag]),
            ServiceRequestsOverTimeBarChart::make(['cacheTag' => $this->cacheTag]),
            ServiceRequestTypesTable::make(['cacheTag' => $this->cacheTag]),
            ServiceRequestsTable::make(['cacheTag' => $this->cacheTag]),
        ];
    }

    public function getColumns(): int|array
    {
        return [
            'sm' => 2,
            'md' => 4,
            'lg' => 4,
        ];
    }

    public function getWidgetData(): array
    {
        return [
            'pageFilters' => $this->filters,
        ];
    }
}
