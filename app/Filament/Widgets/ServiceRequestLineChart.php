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

namespace App\Filament\Widgets;

use AidingApp\ServiceManagement\Models\ServiceRequest;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class ServiceRequestLineChart extends ChartWidget
{
    protected static ?string $heading = 'Service Requests (Last 30 Days)';

    protected static ?string $maxHeight = '250px';

    protected int | string | array $columnSpan = [
        'sm' => 2,
    ];

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'stepSize' => 1,
                        'beginAtZero' => true,
                    ],
                ],
            ],
        ];
    }

    protected function getData(): array
    {
        $totalCreatedPerDay = ServiceRequest::query()
            ->toBase()
            ->selectRaw('DATE(created_at) as date')
            ->selectRaw('COUNT(*) as total')
            ->where('created_at', '>', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total', 'date');

        $serviceRequestTotalPerDay = [];

        foreach (range(29, 0) as $daysAgo) {
            $date = Carbon::now()->subDays($daysAgo)->toDateString();

            $serviceRequestTotalPerDay[$date] = $totalCreatedPerDay[$date] ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Service requests',
                    'data' => array_values($serviceRequestTotalPerDay),
                ],
            ],
            'labels' => array_keys($serviceRequestTotalPerDay),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
