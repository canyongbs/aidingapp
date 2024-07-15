<?php

namespace AidingApp\Report\Filament\Widgets;

use Livewire\Attributes\On;
use Livewire\Attributes\Locked;
use Filament\Widgets\StatsOverviewWidget;

abstract class StatsOverviewReportWidget extends StatsOverviewWidget
{
    #[Locked]
    public string $cacheTag;

    protected static ?string $pollingInterval = null;

    protected static bool $isLazy = false;

    public function mount(string $cacheTag)
    {
        $this->cacheTag = $cacheTag;
    }

    #[On('refresh-widgets')]
    public function refreshWidget()
    {
        $this->dispatch('$refresh');
    }
}
