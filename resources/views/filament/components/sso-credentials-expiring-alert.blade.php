@php
    use Filament\Support\Colors\Color;
@endphp

<div
    class="bg-custom-600 sticky top-16 z-10 flex h-10 items-center px-6 py-2 text-sm font-medium text-white"
    style="--color-600: {{ Color::all()[$color->value][600] }}"
    wire:loading.remove
>
    The single sign-on key will expire within 45 days and must be updated to prevent service interruption.
</div>