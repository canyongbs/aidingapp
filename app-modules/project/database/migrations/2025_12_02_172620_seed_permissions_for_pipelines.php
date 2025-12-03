<?php

use Database\Migrations\Concerns\CanModifyPermissions;
use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class extends Migration
{
     use CanModifyPermissions;

    /**
     * @var array<string, string>
     */
    private array $permissions = [
        'pipeline.view-any' => 'Pipeline',
        'pipeline.create' => 'Pipeline',
        'pipeline.*.view' => 'Pipeline',
        'pipeline.*.update' => 'Pipeline',
        'pipeline.*.delete' => 'Pipeline',
        'pipeline.*.restore' => 'Pipeline',
        'pipeline.*.force-delete' => 'Pipeline',
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
        collect($this->guards)
            ->each(function (string $guard) {
                $permissions = Arr::except($this->permissions, keys: DB::table('permissions')
                    ->where('guard_name', $guard)
                    ->pluck('name')
                    ->all());

                $this->createPermissions($permissions, $guard);
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
