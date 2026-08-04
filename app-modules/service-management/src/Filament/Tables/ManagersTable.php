<?php

namespace AidingApp\ServiceManagement\Filament\Tables;

use App\Models\User;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ManagersTable
{
    public static function query(string $serviceRequestTypeId, ?string $excludeUserId = null): Builder
    {
        return User::query()
            ->where(function (Builder $query) use ($serviceRequestTypeId): void {
                $query
                    ->whereHas('department.manageableServiceRequestTypes', function (Builder $query) use ($serviceRequestTypeId): void {
                        $query->where('service_request_type_id', $serviceRequestTypeId);
                    })
                    ->orWhereHas('manageableServiceRequestTypes', function (Builder $query) use ($serviceRequestTypeId): void {
                        $query->where('service_request_type_id', $serviceRequestTypeId);
                    });
            })
            ->when($excludeUserId, function (Builder $query) use ($excludeUserId): void {
                $query->whereKeyNot($excludeUserId);
            });
    }

    public static function configure(Table $table): Table
    {
        return $table
            ->query(function () use ($table): Builder {
                $arguments = $table->getArguments();

                return static::query(
                    $arguments['serviceRequestTypeId'],
                    $arguments['excludeUserId'] ?? null,
                );
            })
            ->columns([
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->icon(fn (User $record) => $record->presenceStatus()->getIcon())
                    ->iconColor(fn (User $record) => $record->presenceStatus()->getColor())
                    ->tooltip(fn (User $record) => $record->presenceStatus()->getLabel())
                    ->extraAttributes(fn (User $record): array => ['aria-label' => $record->name . ' (' . $record->presenceStatus()->getLabel() . ')']),
                TextColumn::make('department.name')
                    ->label('Department')
                    ->searchable(),
                TextColumn::make('job_title')
                    ->label('Job Title')
                    ->searchable(),
            ])
            ->filters([
                SelectFilter::make('department')
                    ->relationship('department', 'name'),
            ])
            ->paginationPageOptions([5])
            ->defaultPaginationPageOption(5);
    }
}
