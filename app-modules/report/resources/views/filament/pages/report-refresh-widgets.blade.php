@php
    use Illuminate\Support\Facades\Cache;
    use Carbon\Carbon;
@endphp
<x-filament-widgets::widget>
    <div class="flex flex-col items-center md:flex-row">
        <div class="flex-1">
            <p class="text-xs">
                This report was last updated at {{ $lastRefreshTime->format('l, F j, Y g:i A') }}.
            </p>
        </div>

        <div class="flex-shrink-0">

            <x-filament::button
                type="button"
                color="gray"
                icon="heroicon-m-arrow-path"
                labeled-from="sm"
                tag="button"
                wire:click="removeWidgetCache('{{ $this->cacheTag }}')"
            >
                {{ 'Refresh' }}
            </x-filament::button>

        </div>
    </div>
</x-filament-widgets::widget>
