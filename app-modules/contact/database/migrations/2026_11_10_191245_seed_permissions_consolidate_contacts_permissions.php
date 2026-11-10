<?php

use Database\Migrations\Concerns\CanModifyPermissions;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

return new class () extends Migration {
    use CanModifyPermissions;

    /**
     * @var array<string, string>
     */
    private array $permissions = [
        'contacts.view-any' => 'Contacts',
        'contacts.create' => 'Contacts',
        'contacts.*.view' => 'Contacts',
        'contacts.*.update' => 'Contacts',
        'contacts.*.delete' => 'Contacts',
        'contacts.*.restore' => 'Contacts',
        'contacts.*.force-delete' => 'Contacts',
    ];

    /**
     * @var array<string, string>
     */
    private array $webOnlyPermissions = [
        'contacts.import' => 'Contacts',
    ];

    public function up(): void
    {
        DB::transaction(function (): void {
            $this->createPermissions($this->permissions, 'api');
            $this->createPermissions([...$this->permissions, ...$this->webOnlyPermissions], 'web');

            DB::afterCommit(function (): void {
                app(PermissionRegistrar::class)->forgetCachedPermissions();
            });
        });
    }

    public function down(): void
    {
        DB::transaction(function (): void {
            $this->deletePermissions(array_keys($this->permissions), 'api');
            $this->deletePermissions(array_keys([...$this->permissions, ...$this->webOnlyPermissions]), 'web');

            DB::afterCommit(function (): void {
                app(PermissionRegistrar::class)->forgetCachedPermissions();
            });
        });
    }
};
