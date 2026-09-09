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
use AidingApp\Report\Filament\Pages\Concerns\InteractsWithServiceRequestTypeFilter;
use AidingApp\Report\Filament\Widgets\RefreshWidget;
use AidingApp\Report\Filament\Widgets\ResolutionSlaByClassificationDonutChart;
use AidingApp\Report\Filament\Widgets\ResponseSlaByClassificationDonutChart;
use AidingApp\Report\Filament\Widgets\SlaBreachesByServiceRequestTypeTable;
use AidingApp\Report\Filament\Widgets\SlaComplianceOverTimeLineChart;
use AidingApp\Report\Filament\Widgets\SlaPerformanceByAgentTable;
use AidingApp\Report\Filament\Widgets\SlaStats;
use AidingApp\ServiceManagement\Enums\ServiceRequestCategory;
use AidingApp\ServiceManagement\Models\ServiceRequestAssignment;
use App\Enums\Feature;
use App\Enums\ReportLibraryNavigationGroup;
use App\Filament\Clusters\ReportLibrary;
use App\Models\User;
use BackedEnum;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Pages\Dashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Gate;
use UnitEnum;

class Sla extends Dashboard
{
    use HasFiltersForm;
    use InteractsWithServiceRequestTypeFilter;

    protected static ?string $cluster = ReportLibrary::class;

    protected static string | UnitEnum | null $navigationGroup = ReportLibraryNavigationGroup::ServiceDesk;

    protected static ?string $navigationLabel = 'SLA';

    protected static ?string $title = 'SLA';

    protected static string $routePath = 'sla';

    protected static ?int $navigationSort = 15;

    protected static string | BackedEnum | null $navigationIcon = '';

    protected string $cacheTag = 'report-sla';

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
            Section::make('Filters')
                ->schema([
                    ...$this->serviceRequestTypeFilterComponents(),
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
                    Select::make('classification')
                        ->label('Classification')
                        ->options(ServiceRequestCategory::class)
                        ->native(false)
                        ->placeholder('All'),
                    Select::make('assignedAgents')
                        ->label('Assigned Agent')
                        ->multiple()
                        ->searchable()
                        ->options(fn (): array => $this->getAssignedAgentOptions())
                        ->placeholder('All'),
                ])
                ->columns(2),
        ]);
    }

    /**
     * @return array<string, string>
     */
    public function getAssignedAgentOptions(): array
    {
        return User::query()
            ->whereIn('id', ServiceRequestAssignment::query()->select('user_id'))
            ->orderBy('name')
            ->pluck('name', 'id')
            ->all();
    }

    public function getWidgets(): array
    {
        return [
            RefreshWidget::make(['cacheTag' => $this->cacheTag]),
            SlaStats::make(['cacheTag' => $this->cacheTag]),
            SlaComplianceOverTimeLineChart::make(['cacheTag' => $this->cacheTag]),
            ResponseSlaByClassificationDonutChart::make(['cacheTag' => $this->cacheTag]),
            ResolutionSlaByClassificationDonutChart::make(['cacheTag' => $this->cacheTag]),
            SlaPerformanceByAgentTable::make(['cacheTag' => $this->cacheTag]),
            SlaBreachesByServiceRequestTypeTable::make(['cacheTag' => $this->cacheTag]),
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
