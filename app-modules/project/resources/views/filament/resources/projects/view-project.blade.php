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
    use AidingApp\Project\Filament\Resources\Projects\ProjectResource;
    use AidingApp\Project\Filament\Resources\Projects\Widgets\ProjectFilesWidget;
    use AidingApp\Project\Filament\Resources\Projects\Widgets\ProjectStats;
    use AidingApp\Project\Filament\Resources\Projects\Widgets\ProjectAccessWidget;
    use AidingApp\Project\Filament\Resources\Projects\Widgets\ProjectDashboardHeaderWidget;
    use AidingApp\Project\Filament\Resources\Projects\Widgets\ProjectMilestonesWidget;
    use AidingApp\Project\Filament\Resources\Projects\Widgets\ProjectWorkPipelineWidget;
    use Filament\Facades\Filament;

    $record = $this->getRecord();
@endphp

<x-filament-panels::page>
    @livewire(
        ProjectDashboardHeaderWidget::class,
        [
            'record' => $record,
        ]
    )

    @livewire(ProjectStats::class, ['record' => $record])

    <div class="grid grid-cols-1 items-stretch gap-8 lg:grid-cols-2">
        @if (ProjectAccessWidget::canView())
            @livewire(
                ProjectAccessWidget::class,
                [
                    'record' => $this->getRecord(),
                    'resource' => ProjectResource::class,
                ]
            )
        @endif

        <div class="h-full">
            @if (ProjectMilestonesWidget::canView())
                @livewire(
                    ProjectMilestonesWidget::class,
                    [
                        'record' => $this->getRecord(),
                        'resource' => ProjectResource::class,
                    ]
                )
            @endif
        </div>
    </div>

    {{-- Pipeline Widget --}}
    @if (ProjectWorkPipelineWidget::canView())
        @livewire(ProjectWorkPipelineWidget::class, ['record' => $record])
    @endif

    {{-- Files Widget --}}
    @if (ProjectFilesWidget::canView())
        @livewire(ProjectFilesWidget::class, ['record' => $record])
    @endif
</x-filament-panels::page>
