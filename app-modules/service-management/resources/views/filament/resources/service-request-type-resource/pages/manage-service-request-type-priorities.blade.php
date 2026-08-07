@use('App\Features\DefaultPriorityFeature')
<x-filament-panels::page>
    @if (DefaultPriorityFeature::active())
        <x-filament::section heading="Priorities Configuration">
            <form wire:submit="savePriorityConfiguration">
                {{ $this->configurationForm }}

                <x-filament::button type="submit" class="mt-4">Save</x-filament::button>
            </form>
        </x-filament::section>
    @endif

    {{ $this->table }}
</x-filament-panels::page>
