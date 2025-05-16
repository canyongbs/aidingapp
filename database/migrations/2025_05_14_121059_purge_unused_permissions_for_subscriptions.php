<?php

use Database\Migrations\Concerns\CanModifyPermissions;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    use CanModifyPermissions;

    /**
    * @var array<string, string>
    */
    private array $permissions = [
        'subscription.view-any' => 'Subscription',
        'subscription.create' => 'Subscription',
        'subscription.*.view' => 'Subscription',
        'subscription.*.update' => 'Subscription',
        'subscription.*.delete' => 'Subscription',
        'subscription.*.restore' => 'Subscription',
        'subscription.*.force-delete' => 'Subscription',
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
