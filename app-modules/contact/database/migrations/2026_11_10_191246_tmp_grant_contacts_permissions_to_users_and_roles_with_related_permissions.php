<?php

use AidingApp\Authorization\Models\Role;
use App\Features\ContactPermissionConsolidationFeature;
use App\Models\SystemUser;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    public function up(): void
    {
        DB::transaction(function (): void {
            foreach ($this->mappings() as $relatedPermission => $permissions) {
                User::query()
                    ->withTrashed()
                    ->whereHas('permissions', fn (Builder $query) => $query->where('name', $relatedPermission))
                    ->each(fn (User $user) => $user->givePermissionTo($permissions));

                SystemUser::query()
                    ->withTrashed()
                    ->whereHas('permissions', fn (Builder $query) => $query->where('name', $relatedPermission))
                    ->each(fn (SystemUser $systemUser) => $systemUser->givePermissionTo($permissions));

                Role::query()
                    ->whereHas('permissions', fn (Builder $query) => $query->where('name', $relatedPermission))
                    ->each(fn (Role $role) => $role->givePermissionTo($permissions));
            }

            ContactPermissionConsolidationFeature::activate();
        });
    }

    public function down(): void
    {
        // Grants that existed before this migration cannot be told apart from the ones it added, so that is irreversible
        ContactPermissionConsolidationFeature::deactivate();
    }

    /**
     * @return array<string, array<string>>
     */
    private function mappings(): array
    {
        $actions = ['view-any', 'create', '*.view', '*.update', '*.delete', '*.restore', '*.force-delete'];

        $mappings = ['contact.import' => ['contacts.import']];

        foreach (['contact', 'engagement_file', 'engagement_response', 'engagement'] as $prefix) {
            foreach ($actions as $action) {
                $mappings["{$prefix}.{$action}"] = ["contacts.{$action}"];
            }
        }

        // Sending an engagement now requires being able to view and update the contact
        $mappings['engagement.create'] = ['contacts.view-any', 'contacts.*.view', 'contacts.*.update'];

        return $mappings;
    }
};
