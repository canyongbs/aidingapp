<?php

namespace AidingApp\ServiceManagement\Filament\Resources;

use Filament\Resources\Resource;
use Filament\Resources\Pages\Page;
use AidingApp\ServiceManagement\Models\Product;
use AidingApp\ServiceManagement\Filament\Resources\ProductResource\Pages\EditProduct;
use AidingApp\ServiceManagement\Filament\Resources\ProductResource\Pages\ViewProduct;
use AidingApp\ServiceManagement\Filament\Resources\ProductResource\Pages\ListProducts;
use AidingApp\ServiceManagement\Filament\Resources\ProductResource\Pages\CreateProduct;
use AidingApp\ServiceManagement\Filament\Resources\ProductResource\Pages\ManageProductLicenses;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Service Management';

    protected static ?string $navigationLabel = 'License Management';

    protected static ?int $navigationSort = 31;

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewProduct::class,
            EditProduct::class,
            ManageProductLicenses::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProducts::route('/'),
            'create' => CreateProduct::route('/create'),
            'view' => ViewProduct::route('/{record}'),
            'edit' => EditProduct::route('/{record}/edit'),
            'product_licences' => ManageProductLicenses::route('/{record}/product_licences')
        ];
    }
}
