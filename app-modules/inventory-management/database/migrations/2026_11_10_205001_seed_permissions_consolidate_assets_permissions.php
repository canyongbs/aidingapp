<?php

use Database\Migrations\Concerns\CanModifyPermissions;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

return new class () extends Migration {
    use CanModifyPermissions;

    /**
     * @var array<string, string>
     */
    private array $permissions = [
        'assets.view-any' => 'Assets',
        'assets.create' => 'Assets',
        'assets.*.view' => 'Assets',
        'assets.*.update' => 'Assets',
        'assets.*.delete' => 'Assets',
        'assets.*.restore' => 'Assets',
        'assets.*.force-delete' => 'Assets',
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
        DB::transaction(function (): void {
            collect($this->guards)
                ->each(fn (string $guard) => $this->createPermissions($this->permissions, $guard));

            DB::afterCommit(function (): void {
                app(PermissionRegistrar::class)->forgetCachedPermissions();
            });
        });
    }

    public function down(): void
    {
        DB::transaction(function (): void {
            collect($this->guards)
                ->each(fn (string $guard) => $this->deletePermissions(array_keys($this->permissions), $guard));

            DB::afterCommit(function (): void {
                app(PermissionRegistrar::class)->forgetCachedPermissions();
            });
        });
    }
};
