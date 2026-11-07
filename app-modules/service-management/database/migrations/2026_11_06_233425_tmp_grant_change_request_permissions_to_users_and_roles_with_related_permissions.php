<?php

use App\Features\ChangeRequestPermissionCompressionFeature;
use AidingApp\Authorization\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class () extends Migration {
    /**
     * @var array<string>
     */
    private array $actions = [
        'change_request_status.view-any',
        'change_request_status.create',
        'change_request_status.*.view',
        'change_request_status.*.update',
        'change_request_status.*.delete',
        'change_request_status.*.restore',
        'change_request_status.*.force-delete',
        'change_request_type.view-any',
        'change_request_type.create',
        'change_request_type.*.view',
        'change_request_type.*.update',
        'change_request_type.*.delete',
        'change_request_type.*.restore',
        'change_request_type.*.force-delete',
    ];

    public function up(): void
    {
        DB::transaction(function (): void {
            foreach ($this->actions as $relatedPermission) {
                $permission = 'change_request.' . Str::after($relatedPermission, '.');

                User::query()
                    ->whereHas('permissions', fn (Builder $query) => $query->where('name', $relatedPermission))
                    ->each(fn (User $user) => $user->givePermissionTo($permission));

                Role::query()
                    ->whereHas('permissions', fn (Builder $query) => $query->where('name', $relatedPermission))
                    ->each(fn (Role $role) => $role->givePermissionTo($permission));
            }

            ChangeRequestPermissionCompressionFeature::activate();
        });
    }

    public function down(): void
    {
        // Grants that existed before this migration cannot be told apart from the ones it added, so that is irreversible
        ChangeRequestPermissionCompressionFeature::deactivate();
    }
};
