<x-filament-panels::page>
    @vite('app-modules/in-app-communication/resources/js/chat.js')

    <div
        class="h-[calc(100vh-12rem)] overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700"
        id="user-chat-app"
        data-user-id="{{ auth()->user()->getKey() }}"
        data-user-name="{{ auth()->user()->name }}"
        data-user-avatar="{{ auth()->user()->avatar_url ?? '' }}"
        wire:ignore
    ></div>
</x-filament-panels::page>
