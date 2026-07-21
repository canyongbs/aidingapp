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
    $gradient = $project->getGradient();
@endphp

<x-filament-widgets::widget>
    <header class="flex flex-col gap-6">
        <div
            class="flex overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900"
        >
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
                        {{ $this->editProjectAction }}
                        {{ $this->manageAccessAction }}
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
                                class="text-blue-600 dark:text-blue-500"
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
