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
    * @var array<string, string>
    */
    private array $permissions = [
        'copilot.view-any' => 'Copilot',
        'copilot.*.view' => 'Copilot',
        'custom_copilot.*.view' => 'Custom Copilot',
        'custom_copilot.*.update' => 'Custom Copilot',
        'custom_copilot.view-any' => 'Custom Copilot',
        'custom_copilot.create' => 'Custom Copilot',
        'license.view-any' => 'License',
        'license.create' => 'License',
        'license.*.view' => 'License',
        'license.*.update' => 'License',
        'license.*.delete' => 'License',
        'license.*.restore' => 'License',
        'license.*.force-delete' => 'License',
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
            ->each(fn (string $guard) => $this->deletePermissions(array_keys($this->permissions), $guard));
    }

    public function down(): void
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
};
