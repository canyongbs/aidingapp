<?php

use Database\Migrations\Concerns\CanModifyPermissions;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    use CanModifyPermissions;

    /**
    * @var array<string, string>
    */
    private array $permissions = [
        'project.view-any' => 'Project',
        'project.create' => 'Project',
        'project.*.view' => 'Project',
        'project.*.update' => 'Project',
        'project.*.delete' => 'Project',
        'project.*.restore' => 'Project',
        'project.*.force-delete' => 'Project',
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
            ->each(function (string $guard) {
                $this->createPermissions($this->permissions, $guard);
            });
    }

    public function down(): void
    {
        collect($this->guards)
            ->each(fn (string $guard) => $this->deletePermissions(array_keys($this->permissions), $guard));
    }
};
