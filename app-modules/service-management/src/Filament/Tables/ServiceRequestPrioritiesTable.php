<?php

namespace AidingApp\ServiceManagement\Filament\Tables;

use AidingApp\ServiceManagement\Models\ServiceRequestPriority;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ServiceRequestPrioritiesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => ServiceRequestPriority::query()->whereNull('sla_id'))
            ->columns([
                TextColumn::make('type.name')
                    ->label('Type')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('order')
                    ->label('Priority Order')
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
            ])
            ->defaultGroup(
                Group::make('type.name')
                    ->label('Service Request Type')
                    ->collapsible(),
            );
    }
}
