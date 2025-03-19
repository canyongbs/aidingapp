<?php

use Database\Migrations\Concerns\CanModifyPermissions;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    use CanModifyPermissions;

    private array $permissions = [
        'service_monitoring.view-any' => 'Service Monitoring',
        'service_monitoring.create' => 'Service Monitoring',
        'service_monitoring.*.view' => 'Service Monitoring',
        'service_monitoring.*.update' => 'Service Monitoring',
        'service_monitoring.*.delete' => 'Service Monitoring',
        'service_monitoring.*.restore' => 'Service Monitoring',
        'service_monitoring.*.force-delete' => 'Service Monitoring',
    ];

    private array $guards = [
        'web',
        'api',
    ];

    public function up(): void
    {
        collect($this->guards)
            ->each(function (string $guard) {
                $this->createPermissions($this->permissions, $guard);
            });
    }

    public function down(): void
    {
        collect($this->guards)
            ->each(function (string $guard) {
                $this->deletePermissions(array_keys($this->permissions), $guard);
            });
    }
};
