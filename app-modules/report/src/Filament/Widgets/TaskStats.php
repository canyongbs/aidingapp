<?php

namespace AidingApp\Report\Filament\Widgets;

use AidingApp\Contact\Models\Contact;
use AidingApp\Task\Enums\TaskStatus;
use AidingApp\Task\Models\Task;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Number;

class TaskStats extends StatsOverviewReportWidget
{
    protected int | string | array $columnSpan = [
        'sm' => 2,
        'md' => 4,
        'lg' => 4,
    ];

    protected function getStats(): array
    {
        return [
            Stat::make('Total Tasks', Number::abbreviate(
                Cache::tags([$this->cacheTag])->remember('tasks-count', now()->addHours(24), function (): int {
                    return Task::count();
                }),
                maxPrecision: 2,
            )),
            Stat::make('Staff with Open Tasks', Number::abbreviate(
                Cache::tags([$this->cacheTag])->remember('users-with-open-tasks-count', now()->addHours(24), function (): int {
                    return User::query()->whereHas('assignedTasks', function (Builder $query) {
                        $query->whereIn('status', [TaskStatus::Pending, TaskStatus::InProgress]);
                    })->count();
                }),
                maxPrecision: 2,
            )),
            Stat::make('Contacst with Open Tasks', Number::abbreviate(
                Cache::tags([$this->cacheTag])->remember('students-with-open-tasks-count', now()->addHours(24), function (): int {
                    return Contact::query()->whereHas('tasks', function (Builder $query) {
                        $query->whereIn('status', [TaskStatus::Pending, TaskStatus::InProgress]);
                    })->count();
                }),
                maxPrecision: 2,
            )),
        ];
    }
}

