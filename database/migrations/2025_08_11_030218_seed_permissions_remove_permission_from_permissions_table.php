<?php

use Database\Migrations\Concerns\CanModifyPermissions;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class extends Migration
{
    use CanModifyPermissions;

    /**
     * @var array<string> $permissions
     */
    private array $permissions = [
        'permission.view-any' => 'Permission',
        'permission.*.view' => 'Permission',
    ];

    /**
     * @var array<string> $guards
     */
    private array $guards = [
        'web',
    ];

    public function up(): void
    {
        collect($this->guards)
            ->each(fn (string $guard) => $this->deletePermissions(array_keys($this->permissions), $guard));
    }

    public function down(): void
    {
        collect($this->guards)
            ->each(function (string $guard) {
                $permisisons = Arr::except($this->permissions, DB::table('permissions')
                    ->where('guard_name', $guard)
                    ->pluck('name')
                    ->all());

                $this->createPermissions($permisisons, $guard);
            });
    }
};
