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
    $assignedModelClass = filled($entry->assigned_to_type) ? \Illuminate\Database\Eloquent\Relations\Relation::getMorphedModel($entry->assigned_to_type) ?? $entry->assigned_to_type : null;

    $assignedTo = $entry->assignedTo;

    if (blank($assignedTo) && filled($assignedModelClass) && filled($entry->assigned_to_id) && class_exists($assignedModelClass)) {
        $assignedTo = $assignedModelClass::query()->find($entry->assigned_to_id);
    }

    $assignedType = match (true) {
        $assignedModelClass === \App\Models\User::class => 'User',
        $assignedModelClass === \AidingApp\Contact\Models\Contact::class => 'Contact',
        default => null,
    };

    $assignedName = match (true) {
        blank($assignedTo) => 'None',
        $assignedTo instanceof \AidingApp\Contact\Models\Contact => $assignedTo->full_name ?? 'None',
        default => $assignedTo->name ?? 'None',
    };

    $assignedLabel = filled($assignedType) && $assignedName !== 'None' ? sprintf('%s (%s)', $assignedName, $assignedType) : $assignedName;

    $dueLabel = $entry->due ? \Illuminate\Support\Str::title($entry->due->diffForHumans(now(), \Carbon\CarbonInterface::DIFF_ABSOLUTE, false, 6)) : null;
    $dueTooltip = $entry->due?->format('M j, Y g:i A');
@endphp

<div
    class="z-10 flex max-w-md transform cursor-move flex-col rounded-lg bg-white p-5 shadow dark:bg-gray-800"
    data-pipeline="{{ $pipeline->getKey() }}"
    data-entry="{{ $entry->getKey() }}"
    wire:key="pipeline-entry-{{ $entry->getKey() }}"
>
    <div class="flex items-start justify-between gap-2">
        <div class="text-base font-semibold text-gray-900 dark:text-white">
            {{ $entry->name }}
        </div>
        <x-filament::icon-button
            class="fi-primary-color"
            wire:click="viewPipelineEntry('{{ $entry->getKey() }}')"
            icon="heroicon-m-arrow-top-right-on-square"
        />
    </div>

    <hr class="my-3 border-gray-200 dark:border-gray-700" />

    <div class="space-y-1 text-sm text-gray-700 dark:text-gray-300">
        <div><span class="font-bold">Milestones:</span> {{ ($entry->milestones_count ?? 0) > 0 ? $entry->milestones_count : 'None' }}</div>
        <div><span class="font-bold">Assets:</span> {{ ($entry->assets_count ?? 0) > 0 ? $entry->assets_count : 'None' }}</div>
        <div>
            <span class="font-bold">Service Requests:</span> {{ ($entry->service_requests_count ?? 0) > 0 ? $entry->service_requests_count : 'None' }}
        </div>
    </div>

    <hr class="my-3 border-gray-200 dark:border-gray-700" />

    <div class="space-y-1 text-sm text-gray-700 dark:text-gray-300">
        <div><span class="font-bold">Assigned:</span> {{ $assignedLabel }}</div>
        <div title="{{ $dueTooltip ?? '' }}"><span class="font-bold">Due:</span> {{ $dueLabel ? $dueLabel : 'None' }}</div>
    </div>
</div>
