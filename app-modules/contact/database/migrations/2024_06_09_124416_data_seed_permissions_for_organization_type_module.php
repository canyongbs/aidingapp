<?php

use Illuminate\Support\Arr;
use Illuminate\Database\Migrations\Migration;
use Database\Migrations\Concerns\CanModifyPermissions;
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    use CanModifyPermissions;

    private array $permissions = [
        'organization_type.view-any' => 'OrganizationType',
        'organization_type.create' => 'OrganizationType',
        'organization_type.*.view' => 'OrganizationType',
        'organization_type.*.update' => 'OrganizationType',
        'organization_type.*.delete' => 'OrganizationType',
        'organization_type.*.restore' => 'OrganizationType',
        'organization_type.*.force-delete' => 'OrganizationType',
    ];

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
        $this->deletePermissions(array_keys($this->permissions), $this->guards);
    }
};
