@php
    use AidingApp\Project\Filament\Resources\Projects\ProjectResource;

    $totalTasks = $project->tasks()->count();
    $completedTasks = $project
        ->tasks()
        ->where('status', 'completed')
        ->count();
    $progress = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
    $gradient = $project->getGradient();
@endphp

<header class="flex flex-col gap-6">
    <x-filament::breadcrumbs class="hidden sm:block" :breadcrumbs="$breadcrumbs" />

    <h1 class="text-2xl font-bold tracking-tight text-gray-950 dark:text-white">Project Dashboard</h1>

    <div class="flex overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900">
        <div
            class="ml-4 my-4 flex w-24 shrink-0 items-center justify-center rounded-lg"
            style="background: linear-gradient(to bottom, {{ $gradient['from'] }}, {{ $gradient['to'] }})"
        >
            @svg($project->icon ?? 'heroicon-o-clipboard-document-list', 'h-15 w-15 text-white')
        </div>

        <div class="flex-1 p-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div class="flex-1">
                    <h2 class="text-xl font-bold text-gray-950 dark:text-white">
                        {{ $project->name }}
                    </h2>

                    @if ($project->description)
                        <p class="mt-1 max-w-xl text-sm text-gray-600 dark:text-gray-400">
                            {{ $project->description }}
                        </p>
                    @endif
                </div>

                <div class="flex shrink-0 items-center gap-2">
                    <x-filament::actions :actions="$actions" />
                </div>
            </div>

            <div class="mt-5 border-t border-gray-200 dark:border-gray-700"></div>

            <div
                class="mt-4 grid grid-cols-4 divide-x divide-gray-200 text-sm text-gray-500 dark:divide-gray-700 dark:text-gray-400"
            >
                <div class="flex items-center gap-1.5 pr-5">
                    @svg('heroicon-m-building-office', 'h-4 w-4')
                    <span>Department: {{ $project->department?->name ?? 'N/A' }}</span>
                </div>

                <div class="flex items-center gap-1.5 border-l border-gray-200 px-5 dark:border-gray-700">
                    @svg('heroicon-m-calendar', 'h-4 w-4')
                    <span>Start Date: {{ $project->start_date?->format('M j, Y') ?? 'N/A' }}</span>
                </div>

                <div class="flex items-center gap-1.5 border-l border-gray-200 px-5 dark:border-gray-700">
                    @svg('heroicon-m-flag', 'h-4 w-4')
                    <span>Target Go-Live: {{ $project->target_completion_date?->format('M j, Y') ?? 'N/A' }}</span>
                </div>

                <div class="flex items-center gap-1.5 border-l border-gray-200 px-5 dark:border-gray-700">
                    <svg class="h-4 w-4" viewBox="0 0 20 20">
                        <circle
                            cx="10"
                            cy="10"
                            r="8"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            class="text-indigo-200 dark:text-gray-700"
                        />
                        <circle
                            cx="10"
                            cy="10"
                            r="8"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-dasharray="{{ 2 * 3.14159 * 8 }}"
                            stroke-dashoffset="{{ 2 * 3.14159 * 8 * (1 - $progress / 100) }}"
                            transform="rotate(-90 10 10)"
                            class="text-primary-600"
                        />
                    </svg>
                    <span>Progress: {{ $progress }}%</span>
                </div>
            </div>
        </div>
    </div>
</header>
