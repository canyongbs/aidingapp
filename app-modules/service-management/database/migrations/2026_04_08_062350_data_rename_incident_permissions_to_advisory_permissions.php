<?php

use App\Features\RenameIncidentsFeature;
use Database\Migrations\Concerns\CanModifyPermissions;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class extends Migration
{
    use CanModifyPermissions;

    /**
     * @var array<string, string>
     */
    private array $permissions = [
        'incident.view-any' => 'advisory.view-any',
        'incident.create' => 'advisory.create',
        'incident.*.view' => 'advisory.*.view',
        'incident.*.update' => 'advisory.*.update',
        'incident.*.delete' => 'advisory.*.delete',
        'incident.*.restore' => 'advisory.*.restore',
        'incident.*.force-delete' => 'advisory.*.force-delete',
        'incident_update.view-any' => 'advisory_update.view-any',
        'incident_update.create' => 'advisory_update.create',
        'incident_update.*.view' => 'advisory_update.*.view',
        'incident_update.*.update' => 'advisory_update.*.update',
        'incident_update.*.delete' => 'advisory_update.*.delete',
        'incident_update.*.restore' => 'advisory_update.*.restore',
        'incident_update.*.force-delete' => 'advisory_update.*.force-delete',
    ];

    /**
     * @var array<string>
     */
    private array $guards = [
        'web',
        'api',
    ];

    public function up(): void
    {
        DB::transaction(function () {
            collect($this->guards)->each(function (string $guard) {
                $this->renamePermissions($this->permissions, $guard);
            });

            $this->renamePermissionGroups([
                'Incident' => 'Advisory',
                'Incident Update' => 'Advisory Update',
            ]);

            RenameIncidentsFeature::activate();
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            RenameIncidentsFeature::deactivate();
            
            collect($this->guards)->each(function (string $guard) {
                $this->renamePermissions(array_flip($this->permissions), $guard);
            });

            $this->renamePermissionGroups([
                'Advisory' => 'Incident',
                'Advisory Update' => 'Incident Update',
            ]);
        });
    }
};
