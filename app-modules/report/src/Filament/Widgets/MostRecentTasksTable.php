<?php

namespace AidingApp\Report\Filament\Widgets;

use AidingApp\Contact\Filament\Resources\ContactResource;
use AidingApp\Contact\Models\Contact;
use AidingApp\Task\Models\Task;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Livewire\Attributes\On;

class MostRecentTasksTable extends BaseWidget
{
    public string $cacheTag;

    protected static ?string $heading = 'Most Recent Tasks Added';

    protected static bool $isLazy = false;

    protected static ?string $pollingInterval = null;

    protected int | string | array $columnSpan = [
        'sm' => 1,
        'md' => 4,
        'lg' => 4,
    ];

    public function mount(string $cacheTag)
    {
        $this->cacheTag = $cacheTag;
    }

    #[On('refresh-widgets')]
    public function refreshWidget()
    {
        $this->dispatch('$refresh');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Task::query()
                    ->with(['concern'])
                    ->orderBy('created_at', 'desc')
                    ->limit(10)
            )
            ->columns([
                TextColumn::make('title'),
                TextColumn::make('status'),
                TextColumn::make('association')
                    ->label('Association')
                    ->getStateUsing(fn (Task $record): ?string => ! is_null($record->concern) ? match ($record->concern::class) {
                        Contact::class => 'Contact',
                    } : 'Unrelated'),
                TextColumn::make('concern.display_name')
                    ->label('Related To')
                    ->getStateUsing(fn (Task $record): ?string => $record->concern?->{$record->concern::displayNameKey()} ?? 'N/A')
                    ->url(fn (Task $record) => match ($record->concern ? $record->concern::class : null) {
                        Contact::class => ContactResource::getUrl('view', ['record' => $record->concern]),
                        default => null,
                    }),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime(),
            ])
            ->paginated([10]);
    }
}
