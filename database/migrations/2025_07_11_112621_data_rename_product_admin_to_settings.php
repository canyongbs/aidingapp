<?php

use CanyonGBS\Common\Database\Migrations\Concerns\CanModifyPermissions;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    use CanModifyPermissions;

    /**
     * @var array<string, string> $productAdminToSettingsPermissions
     */
    private array $productAdminToSettingsPermissions = [
        'product_admin.*.delete' => 'settings.*.delete',
        'product_admin.*.force-delete' => 'settings.*.force-delete',
        'product_admin.*.restore' => 'settings.*.restore',
        'product_admin.*.update' => 'settings.*.update',
        'product_admin.*.view' => 'settings.*.view',
        'product_admin.create' => 'settings.create',
        'product_admin.view-any' => 'settings.view-any',
    ];

    /**
     * @var array<string, string> $maintenanceProviderPermissions
     */
    private array $maintenanceProviderPermissions = [
        'maintenance_provider.view-any' => 'Maintenance Provider',
        'maintenance_provider.create' => 'Maintenance Provider',
        'maintenance_provider.*.view' => 'Maintenance Provider',
        'maintenance_provider.*.update' => 'Maintenance Provider',
        'maintenance_provider.*.delete' => 'Maintenance Provider',
        'maintenance_provider.*.restore' => 'Maintenance Provider',
        'maintenance_provider.*.force-delete' => 'Maintenance Provider',
    ];

    /**
     * @var array<string> $guards
     */
    private array $guards = [
        'web',
        'api',
    ];

    public function up(): void
    {
        DB::transaction(function () {

            collect($this->guards)
            ->each(fn (string $guard) => $this->deletePermissions(array_keys($this->maintenanceProviderPermissions), $guard));

            collect($this->guards)->each(function (string $guard) {
                $this->renamePermissions($this->productAdminToSettingsPermissions, $guard);
            });

            $this->renamePermissionGroups([
                'Product Admin' => 'Settings',
            ]);
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            
            collect($this->guards)
            ->each(function (string $guard) {
                $this->createPermissions($this->maintenanceProviderPermissions, $guard);
            });

            collect($this->guards)->each(function (string $guard) {
                $this->renamePermissions(array_flip($this->productAdminToSettingsPermissions), $guard);
            });

            $this->renamePermissionGroups([
                'Settings' => 'Product Admin',
            ]);
        });
    }
};
