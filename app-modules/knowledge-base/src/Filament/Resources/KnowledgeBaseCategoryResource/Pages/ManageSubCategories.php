<?php

namespace AidingApp\KnowledgeBase\Filament\Resources\KnowledgeBaseCategoryResource\Pages;

use App\Features\KnowledgeBaseSubcategory;
use Filament\Resources\Pages\ManageRelatedRecords;
use AidingApp\KnowledgeBase\Filament\Resources\KnowledgeBaseCategoryResource;
use AidingApp\KnowledgeBase\Filament\Resources\KnowledgeBaseCategoryResource\RelationManagers\SubCategoriesRelationManager;

class ManageSubCategories extends ManageRelatedRecords
{
    protected static string $resource = KnowledgeBaseCategoryResource::class;

    protected static string $relationship = 'subCategories';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $title = 'Manage Subcategories';

    public static function getNavigationLabel(): string
    {
        return 'Manage Subcategories';
    }

    public static function canAccess(array $arguments = []): bool
    {
        return parent::canAccess($arguments) && KnowledgeBaseSubcategory::active() && blank($arguments['record']->parent_id);
    }

    public function getRelationManagers(): array
    {
        return [
            SubCategoriesRelationManager::class,
        ];
    }
}
