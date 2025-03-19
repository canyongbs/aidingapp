<?php

use Database\Migrations\Concerns\CanModifyPermissions;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    use CanModifyPermissions;

    private array $permissions = [
        'user.view-email' => 'User',
    ];

    private array $guards = [
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
                $this->createPermissions($this->permissions, $guard);
            });
    }
};
