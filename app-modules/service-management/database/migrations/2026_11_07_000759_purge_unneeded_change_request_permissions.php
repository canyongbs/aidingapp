<?php

use Illuminate\Database\Migrations\Migration;
use Database\Migrations\Concerns\CanModifyPermissions;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    use CanModifyPermissions;
    
    /**
    * @var array<string, string>
    */
    private array $permissions = [
        'change_request_status.view-any' => 'Change Request Status',
        'change_request_status.create' => 'Change Request Status',
        'change_request_status.*.view' => 'Change Request Status',
        'change_request_status.*.update' => 'Change Request Status',
        'change_request_status.*.delete' => 'Change Request Status',
        'change_request_status.*.restore' => 'Change Request Status',
        'change_request_status.*.force-delete' => 'Change Request Status',
        'change_request_type.view-any' => 'Change Request Type',
        'change_request_type.create' => 'Change Request Type',
        'change_request_type.*.view' => 'Change Request Type',
        'change_request_type.*.update' => 'Change Request Type',
        'change_request_type.*.delete' => 'Change Request Type',
        'change_request_type.*.restore' => 'Change Request Type',
        'change_request_type.*.force-delete' => 'Change Request Type',
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
            collect($this->guards)
                ->each(function (string $guard) {
                    $permissions = Arr::except($this->permissions, keys: DB::table('permissions')
                        ->where('guard_name', $guard)
                        ->pluck('name')
                        ->all());

                    $this->createPermissions($permissions, $guard);
                });
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            collect($this->guards)
                ->each(fn (string $guard) => $this->deletePermissions(array_keys($this->permissions), $guard));
        });
    }
};
