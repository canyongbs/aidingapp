<?php

namespace AidingApp\Report\Filament\Pages;

use AidingApp\Authorization\Enums\LicenseType;
use AidingApp\Report\Filament\Widgets\RefreshWidget;
use AidingApp\Report\Filament\Widgets\ServiceRequestFeedbackStats;
use AidingApp\Report\Filament\Widgets\ServiceRequestFeedbackTable;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use App\Filament\Clusters\ReportLibrary;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Pages\Dashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;

class ServiceRequestFeedback extends Dashboard
{
    use HasFiltersForm;

    protected static ?string $cluster = ReportLibrary::class;

    protected static ?string $navigationGroup = 'Service Management';

    protected static ?string $navigationLabel = 'SR Feedback';

    protected static ?string $title = 'Service Request Feedback';

    protected static string $routePath = 'sr-feedback';

    protected static ?int $navigationSort = 20;

    protected string $cacheTag = 'report-service-request-feedback';

    protected static string $view = 'report::filament.pages.report';

    public static function canAccess(): bool
    {
        /** @var User $user */
        $user = auth()->user();

        return $user->hasLicense(LicenseType::RecruitmentCrm) && $user->can('report-library.view-any');
    }

    /** @return list<string>|null */
    public function getServiceRequestTypes(): ?array
    {
        $types = $this->filters['serviceRequestTypes'] ?? null;

        return filled($types) ? (array) $types : null;
    }

    public function filtersForm(Form $form): Form
    {
        return $form->schema([
            Section::make()
                ->schema([
                    DatePicker::make('startDate')
                        ->displayFormat('m-d-Y')
                        ->native(false)
                        ->maxDate(fn (Get $get) => $get('endDate') ?: now())
                        ->afterStateUpdated(function (callable $set, mixed $state, Get $get) {
                            if (blank($get('endDate')) && filled($state)) {
                                $set('endDate', $state);
                            }
                        }),
                    DatePicker::make('endDate')
                        ->displayFormat('m-d-Y')
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

            Section::make('Advance Filters')
                ->schema([
                    Select::make('serviceRequestTypes')
                        ->label('Service Request Type Filter')
                        ->options(ServiceRequestType::query()
                            ->orderBy('name')
                            ->pluck('name', 'id'))
                        ->multiple()
                        ->searchable()
                        ->placeholder('All'),
                ])
                ->columns(1),
        ]);
    }

    public function getWidgets(): array
    {
        return [
            RefreshWidget::make(['cacheTag' => $this->cacheTag]),
            ServiceRequestFeedbackStats::make(['cacheTag' => $this->cacheTag]),
            ServiceRequestFeedbackTable::make(['cacheTag' => $this->cacheTag]),
        ];
    }

    public function getColumns(): int|string|array
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
            'filters' => $this->filters,
        ];
    }
}
