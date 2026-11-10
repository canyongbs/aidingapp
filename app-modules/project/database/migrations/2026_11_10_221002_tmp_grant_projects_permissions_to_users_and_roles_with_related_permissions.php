<?php

use AidingApp\Authorization\Models\Role;
use App\Features\ProjectPermissionConsolidationFeature;
use App\Models\SystemUser;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    public function up(): void
    {
        DB::transaction(function (): void {
            foreach ($this->mappings() as $relatedPermission => $permission) {
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

            ProjectPermissionConsolidationFeature::activate();
        });
    }

    public function down(): void
    {
        // Grants that existed before this migration cannot be told apart from the ones it added, so that is irreversible
        ProjectPermissionConsolidationFeature::deactivate();
    }

    /**
     * Both groups support every action, so each related permission maps to the same action in the consolidated group.
     *
     * @return array<string, string>
     */
    private function mappings(): array
    {
        $actions = ['view-any', 'create', '*.view', '*.update', '*.delete', '*.restore', '*.force-delete'];

        $mappings = [];

        foreach (['project', 'pipeline'] as $prefix) {
            foreach ($actions as $action) {
                $mappings["{$prefix}.{$action}"] = "projects.{$action}";
            }
        }

        return $mappings;
    }
};
