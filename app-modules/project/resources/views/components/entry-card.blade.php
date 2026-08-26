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
@use('App\Models\User')
@use('AidingApp\Contact\Models\Contact')
@use('Illuminate\Database\Eloquent\Relations\Relation')
@use('Illuminate\Support\Str')
@php
    $assignedModelClass = filled($entry->assigned_to_type) ? Relation::getMorphedModel($entry->assigned_to_type) ?? $entry->assigned_to_type : null;

    $assignedTo = $entry->assignedTo;

    $assignedType = match (true) {
        $assignedModelClass === User::class => 'User',
        $assignedModelClass === Contact::class => 'Contact',
        default => null,
    };

    $assignedName = match (true) {
        blank($assignedTo) => 'None',
        $assignedTo instanceof Contact => $assignedTo->full_name ?? 'None',
        default => $assignedTo->name ?? 'None',
    };

    $assignedLabel = filled($assignedType) && $assignedName !== 'None' ? sprintf('%s (%s)', $assignedName, $assignedType) : $assignedName;

    $dueLabel = null;

    if ($entry->due) {
        $dueInterval = now()->diff($entry->due);
        $dueDays = (int) $dueInterval->days;
        $dueHours = (int) $dueInterval->h;

        $dueParts = [];

        if ($dueDays > 0) {
            $dueParts[] = sprintf('%d %s', $dueDays, Str::plural('Day', $dueDays));
        }

        if ($dueHours > 0) {
            $dueParts[] = sprintf('%d %s', $dueHours, Str::plural('Hour', $dueHours));
        }

        $dueLabel = filled($dueParts) ? implode(' ', $dueParts) : sprintf('%d %s', $dueHours, Str::plural('Hour', $dueHours));
    }

    $dueTooltip = $entry->due?->format('M j, Y g:i A');

    $canUpdatePipeline = auth()
        ->user()
        ->can('update', $pipeline);
@endphp

<div
    role="listitem"
    class="z-10 flex w-full transform cursor-move flex-col rounded-lg bg-white p-5 shadow dark:bg-gray-800"
    data-pipeline="{{ $pipeline->getKey() }}"
    data-entry="{{ $entry->getKey() }}"
    wire:key="pipeline-entry-{{ $entry->getKey() }}"
>
    <div class="flex items-start justify-between gap-2">
        <button
            type="button"
            class="min-w-0 wrap-break-word text-left text-base font-semibold text-gray-900 hover:underline dark:text-white"
            wire:click="mountAction('{{ $canUpdatePipeline ? 'editPipelineEntry' : 'viewPipelineEntry' }}', { entry: '{{ $entry->getKey() }}' })"
        >
            {{ $entry->name }}
        </button>

        @if ($canUpdatePipeline)
            <x-filament::icon-button
                class="shrink-0"
                icon="heroicon-m-trash"
                color="danger"
                label="Remove"
                tooltip="Remove"
                size="xs"
                wire:click="mountAction('removePipelineEntry', { entry: '{{ $entry->getKey() }}' })"
            />
        @endif
    </div>

    <hr class="my-3 border-gray-200 dark:border-gray-700" />

    <div class="space-y-1 text-sm text-gray-700 dark:text-gray-300">
        <div>
            <span class="font-bold">Milestone:</span>
            {{ $entry->milestone->title ?? 'None' }}
        </div>
        <div>
            <span class="font-bold">Assets:</span>
            {{ ($entry->assets_count ?? 0) > 0 ? $entry->assets_count : 'None' }}
        </div>
        <div>
            <span class="font-bold">Service Requests:</span>
            {{ ($entry->service_requests_count ?? 0) > 0 ? $entry->service_requests_count : 'None' }}
        </div>
    </div>

    <hr class="my-3 border-gray-200 dark:border-gray-700" />

    <div class="space-y-1 text-sm text-gray-700 dark:text-gray-300">
        <div>
            <span class="font-bold">Assigned:</span>
            {{ $assignedLabel }}
        </div>
        <div>
            <span class="font-bold">Start Date:</span>
            {{ $entry->start_date?->format('M j, Y g:i A') ?? 'None' }}
        </div>

        <div title="{{ $dueTooltip ?? '' }}">
            <span class="font-bold">Due:</span>
            {{ $dueLabel ? $dueLabel : 'None' }}
        </div>
    </div>
</div>
