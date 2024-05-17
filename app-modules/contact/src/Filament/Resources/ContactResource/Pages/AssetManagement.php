<?php

namespace AidingApp\Contact\Filament\Resources\ContactResource\Pages;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Support\Htmlable;
use Filament\Resources\Pages\ManageRelatedRecords;
use App\Filament\Concerns\FiltersManagersFromGroups;
use Filament\Resources\RelationManagers\RelationGroup;
use AidingApp\Contact\Filament\Resources\ContactResource;
use AidingApp\Contact\Filament\Resources\ContactResource\RelationManagers\AssetCheckInRelationManager;
use AidingApp\Contact\Filament\Resources\ContactResource\RelationManagers\AssetCheckOutRelationManager;

class AssetManagement extends ManageRelatedRecords
{
    use FiltersManagersFromGroups;

    protected static string $resource = ContactResource::class;

    protected static string $relationship = 'assetCheckIns';

    protected static ?string $navigationLabel = 'Assets';

    protected static ?string $breadcrumb = 'Assets';

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    public function getTitle(): string | Htmlable
    {
        return 'Assets';
    }

    public static function canAccess(array $arguments = []): bool
    {
        return (bool) count(static::managers($arguments['record'] ?? null));
    }

    public function getRelationManagers(): array
    {
        return static::managers($this->getRecord());
    }

    private static function managers(?Model $record = null): array
    {
        return collect([
            RelationGroup::make('Assets', [
                AssetCheckOutRelationManager::class,
                AssetCheckInRelationManager::class,
            ]),
        ])
            ->map(fn ($relationManager) => self::filterRelationManagers($relationManager, $record))
            ->filter()
            ->toArray();
    }
}
