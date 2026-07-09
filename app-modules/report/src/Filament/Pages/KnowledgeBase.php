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

use AidingApp\KnowledgeBase\Models\KnowledgeBaseCategory;
use AidingApp\Report\Filament\Widgets\KnowledgeBaseArticlesByCategoryDonutChart;
use AidingApp\Report\Filament\Widgets\KnowledgeBaseArticlesOverTimeBarChart;
use AidingApp\Report\Filament\Widgets\KnowledgeBaseArticlesTable;
use AidingApp\Report\Filament\Widgets\KnowledgeBaseConcernsByStatusDonutChart;
use AidingApp\Report\Filament\Widgets\KnowledgeBaseStats;
use AidingApp\Report\Filament\Widgets\RefreshWidget;
use App\Enums\Feature;
use App\Enums\ReportLibraryNavigationGroup;
use App\Filament\Clusters\ReportLibrary;
use App\Models\User;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Pages\Dashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Gate;
use UnitEnum;

class KnowledgeBase extends Dashboard
{
    use HasFiltersForm;

    protected static ?string $cluster = ReportLibrary::class;

    protected static string | UnitEnum | null $navigationGroup = ReportLibraryNavigationGroup::ServiceDesk;

    protected static ?string $navigationLabel = 'Knowledge Base';

    protected static ?string $title = 'Knowledge Base';

    protected static string $routePath = 'knowledge-base';

    protected static ?int $navigationSort = 30;

    protected static string | BackedEnum | null $navigationIcon = '';

    protected string $cacheTag = 'report-knowledge-base';

    protected string $view = 'report::filament.pages.report';

    public static function canAccess(): bool
    {
        if (! Gate::check(Feature::KnowledgeManagement->getGateName())) {
            return false;
        }

        /** @var User $user */
        $user = auth()->user();

        return $user->can('report-library.view-any');
    }

    public function filtersForm(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Filters')
                ->schema([
                    Select::make('categories')
                        ->label('Category')
                        ->options(
                            KnowledgeBaseCategory::query()
                                ->orderBy('name')
                                ->pluck('name', 'id')
                        )
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
            KnowledgeBaseStats::make(['cacheTag' => $this->cacheTag]),
            KnowledgeBaseConcernsByStatusDonutChart::make(['cacheTag' => $this->cacheTag]),
            KnowledgeBaseArticlesByCategoryDonutChart::make(['cacheTag' => $this->cacheTag]),
            KnowledgeBaseArticlesOverTimeBarChart::make(['cacheTag' => $this->cacheTag]),
            KnowledgeBaseArticlesTable::make(['cacheTag' => $this->cacheTag]),
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
