<?php

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;
use Database\Migrations\Concerns\CanModifyPermissions;

return new class () extends Migration {
    use CanModifyPermissions;

    private array $permissions = [
        'product.view-any' => 'Product',
        'product.create' => 'Product',
        'product.*.view' => 'Product',
        'product.*.update' => 'Product',
        'product.*.delete' => 'Product',
        'product.*.restore' => 'Product',
        'product.*.force-delete' => 'Product',
        'product-license.view-any' => 'Product License',
        'product-license.create' => 'Product License',
        'product-license.*.view' => 'Product License',
        'product-license.*.update' => 'Product License',
        'product-license.*.delete' => 'Product License',
        'product-license.*.restore' => 'Product License',
        'product-license.*.force-delete' => 'Product License',
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
