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
    private array $groups = [
        'contact' => 'Contact',
        'engagement' => 'Engagement',
        'engagement_file' => 'Engagement File',
        'engagement_response' => 'Engagement Response',
    ];

    /**
     * @var array<string>
     */
    private array $actions = [
        'view-any',
        'create',
        '*.view',
        '*.update',
        '*.delete',
        '*.restore',
        '*.force-delete',
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
                ->each(fn (string $guard) => $this->deletePermissions(array_keys($this->permissions($guard)), $guard));

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
                    $permissions = Arr::except($this->permissions($guard), keys: DB::table('permissions')
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

    /**
     * @return array<string, string>
     */
    private function permissions(string $guard): array
    {
        $permissions = [];

        foreach ($this->groups as $prefix => $group) {
            foreach ($this->actions as $action) {
                $permissions["{$prefix}.{$action}"] = $group;
            }
        }

        if ($guard === 'web') {
            $permissions['contact.import'] = 'Contact';
        }

        return $permissions;
    }
};
