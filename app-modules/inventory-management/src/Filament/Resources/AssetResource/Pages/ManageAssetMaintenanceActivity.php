<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/aidingapp/blob/main/LICENSE.

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
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\InventoryManagement\Filament\Resources\AssetResource\Pages;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Support\Htmlable;
use Filament\Resources\Pages\ManageRelatedRecords;
use AdvisingApp\InventoryManagement\Filament\Resources\AssetResource;
use AdvisingApp\InventoryManagement\Filament\Resources\AssetResource\RelationManagers\MaintenanceActivitiesRelationManager;

class ManageAssetMaintenanceActivity extends ManageRelatedRecords
{
    protected static string $resource = AssetResource::class;

    // TODO: Obsolete when there is no table, remove from Filament
    protected static string $relationship = 'maintenanceActivities';

    protected static ?string $navigationLabel = 'Maintenance';

    protected static ?string $breadcrumb = 'Maintenance';

    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';

    public function getTitle(): string | Htmlable
    {
        return 'Manage Asset Maintenance Activity';
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
            MaintenanceActivitiesRelationManager::class,
        ])
            ->reject(fn ($relationManager) => $record && (! $relationManager::canViewForRecord($record, static::class)))
            ->toArray();
    }
}
