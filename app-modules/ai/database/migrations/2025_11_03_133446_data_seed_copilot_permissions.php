<?php

use CanyonGBS\Common\Database\Migrations\Concerns\CanModifyPermissions;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    use CanModifyPermissions;

    /**
     * @var array<string> $permissions
     */
    private array $permissions = [
        'copilot.view-any' => 'Copilot',
        'copilot.*.view' => 'Copilot',
    ];

    /**
     * @var array<string> $guards
     */
    private array $guards = [
        'web',
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
