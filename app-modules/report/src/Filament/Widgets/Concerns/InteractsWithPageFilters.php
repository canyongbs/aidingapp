<?php

namespace AidingApp\Report\Filament\Widgets\Concerns;

use Carbon\Carbon;
use Filament\Widgets\Concerns\InteractsWithPageFilters as InteractsWithPageFiltersBase;

trait InteractsWithPageFilters
{
    use InteractsWithPageFiltersBase;

    public function getStartDate(): ?Carbon
    {
        $startDate = $this->filters['startDate'] ?? null;

        return filled($startDate) ? Carbon::parse($startDate)->startOfDay() : null;
    }

    public function getEndDate(): ?Carbon
    {
        $endDate = $this->filters['endDate'] ?? null;

        return filled($endDate) ? Carbon::parse($endDate)->endOfDay() : null;
    }

    /** @return list<string>|null */
    public function getServiceRequestTypes(): ?array
    {
        $types = $this->filters['serviceRequestTypes'] ?? null;

        return filled($types) ? (array) $types : null;
    }
}
