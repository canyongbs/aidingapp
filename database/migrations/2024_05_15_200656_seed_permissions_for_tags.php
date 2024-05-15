<?php

use Illuminate\Database\Migrations\Migration;
use Database\Migrations\Concerns\CanModifyPermissions;

return new class () extends Migration {
    use CanModifyPermissions;

    private array $permissions = [
        'tag.view-any' => 'Tag',
        'tag.create' => 'Tag',
        'tag.*.view' => 'Tag',
        'tag.*.update' => 'Tag',
        'tag.*.delete' => 'Tag',
        'tag.*.restore' => 'Tag',
        'tag.*.force-delete' => 'Tag',
    ];

    private array $guards = [
        'web',
        'api',
    ];

    public function up(): void
    {
        $this->createPermissions($this->permissions, $this->guards);
    }

    public function down(): void
    {
        $this->deletePermissions(array_keys($this->permissions), $this->guards);
    }
};
