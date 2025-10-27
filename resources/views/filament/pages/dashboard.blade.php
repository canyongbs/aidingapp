@php
    use AidingApp\Authorization\Filament\Widgets\UnlicensedNotice;
    use App\Filament\Widgets\ListServiceRequestTableWidgets;
    use App\Filament\Widgets\ServiceRequestDonutChart;
    use App\Filament\Widgets\ServiceRequestLineChart;
    use App\Filament\Widgets\ServiceRequestWidget;
@endphp

<x-filament-panels::page>
    <div class="grid gap-6">
        <div
            class="col-span-full flex flex-col items-center rounded-lg bg-black bg-cover bg-no-repeat px-16 py-8 lg:col-span-5"
            style="background-image: url('{{ asset('images/banner.png') }}')"
        >
            <div class="grid w-full gap-1 text-center md:text-start md:text-3xl">
                <div class="text-3xl font-bold text-white">
                    Welcome,
                </div>

                <div class="text-4xl font-bold text-white">
                    {{ auth()->user()->name }}!
                </div>

                <div class="text-xl text-gray-200">
                    <p id="current-date"></p>
                </div>

                <div class="text-xl text-gray-200">
                    <p id="current-time"></p>
                </div>
            </div>
        </div>

        <div class="col-span-full flex flex-col gap-3 lg:col-span-5">
            @if (UnlicensedNotice::canView())
                @livewire(UnlicensedNotice::class)
            @else
                @livewire(ServiceRequestWidget::class)

                <div class="flex gap-3">
                    <div class="w-full md:w-1/2">
                        @livewire(ServiceRequestLineChart::class)
                    </div>
                    <div class="w-full md:w-1/2">
                        @livewire(ServiceRequestDonutChart::class)
                    </div>
                </div>
                
                @livewire(ListServiceRequestTableWidgets::class)

            @endif
        </div>
    </div>
</x-filament-panels::page>

<script>
    document.getElementById('current-date').textContent = (new Date()).toLocaleDateString('en-US', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
    document.getElementById('current-time').textContent = (new Date()).toLocaleTimeString('en-US', {
        hour: 'numeric',
        minute: '2-digit',
        hour12: true
    });
</script>