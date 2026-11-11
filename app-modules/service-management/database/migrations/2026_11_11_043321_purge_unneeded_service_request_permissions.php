<?php

use Database\Migrations\Concerns\CanModifyPermissions;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

return new class () extends Migration {
    use CanModifyPermissions;

    /**
    * @var array<string, string>
    */
    private array $permissions = [
        'service_request_assignment.view-any' => 'Service Request Assignment',
        'service_request_assignment.create' => 'Service Request Assignment',
        'service_request_assignment.*.view' => 'Service Request Assignment',
        'service_request_assignment.*.update' => 'Service Request Assignment',
        'service_request_assignment.*.delete' => 'Service Request Assignment',
        'service_request_assignment.*.restore' => 'Service Request Assignment',
        'service_request_assignment.*.force-delete' => 'Service Request Assignment',
        'service_request_history.view-any' => 'Service Request History',
        'service_request_history.create' => 'Service Request History',
        'service_request_history.*.view' => 'Service Request History',
        'service_request_history.*.update' => 'Service Request History',
        'service_request_history.*.delete' => 'Service Request History',
        'service_request_history.*.restore' => 'Service Request History',
        'service_request_history.*.force-delete' => 'Service Request History',
        'service_request_priority.view-any' => 'Service Request Priority',
        'service_request_priority.create' => 'Service Request Priority',
        'service_request_priority.*.view' => 'Service Request Priority',
        'service_request_priority.*.update' => 'Service Request Priority',
        'service_request_priority.*.delete' => 'Service Request Priority',
        'service_request_priority.*.restore' => 'Service Request Priority',
        'service_request_priority.*.force-delete' => 'Service Request Priority',
        'service_request_update.view-any' => 'Service Request Update',
        'service_request_update.create' => 'Service Request Update',
        'service_request_update.*.view' => 'Service Request Update',
        'service_request_update.*.update' => 'Service Request Update',
        'service_request_update.*.delete' => 'Service Request Update',
        'service_request_update.*.restore' => 'Service Request Update',
        'service_request_update.*.force-delete' => 'Service Request Update',
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
                ->each(fn (string $guard) => $this->deletePermissions(array_keys($this->permissions), $guard));

            DB::afterCommit(function (): void {
                app(PermissionRegistrar::class)->forgetCachedPermissions();
            });
        });
    }

    public function down(): void
    {
        DB::transaction(function (): void {
            collect($this->guards)
                ->each(function (string $guard): void {
                    $permissions = Arr::except($this->permissions, keys: DB::table('permissions')
                        ->where('guard_name', $guard)
                        ->pluck('name')
                        ->all());

                    $this->createPermissions($permissions, $guard);
                });

            DB::afterCommit(function (): void {
                app(PermissionRegistrar::class)->forgetCachedPermissions();
            });
        });
    }
};
