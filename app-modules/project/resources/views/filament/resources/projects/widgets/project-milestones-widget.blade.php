@php
    use AidingApp\Project\Filament\Resources\Projects\Pages\ManageMilestones;
@endphp

<x-filament-widgets::widget class="h-full">
    <x-filament::section>
        <x-slot name="heading">Project Milestones</x-slot>
        <x-slot name="afterHeader">
            <x-filament::button color="gray" wire:click="mountAction('manageMilestoneCreate')">
                Manage
            </x-filament::button>
        </x-slot>

        <div class="h-96 overflow-y-auto">
            {{ $this->table }}
        </div>
    </x-filament::section>

    <x-filament-actions::modals />
</x-filament-widgets::widget>
