<?php

namespace AidingApp\Report\Filament\Widgets;

use Illuminate\Support\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;
use Filament\Notifications\Notification;

class RefreshWidget extends StatsOverviewReportWidget
{
    public Carbon $lastRefreshTime;

    protected static string $view = 'report::filament.pages.report-refresh-widgets';

    protected int | string | array $columnSpan = [
        'sm' => 4,
        'md' => 4,
        'lg' => 4,
    ];

    public function render(): View
    {
        $user = auth()->user();
        $timezone = $user->timezone;

        $this->lastRefreshTime = Carbon::parse(
            Cache::tags([$this->cacheTag])->remember(
                'updated-time',
                now()->addHours(24),
                fn () => now()
            )
        )->setTimezone($timezone);

        return parent::render();
    }

    public function removeWidgetCache($cacheTag)
    {
        Cache::tags([$cacheTag])->flush();

        $this->dispatch('refresh-widgets');

        Notification::make()
            ->title('Report successfully refreshed!')
            ->success()
            ->send();
    }
}
