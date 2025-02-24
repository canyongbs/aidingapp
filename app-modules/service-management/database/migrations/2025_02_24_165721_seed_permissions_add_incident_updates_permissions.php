<?php

use Illuminate\Database\Migrations\Migration;
use Database\Migrations\Concerns\CanModifyPermissions;

return new class extends Migration
{
    use CanModifyPermissions;
    
    private array $permissions = [
        'incident_update.view-any' => 'Incident Update',
        'incident_update.create' => 'Incident Update',
        'incident_update.*.view' => 'Incident Update',
        'incident_update.*.update' => 'Incident Update',
        'incident_update.*.delete' => 'Incident Update',
        'incident_update.*.restore' => 'Incident Update',
        'incident_update.*.force-delete' => 'Incident Update',
    ];

    private array $guards = [
        'web',
        'api',
    ];

    public function up(): void
    {
        foreach ($this->guards as $guard) {
            $this->createPermissions($this->permissions, $guard);
        }
    }

    public function down(): void
    {
        foreach ($this->guards as $guard) {
            $this->deletePermissions(array_keys($this->permissions), $guard);
        }
    }
};
