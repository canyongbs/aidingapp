<x-filament-panels::page>
    <form class="grid gap-y-6" wire:submit="save">
        {{ $this->form }}

        <x-filament::actions :actions="$this->getFormActions()" :full-width="$this->hasFullWidthFormActions()" />
    </form>
</x-filament-panels::page>