{{--
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
--}}
@php
    use Filament\Support\Colors\Color;
    use Filament\Support\Facades\FilamentColor;

    $color = $project->color?->value;
    $palettes = Color::all();
    $palette = FilamentColor::getColor($color ?? 'blue') ?? $palettes[$color ?? 'blue'] ?? $palettes['blue'];

    $gradient = [
        'from' => $palette[400],
        'to' => $palette[700],
        'darkFrom' => $palette[500],
        'darkTo' => $palette[800],
    ];
@endphp

<x-filament-widgets::widget>
    <header>
        <div
            class="flex flex-col overflow-hidden rounded-xl border border-gray-200 bg-white sm:flex-row dark:border-gray-700 dark:bg-gray-900"
        >
            <div
                class="mt-4 flex aspect-square w-24 shrink-0 items-center justify-center self-center rounded-lg bg-linear-to-b from-[var(--project-gradient-from)] to-[var(--project-gradient-to)] sm:my-4 sm:ms-4 dark:from-[var(--project-gradient-from-dark)] dark:to-[var(--project-gradient-to-dark)]"
                style="
                    --project-gradient-from: {{ $gradient['from'] }};
                    --project-gradient-to: {{ $gradient['to'] }};
                    --project-gradient-from-dark: {{ $gradient['darkFrom'] }};
                    --project-gradient-to-dark: {{ $gradient['darkTo'] }};
                "
            >
                @svg($project->icon ?? 'heroicon-o-clipboard-document-list', 'h-14 w-14 text-white')
            </div>

            <div class="flex-1 p-6">
                <div
                    class="flex flex-col gap-4 sm:flex-row sm:justify-between {{ $project->description ? 'sm:items-start' : 'sm:items-center' }}"
                >
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

                    <div class="flex flex-wrap items-center gap-2">
                        {{ $this->manageAccessAction }}
                        {{ $this->editProjectAction }}
                    </div>
                </div>

                <div class="mt-5 border-t border-gray-200 dark:border-gray-700"></div>

                <div
                    class="mt-4 grid grid-cols-1 gap-x-6 gap-y-3 text-sm text-gray-500 md:grid-cols-2 lg:flex lg:items-center lg:justify-between dark:text-gray-400"
                >
                    <div class="flex items-center gap-1.5">
                        @svg('heroicon-c-building-office', 'h-4 w-4 shrink-0')
                        <span>Department: {{ $project->department?->name ?? 'N/A' }}</span>
                    </div>

                    <div class="flex items-center gap-1.5">
                        @svg('heroicon-c-calendar', 'h-4 w-4 shrink-0')
                        <span>Start Date: {{ $project->start_date?->format('M j, Y') ?? 'N/A' }}</span>
                    </div>

                    <div class="flex items-center gap-1.5">
                        @svg('heroicon-c-flag', 'h-4 w-4 shrink-0')
                        <span>Target Go-Live: {{ $project->target_completion_date?->format('M j, Y') ?? 'N/A' }}</span>
                    </div>

                    <div class="flex items-center gap-1.5">
                        <svg class="h-4 w-4 shrink-0" viewBox="0 0 20 20">
                            <circle
                                cx="10"
                                cy="10"
                                r="8"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="5"
                                class="text-gray-200 dark:text-gray-700"
                            />
                            <circle
                                cx="10"
                                cy="10"
                                r="8"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="5"
                                stroke-linecap="round"
                                stroke-dasharray="{{ 2 * 3.14159 * 8 }}"
                                stroke-dashoffset="{{ 2 * 3.14159 * 8 * (1 - $progress / 100) }}"
                                transform="rotate(-90 10 10)"
                                class="text-primary-600 dark:text-primary-500"
                            />
                        </svg>
                        <span>Progress: {{ $progress }}%</span>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <x-filament-actions::modals />
</x-filament-widgets::widget>
