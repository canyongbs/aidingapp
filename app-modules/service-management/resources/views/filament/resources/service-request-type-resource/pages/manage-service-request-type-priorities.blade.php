<x-filament-panels::page>
    <x-filament::section heading="Priorities Configuration">
        <form wire:submit="savePriorityConfiguration">
            {{ $this->configurationForm }}

            <x-filament::button type="submit" class="mt-4">Save</x-filament::button>
        </form>
    </x-filament::section>

    {{ $this->table }}
</x-filament-panels::page>
