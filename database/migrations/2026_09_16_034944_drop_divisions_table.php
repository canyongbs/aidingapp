<?php

use Database\Migrations\Concerns\CanModifyPermissions;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    use CanModifyPermissions;

    /**
     * @var array<string, string>
     */
    private array $permissions = [
        'division.view-any' => 'Division',
        'division.create' => 'Division',
        'division.*.view' => 'Division',
        'division.*.update' => 'Division',
        'division.*.delete' => 'Division',
        'division.*.restore' => 'Division',
        'division.*.force-delete' => 'Division',
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
                ->each(fn (string $guard) => $this->deletePermissions(array_keys($this->permissions), $guard));

            Schema::dropIfExists('divisions');

            DB::table('audits')->where('auditable_type', 'division')->delete();
        });
    }

    public function down(): void
    {
        DB::transaction(function (): void {
            collect($this->guards)
                ->each(function (string $guard): void {
                    $permissions = Arr::except($this->permissions, keys: DB::table('permissions')
                        ->where('guard_name', $guard)
                        ->pluck('name')
                        ->all());

                    $this->createPermissions($permissions, $guard);
                });

            Schema::create('divisions', function (Blueprint $table) {
                $table->uuid('id')->primary();

                $table->string('name')->unique();
                $table->longText('description')->nullable();
                $table->string('code')->unique();

                $table->foreignUuid('created_by_id')->nullable()->constrained('users');
                $table->foreignUuid('last_updated_by_id')->nullable()->constrained('users');

                $table->boolean('is_default')->default(false);

                $table->timestamps();
                $table->softDeletes();
            });
        });
    }
};
