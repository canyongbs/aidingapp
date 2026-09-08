<?php

namespace AidingApp\Group\Filament\Resources\Groups;

use AidingApp\Group\Filament\Resources\Groups\Pages\CreateGroup;
use AidingApp\Group\Filament\Resources\Groups\Pages\EditGroup;
use AidingApp\Group\Filament\Resources\Groups\Pages\ListGroups;
use AidingApp\Group\Filament\Resources\Groups\Pages\ViewGroup;
use AidingApp\Group\Filament\Resources\Groups\RelationManagers\UsersRelationManager;
use AidingApp\Group\Filament\Resources\Groups\Schemas\GroupForm;
use AidingApp\Group\Models\Group;
use App\Enums\NavigationGroup;
use App\Features\GroupManagementFeature;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use UnitEnum;

class GroupResource extends Resource
{
    protected static ?string $model = Group::class;

    protected static string | UnitEnum | null $navigationGroup = NavigationGroup::Users;

    protected static ?int $navigationSort = 30;

    public static function form(Schema $schema): Schema
    {
        return GroupForm::configure($schema);
    }

    public static function canAccess(): bool
    {
        return GroupManagementFeature::active() && parent::canAccess();
    }

    public static function getRelations(): array
    {
        return [
            UsersRelationManager::make(),
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListGroups::route('/'),
            'create' => CreateGroup::route('/create'),
            'view' => ViewGroup::route('/{record}'),
            'edit' => EditGroup::route('/{record}/edit'),
        ];
    }
}
