@php
    use AidingApp\Project\Filament\Resources\Projects\ProjectResource;
    use Filament\Facades\Filament;
@endphp

<x-filament-widgets::widget class="h-full">
    <x-filament::section>
        <x-slot name="heading">Project Access</x-slot>
        <x-slot name="afterHeader">
            <x-filament::button color="gray" wire:click="mountAction('manageAccess')">Manage</x-filament::button>
        </x-slot>

        <div class="h-96 divide-y divide-gray-200 overflow-y-auto dark:divide-gray-700">
            {{-- Managers (avatars) --}}
            <div class="py-4 first:pt-0">
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">
                    Managers ({{ $this->getManagers()->count() }})
                </p>
                <div class="mt-2 flex flex-wrap gap-3">
                    @foreach ($this->getManagers() as $user)
                        <x-project::avatar-component :user="$user" />
                    @endforeach
                </div>
            </div>

            {{-- Auditors (avatars) --}}
            <div class="py-4">
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">
                    Auditors ({{ $this->getAuditors()->count() }})
                </p>
                <div class="mt-2 flex flex-wrap gap-3">
                    @foreach ($this->getAuditors() as $user)
                        <x-project::avatar-component :user="$user" />
                    @endforeach
                </div>
            </div>

            {{-- Guests (initials - contacts) --}}
            <div class="py-4">
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">
                    Guests ({{ $this->getGuests()->count() }})
                </p>
                <div class="mt-2 flex flex-wrap gap-3">
                    @foreach ($this->getGuests() as $contact)
                        @php
                            $initials = strtoupper(substr($contact->first_name ?? '', 0, 1) . substr($contact->last_name ?? '', 0, 1));
                            $colors = ['bg-amber-500', 'bg-emerald-500', 'bg-violet-500', 'bg-rose-500', 'bg-cyan-500', 'bg-indigo-500'];
                            $color = $colors[crc32($contact->getKey()) % count($colors)];
                        @endphp

                        <div class="flex flex-col items-center gap-1">
                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-full {{ $color }} text-sm font-semibold text-white ring-2 ring-white dark:ring-gray-800"
                            >
                                {{ $initials }}
                            </div>
                            <span class="max-w-15 truncate text-xs text-gray-600 dark:text-gray-400">
                                {{ str($contact->first_name)->limit(8) }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </x-filament::section>

    <x-filament-actions::modals />
</x-filament-widgets::widget>
