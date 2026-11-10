<?php

use AidingApp\Authorization\Models\Role;
use App\Features\AssetPermissionConsolidationFeature;
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

            AssetPermissionConsolidationFeature::activate();
        });
    }

    public function down(): void
    {
        // Grants that existed before this migration cannot be told apart from the ones it added, so that is irreversible
        AssetPermissionConsolidationFeature::deactivate();
    }

    /**
     * Only actions the related feature supports are carried over, so the consolidation never grants a capability a feature does not have.
     *
     * @return array<string, string>
     */
    private function mappings(): array
    {
        $allActions = ['view-any', 'create', '*.view', '*.update', '*.delete', '*.restore', '*.force-delete'];

        $supportedActions = [
            'asset' => $allActions,
            'asset_location' => $allActions,
            'asset_status' => $allActions,
            'asset_type' => $allActions,
            'maintenance_provider' => $allActions,
            'maintenance_activity' => ['view-any', 'create', '*.view', '*.update'],
            'asset_check_in' => ['view-any', '*.view'],
            'asset_check_out' => ['view-any', '*.view'],
        ];

        $mappings = [];

        foreach ($supportedActions as $prefix => $actions) {
            foreach ($actions as $action) {
                $mappings["{$prefix}.{$action}"] = "assets.{$action}";
            }
        }

        return $mappings;
    }
};
