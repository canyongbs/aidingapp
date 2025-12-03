{{--
    <COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

    Notice:

    - This software is closed source and the source code is a trade secret.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ is a registered trademarks of Canyon GBS LLC, and we are
      committed to enforcing and protecting our trademarks vigorously.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
--}}
<div
    class="z-10 flex max-w-md transform cursor-move flex-col rounded-lg bg-white p-5 shadow dark:bg-gray-800"
    data-pipeline="{{ $pipeline->getKey() }}"
    data-entry="{{ $entry->getKey() }}"
    wire:key="pipeline-{{ $pipeline->getKey() }}-{{ time() }}"
>
    <div class="flex items-center justify-between">
        <div class="text-base font-semibold text-gray-900 dark:text-white">
            <small class="capitalize">
                {{ $entry->organizable?->name }}
            </small>
            <br>
            <x-filament::badge color="success">
                {{ $entry->pipelineEntryType->classification === PipelineEntryTypeClassification::Opportunity && filled($entry->opportunity_value) ? $entry->opportunity_value : 'N/A' }}
            </x-filament::badge>
            <br>
            @if ($entry->organizable?->getMorphClass() === 'client')
                <x-filament::badge color="gray">
                    {{ $entry->organizable?->partner?->name }}
                </x-filament::badge>
            @endif
        </div>
        <x-filament::icon-button
            class="fi-primary-color"
            wire:click="viewPipelineEntry('{{ $entry->getKey() }}')"
            icon="heroicon-m-arrow-top-right-on-square"
        />
    </div>
</div>
