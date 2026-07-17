@props([
    'user',
])
@php
    use Filament\Facades\Filament;
@endphp

<div class="flex flex-col items-center gap-1">
    <img
        src="{{ Filament::getUserAvatarUrl($user) }}"
        alt="{{ $user->name }}"
        class="h-10 w-10 rounded-full object-cover ring-2 ring-white dark:ring-gray-800"
    />
    <span class="max-w-[60px] truncate text-xs text-gray-600 dark:text-gray-400">
        {{ str($user->name)->before(' ') }}
    </span>
</div>
