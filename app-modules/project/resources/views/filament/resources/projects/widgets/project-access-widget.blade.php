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
    use AidingApp\Project\Filament\Resources\Projects\ProjectResource;
    use Filament\Facades\Filament;
@endphp

<x-filament-widgets::widget class="h-full">
    <x-filament::section>
        <x-slot name="heading">Project Access</x-slot>
        <x-slot name="afterHeader">
            <x-filament::button color="gray" wire:click="mountAction('manageAccess')">Manage</x-filament::button>
        </x-slot>

        <div class="divide-y divide-gray-200 dark:divide-gray-700">
            {{-- Managers (avatars) --}}
            <div class="py-4 first:pt-0">
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">
                    Managers ({{ $this->managers->count() }})
                </p>
                <div class="mt-2 flex flex-wrap gap-3">
                    @foreach ($this->managers as $user)
                        <x-project::avatar :user="$user" />
                    @endforeach
                </div>
            </div>

            {{-- Auditors (avatars) --}}
            <div class="py-4">
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">
                    Auditors ({{ $this->auditors->count() }})
                </p>
                <div class="mt-2 flex flex-wrap gap-3">
                    @foreach ($this->auditors as $user)
                        <x-project::avatar :user="$user" />
                    @endforeach
                </div>
            </div>

            {{-- Guests (initials - contacts) --}}
            <div class="py-4">
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">
                    Guests ({{ $this->guests->count() }})
                </p>
                <div class="mt-2 flex flex-wrap gap-3">
                    @foreach ($this->guests as $item)
                        @php
                            $isContact = $item instanceof \AidingApp\Contact\Models\Contact;
                            $name = $isContact ? $item->full_name : $item->name;
                            $initials = $isContact
                                ? strtoupper(substr($item->first_name ?? '', 0, 1) . substr($item->last_name ?? '', 0, 1))
                                : strtoupper(
                                    collect(explode(' ', trim($item->name ?? '')))
                                        ->filter()
                                        ->take(2)
                                        ->map(fn (string $word): string => substr($word, 0, 1))
                                        ->implode(''),
                                );
                            $colors = ['bg-amber-500', 'bg-emerald-500', 'bg-violet-500', 'bg-rose-500', 'bg-cyan-500', 'bg-indigo-500'];
                            $color = $colors[crc32($item->getKey()) % count($colors)];
                        @endphp

                        <div class="flex flex-col items-center gap-1">
                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-full {{ $color }} text-sm font-semibold text-white ring-2 ring-white dark:ring-gray-800"
                            >
                                {{ $initials }}
                            </div>
                            <span class="max-w-15 truncate text-xs text-gray-600 dark:text-gray-400">
                                {{ str($name)->limit(8) }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </x-filament::section>

    <x-filament-actions::modals />
</x-filament-widgets::widget>
