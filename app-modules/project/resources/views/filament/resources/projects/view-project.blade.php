@php
    use AidingApp\Project\Filament\Resources\Projects\ProjectResource;
    use AidingApp\Project\Filament\Widgets\ProjectWorkPipelineWidget;
    use AidingApp\Project\Filament\Widgets\ProjectDashboardFilesWidget;
    use AidingApp\Project\Filament\Resources\Projects\Widgets\ProjectStats;
    use Filament\Facades\Filament;

    $record = $this->getRecord();
    $managers = $record->managerUsers;
    $auditors = $record->auditorUsers;
    $guests = $record->guestContacts;
    $filesCount = $record->files()->count();
    $pipelineTasksCount = $record
        ->pipelines()
        ->withCount('entries')
        ->get()
        ->sum('entries_count');
    $milestonesCount = $record->milestones()->count();
@endphp

<x-filament-panels::page>
    {{-- Stats Cards --}}
    @livewire(ProjectStats::class, ['record' => $record])

    {{-- Project Header --}}

    {{-- Two-column: Access + Milestones --}}
    <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
        {{-- Project Access --}}
        <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-900">
            <div class="flex items-center justify-between">
                <h3 class="text-base font-semibold text-gray-950 dark:text-white">Project Access</h3>
                <a
                    href="{{ ProjectResource::getUrl('manage-managers', ['record' => $record]) }}"
                    class="text-sm font-medium text-primary-600 hover:text-primary-500 dark:text-primary-400"
                >
                    Manage
                </a>
            </div>

            {{-- Managers (avatars) --}}
            <div class="mt-4">
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Managers ({{ $managers->count() }})</p>
                <div class="mt-2 flex flex-wrap gap-3">
                    @foreach ($managers as $user)
                        <div class="flex flex-col items-center gap-1">
                            <img
                                src="{{ Filament::getUserAvatarUrl($user) }}"
                                alt="{{ $user->name }}"
                                class="h-10 w-10 rounded-full object-cover ring-2 ring-white dark:ring-gray-800"
                            />
                            <span class="max-w-[60px] truncate text-xs text-gray-600 dark:text-gray-400">
                                {{ str($user->name)->before(' ') }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Auditors (avatars) --}}
            <div class="mt-4">
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Auditors ({{ $auditors->count() }})</p>
                <div class="mt-2 flex flex-wrap gap-3">
                    @foreach ($auditors as $user)
                        <div class="flex flex-col items-center gap-1">
                            <img
                                src="{{ Filament::getUserAvatarUrl($user) }}"
                                alt="{{ $user->name }}"
                                class="h-10 w-10 rounded-full object-cover ring-2 ring-white dark:ring-gray-800"
                            />
                            <span class="max-w-[60px] truncate text-xs text-gray-600 dark:text-gray-400">
                                {{ str($user->name)->before(' ') }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Guests (initials - contacts) --}}
            <div class="mt-4">
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Guests ({{ $guests->count() }})</p>
                <div class="mt-2 flex flex-wrap gap-3">
                    @foreach ($guests as $contact)
                        @php
                            $initials = strtoupper(substr($contact->first_name ?? '', 0, 1) . substr($contact->last_name ?? '', 0, 1));
                            $colors = ['bg-amber-500', 'bg-emerald-500', 'bg-violet-500', 'bg-rose-500', 'bg-cyan-500', 'bg-indigo-500'];
                            $color = $colors[crc32($contact->getKey()) % count($colors)];
                        @endphp

                        <div class="flex flex-col items-center gap-1">
                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-full {{ $color }} text-sm font-semibold text-white ring-2 ring-white dark:ring-gray-800"
                            >
                                {{ $initials }}
                            </div>
                            <span class="max-w-[60px] truncate text-xs text-gray-600 dark:text-gray-400">
                                {{ str($contact->first_name)->limit(8) }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Milestones (named schema) --}}
        {{--
            <div>
            {{ $this->milestones }}
            </div>
        --}}
    </div>

    {{-- Pipeline Widget --}}
    {{-- @livewire(ProjectWorkPipelineWidget::class, ['record' => $record]) --}}

    {{-- Files Widget --}}
    {{-- @livewire(ProjectDashboardFilesWidget::class, ['record' => $record]) --}}
</x-filament-panels::page>
