<?php

use Database\Migrations\Concerns\CanModifyPermissions;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    use CanModifyPermissions;

    private array $permissions = [
        'purchase.view-any' => 'Purchase',
        'purchase.create' => 'Purchase',
        'purchase.*.view' => 'Purchase',
        'purchase.*.update' => 'Purchase',
        'purchase.*.delete' => 'Purchase',
        'purchase.*.restore' => 'Purchase',
        'purchase.*.force-delete' => 'Purchase',
    ];

    private array $guards = [
        'web',
        'api',
    ];

    public function up(): void
    {
        collect($this->guards)
            ->each(function (string $guard) {
                $this->createPermissions($this->permissions, $guard);
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
