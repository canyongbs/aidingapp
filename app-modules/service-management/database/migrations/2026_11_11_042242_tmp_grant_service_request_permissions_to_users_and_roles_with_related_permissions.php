<?php

use AidingApp\Authorization\Models\Role;
use App\Features\ServiceRequestPermissionCompressionFeature;
use App\Models\SystemUser;
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
        'service_request_assignment.view-any',
        'service_request_assignment.create',
        'service_request_assignment.*.view',
        'service_request_assignment.*.update',
        'service_request_assignment.*.delete',
        'service_request_assignment.*.restore',
        'service_request_assignment.*.force-delete',
        'service_request_history.view-any',
        'service_request_history.create',
        'service_request_history.*.view',
        'service_request_history.*.update',
        'service_request_history.*.delete',
        'service_request_history.*.restore',
        'service_request_history.*.force-delete',
        'service_request_priority.view-any',
        'service_request_priority.create',
        'service_request_priority.*.view',
        'service_request_priority.*.update',
        'service_request_priority.*.delete',
        'service_request_priority.*.restore',
        'service_request_priority.*.force-delete',
        'service_request_update.view-any',
        'service_request_update.create',
        'service_request_update.*.view',
        'service_request_update.*.update',
        'service_request_update.*.delete',
        'service_request_update.*.restore',
        'service_request_update.*.force-delete',
    ];

    public function up(): void
    {
        DB::transaction(function (): void {
            foreach ($this->actions as $relatedPermission) {
                $permission = 'service_request.' . Str::after($relatedPermission, '.');

                User::query()
                    ->withTrashed()
                    ->whereHas('permissions', fn (Builder $query) => $query->where('name', $relatedPermission))
                    ->each(fn (User $user) => $user->givePermissionTo($permission));

                SystemUser::query()
                    ->withTrashed()
                    ->whereHas('permissions', fn (Builder $query) => $query->where('name', $relatedPermission))
                    ->each(fn (SystemUser $systemUser) => $systemUser->givePermissionTo($permission));

                Role::query()
                    ->whereHas('permissions', fn (Builder $query) => $query->where('name', $relatedPermission))
                    ->each(fn (Role $role) => $role->givePermissionTo($permission));
            }

            ServiceRequestPermissionCompressionFeature::activate();
        });
    }

    public function down(): void
    {
        // Grants that existed before this migration cannot be told apart from the ones it added, so that is irreversible
        ServiceRequestPermissionCompressionFeature::deactivate();
    }
};
