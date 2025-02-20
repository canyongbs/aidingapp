<?php

namespace AidingApp\Report\Filament\Widgets;

use AidingApp\Contact\Models\Contact;
use AidingApp\Task\Models\Task;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class TaskCumulativeCountLineChart extends LineChartReportWidget
{
    protected static ?string $heading = 'Tasks by Affiliation';

    protected int | string | array $columnSpan = 'full';

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
            'scales' => [
                'y' => [
                    'min' => 0,
                ],
            ],
        ];
    }

    protected function getData(): array
    {
        $runningTotalPerMonth = Cache::tags([$this->cacheTag])->remember('task_cumulative_count_line_chart', now()->addHours(24), function (): array {
            $totalContactTasksPerMonth = Task::query()
                ->whereHasMorph('concern', Contact::class)
                ->toBase()
                ->selectRaw('date_trunc(\'month\', created_at) as month')
                ->selectRaw('count(*) as total')
                ->where('created_at', '>', now()->subYear())
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('total', 'month');

            $totalUnrelatedTasksPerMonth = Task::query()
                ->whereNull('concern_id')
                ->selectRaw('date_trunc(\'month\', created_at) as month')
                ->selectRaw('count(*) as total')
                ->where('created_at', '>', now()->subYear())
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('total', 'month');

            $data = [];

            foreach (range(11, 0) as $month) {
                $month = Carbon::now()->subMonths($month);
                $data['contactTasks'][$month->format('M Y')] = $totalContactTasksPerMonth[$month->startOfMonth()->toDateTimeString()] ?? 0;
                $data['unrelatedTasks'][$month->format('M Y')] = $totalUnrelatedTasksPerMonth[$month->startOfMonth()->toDateTimeString()] ?? 0;
            }

            return $data;
        });

        return [
            'datasets' => [
                [
                    'label' => 'Contact Tasks',
                    'data' => array_values($runningTotalPerMonth['contactTask']),
                    'borderColor' => '#2C8BCA',
                    'pointBackgroundColor' => '#2C8BCA',
                ],
                [
                    'label' => 'Unrelated Tasks',
                    'data' => array_values($runningTotalPerMonth['unrelatedTasks']),
                    'borderColor' => '#FFA500',
                    'pointBackgroundColor' => '#FFA500',
                ],
            ],
            'labels' => array_keys($runningTotalPerMonth['contactTask']),
        ];
    }
}
