<?php

namespace AidingApp\Report\Filament\Widgets;

use Livewire\Attributes\On;
use Livewire\Attributes\Locked;
use Filament\Widgets\ChartWidget;

abstract class LineChartReportWidget extends ChartWidget
{
    #[Locked]
    public string $cacheTag;

    protected static ?string $pollingInterval = null;

    protected static ?string $maxHeight = '200px';

    protected static bool $isLazy = false;

    public function mount($cacheTag = null): void
    {
        parent::mount();

        $this->cacheTag = $cacheTag;
    }

    #[On('refresh-widgets')]
    public function refreshWidget()
    {
        $this->dispatch('$refresh');
    }

    protected function getType(): string
    {
        return 'line';
    }
}
