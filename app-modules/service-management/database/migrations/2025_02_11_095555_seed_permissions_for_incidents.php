<?php

use Database\Migrations\Concerns\CanModifyPermissions;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    use CanModifyPermissions;

    private array $permissions = [
        'incident.view-any' => 'Incident',
        'incident.create' => 'Incident',
        'incident.*.view' => 'Incident',
        'incident.*.update' => 'Incident',
        'incident.*.delete' => 'Incident',
        'incident.*.restore' => 'Incident',
        'incident.*.force-delete' => 'Incident',
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
