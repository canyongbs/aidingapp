<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

    Aiding App™ is licensed under the Elastic License 2.0. For more details,
    see <https://github.com/canyongbs/aidingapp/blob/main/LICENSE.>

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Aiding App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    <https://www.canyongbs.com> or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AidingApp\InventoryManagement\Filament\Resources;

use AidingApp\InventoryManagement\Filament\Resources\AssetResource\Pages\AssetTimeline;
use AidingApp\InventoryManagement\Filament\Resources\AssetResource\Pages\CreateAsset;
use AidingApp\InventoryManagement\Filament\Resources\AssetResource\Pages\ListAssets;
use AidingApp\InventoryManagement\Filament\Resources\AssetResource\Pages\ManageAssetMaintenanceActivity;
use AidingApp\InventoryManagement\Filament\Resources\AssetResource\Pages\ViewAsset;
use AidingApp\InventoryManagement\Models\Asset;
use AidingApp\InventoryManagement\Models\AssetLocation;
use AidingApp\InventoryManagement\Models\AssetStatus;
use AidingApp\InventoryManagement\Models\AssetType;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Illuminate\Support\Carbon;

// TODO: Can delete this and all underlying pages once we fork
class AssetResource extends Resource
{
    protected static ?string $model = Asset::class;

    protected static ?string $navigationLabel = 'Asset Management';

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';

    protected static ?string $navigationGroup = 'Service Management';

    protected static ?int $navigationSort = 30;

    protected static ?string $breadcrumb = 'Asset Management';

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewAsset::class,
            ManageAssetMaintenanceActivity::class,
            AssetTimeline::class,
        ]);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required(),
                Textarea::make('description')
                    ->required(),
                TextInput::make('serial_number')
                    ->required(),
                Select::make('type_id')
                    ->relationship('type', 'name')
                    ->preload()
                    ->label('Type')
                    ->required()
                    ->exists((new AssetType())->getTable(), 'id'),
                Select::make('status_id')
                    ->relationship('status', 'name')
                    ->preload()
                    ->label('Status')
                    ->required()
                    ->exists((new AssetStatus())->getTable(), 'id'),
                Select::make('location_id')
                    ->relationship('location', 'name')
                    ->preload()
                    ->label('Location')
                    ->required()
                    ->exists((new AssetLocation())->getTable(), 'id'),
                DatePicker::make('purchase_date')
                    ->required()
                    ->live()
                    ->helperText(function (?string $state) {
                        if (blank($state)) {
                            return null;
                        }

                        return (new Asset([
                            'purchase_date' => Carbon::parse($state),
                        ]))->purchase_age;
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAssets::route('/'),
            'create' => CreateAsset::route('/create'),
            'view' => ViewAsset::route('/{record}'),
            'manage-maintenance-activity' => ManageAssetMaintenanceActivity::route('/{record}/maintenance-activity'),
            'asset-timeline' => AssetTimeline::route('/{record}/timeline'),
        ];
    }
}
