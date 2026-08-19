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
    use AidingApp\Project\Filament\Resources\Projects\Widgets\ProjectFilesWidget;
    use AidingApp\Project\Filament\Resources\Projects\Widgets\ProjectStatsWidget;
    use AidingApp\Project\Filament\Resources\Projects\Widgets\ProjectAccessWidget;
    use AidingApp\Project\Filament\Resources\Projects\Widgets\ProjectDashboardHeaderWidget;
    use AidingApp\Project\Filament\Resources\Projects\Widgets\ProjectWorkPipelineWidget;
    use AidingApp\Project\Models\Pipeline;
    use AidingApp\Project\Models\ProjectFile;

    $record = $this->getRecord();
    $canViewAccess = ProjectAccessWidget::canView();
    $canViewPipelines = auth()
        ->user()
        ?->can('viewAny', [Pipeline::class, $record]);
    $canViewFiles = auth()
        ->user()
        ?->can('viewAny', [ProjectFile::class, $record]);
@endphp

<x-filament-panels::page>
    @livewire(ProjectDashboardHeaderWidget::class, ['record' => $record])

    @if (ProjectStatsWidget::canView())
        @livewire(ProjectStatsWidget::class, ['record' => $record])
    @endif

    @if ($canViewAccess || $canViewPipelines || $canViewFiles)
        <div x-data="{ tab: 'access' }" class="space-y-6">
            <div class="flex justify-center">
                <x-filament::tabs>
                    @if ($canViewAccess)
                        <x-filament::tabs.item
                            tag="button"
                            x-on:click="tab = 'access'"
                            :alpine-active="'tab === \'access\''"
                            icon="heroicon-m-key"
                        >
                            Access
                        </x-filament::tabs.item>
                    @endif

                    @if ($canViewPipelines)
                        <x-filament::tabs.item
                            tag="button"
                            x-on:click="tab = 'pipelines'"
                            :alpine-active="'tab === \'pipelines\''"
                            icon="heroicon-m-view-columns"
                        >
                            Pipelines
                        </x-filament::tabs.item>
                    @endif

                    @if ($canViewFiles)
                        <x-filament::tabs.item
                            tag="button"
                            x-on:click="tab = 'files'"
                            :alpine-active="'tab === \'files\''"
                            icon="heroicon-m-paper-clip"
                        >
                            Files
                        </x-filament::tabs.item>
                    @endif
                </x-filament::tabs>
            </div>

            @if ($canViewAccess)
                <div x-show="tab === 'access'" x-cloak>
                    @livewire(ProjectAccessWidget::class, ['record' => $record], key('project-access-' . $record->getKey()))
                </div>
            @endif

            @if ($canViewPipelines)
                <div x-show="tab === 'pipelines'" x-cloak>
                    @livewire(ProjectWorkPipelineWidget::class, ['record' => $record], key('project-pipelines-' . $record->getKey()))
                </div>
            @endif

            @if ($canViewFiles)
                <div x-show="tab === 'files'" x-cloak>
                    @livewire(ProjectFilesWidget::class, ['record' => $record], key('project-files-' . $record->getKey()))
                </div>
            @endif
        </div>
    @endif
</x-filament-panels::page>
