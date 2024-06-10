<?php

use Illuminate\Database\Migrations\Migration;
use Database\Migrations\Concerns\CanModifyPermissions;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    use CanModifyPermissions;

    private array $permissions = [
        'organization_industry.view-any' => 'OrganizationIndustry',
        'organization_industry.create' => 'OrganizationIndustry',
        'organization_industry.*.view' => 'OrganizationIndustry',
        'organization_industry.*.update' => 'OrganizationIndustry',
        'organization_industry.*.delete' => 'OrganizationIndustry',
        'organization_industry.*.restore' => 'OrganizationIndustry',
        'organization_industry.*.force-delete' => 'OrganizationIndustry',
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
