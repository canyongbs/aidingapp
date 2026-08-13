<div
    class="flex w-full items-center justify-between gap-3"
    x-init="$el.closest('.fi-ta-group-header').firstElementChild.classList.add('grow')"
    x-on:click.stop
>
    <span>{{ $progress }}</span>

    @if ($milestone)
        <div class="flex items-center gap-3">
            @can('update', $milestone)
                <x-filament::link
                    tag="button"
                    icon="heroicon-m-pencil-square"
                    :wire:click="'mountAction(\'editMilestone\', { milestone:\'' . $milestone->getKey() . '\' })'"
                >
                    Edit
                </x-filament::link>
            @endcan

            @can('delete', $milestone)
                <x-filament::link
                    tag="button"
                    icon="heroicon-m-trash"
                    color="danger"
                    :wire:click="'mountAction(\'deleteMilestone\', { milestone:\'' . $milestone->getKey() . '\' })'"
                >
                    Delete
                </x-filament::link>
            @endcan
        </div>
    @endif
</div>
