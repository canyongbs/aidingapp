<?php

use Filament\Infolists\Components\TextEntry;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;

it('uses `N/A` as the default placeholder for nullable display components', function (string $componentClass): void {
    $component = $componentClass::make('department.name');

    expect($component->getPlaceholder())->toBe('N/A');
})->with([
    'text entry' => TextEntry::class,
    'text column' => TextColumn::class,
    'color column' => ColorColumn::class,
    'icon column' => IconColumn::class,
]);

it('allows display components to override the default placeholder', function (string $componentClass, ?string $placeholder): void {
    $component = $componentClass::make('department.name')->placeholder($placeholder);

    expect($component->getPlaceholder())->toBe($placeholder);
})->with([
    'text entry' => TextEntry::class,
    'text column' => TextColumn::class,
    'color column' => ColorColumn::class,
    'icon column' => IconColumn::class,
])->with([
    'custom placeholder' => 'Unassigned',
    'disabled placeholder' => null,
]);
