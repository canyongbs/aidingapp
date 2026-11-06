<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Aiding App® is licensed under the Elastic License 2.0. For more details,
    see <https://github.com/canyongbs/aidingapp/blob/main/LICENSE.>

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Aiding App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    <https://www.canyongbs.com> or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

use App\Features\LicensesPermissionsConsolidationFeature;
use Database\Migrations\Concerns\CanModifyPermissions;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

return new class () extends Migration {
    use CanModifyPermissions;

    /**
     * @var array<string>
     */
    private array $operations = [
        'view-any',
        'create',
        '*.view',
        '*.update',
        '*.delete',
        '*.restore',
        '*.force-delete',
    ];

    /**
     * The permission prefixes being consolidated, keyed by prefix with their permission group as the value.
     *
     * @var array<string, string>
     */
    private array $legacyGroups = [
        'product' => 'Product',
        'product_license' => 'Product License',
    ];

    private string $prefix = 'licenses';

    private string $group = 'Licenses';

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
            collect($this->guards)->each(function (string $guard): void {
                $this->createPermissions($this->permissionsFor($this->prefix, $this->group), $guard);

                foreach ($this->operations as $operation) {
                    $targetId = $this->permissionId("{$this->prefix}.{$operation}", $guard);

                    foreach (array_keys($this->legacyGroups) as $legacyPrefix) {
                        $this->copyGrants($this->permissionId("{$legacyPrefix}.{$operation}", $guard), $targetId);
                    }
                }

                foreach ($this->legacyGroups as $legacyPrefix => $legacyGroup) {
                    $this->deletePermissions(array_keys($this->permissionsFor($legacyPrefix, $legacyGroup)), $guard);
                }
            });

            // TODO: Cleanup Task (licenses-permissions-consolidation): remove this activate() call and the deactivate() call in down() once LicensesPermissionsConsolidationFeature is removed
            LicensesPermissionsConsolidationFeature::activate();

            // The policies switch to the new permission names as soon as the flag is active, so the cached permission list must be rebuilt.
            DB::afterCommit(fn () => app(PermissionRegistrar::class)->forgetCachedPermissions());
        });
    }

    public function down(): void
    {
        DB::transaction(function (): void {
            LicensesPermissionsConsolidationFeature::deactivate();

            collect($this->guards)->each(function (string $guard): void {
                foreach ($this->legacyGroups as $legacyPrefix => $legacyGroup) {
                    $this->createPermissions($this->permissionsFor($legacyPrefix, $legacyGroup), $guard);
                }

                foreach ($this->operations as $operation) {
                    $sourceId = $this->permissionId("{$this->prefix}.{$operation}", $guard);

                    foreach (array_keys($this->legacyGroups) as $legacyPrefix) {
                        $this->copyGrants($sourceId, $this->permissionId("{$legacyPrefix}.{$operation}", $guard));
                    }
                }

                $this->deletePermissions(array_keys($this->permissionsFor($this->prefix, $this->group)), $guard);
            });

            DB::afterCommit(fn () => app(PermissionRegistrar::class)->forgetCachedPermissions());
        });
    }

    /**
     * @return array<string, string>
     */
    private function permissionsFor(string $prefix, string $group): array
    {
        return collect($this->operations)
            ->mapWithKeys(fn (string $operation): array => ["{$prefix}.{$operation}" => $group])
            ->all();
    }

    private function permissionId(string $name, string $guard): ?string
    {
        return DB::table('permissions')
            ->where('guard_name', $guard)
            ->where('name', $name)
            ->value('id');
    }

    /**
     * Grants the target permission to every role and model that holds the source permission, skipping existing grants.
     */
    private function copyGrants(?string $sourceId, ?string $targetId): void
    {
        if (blank($sourceId) || blank($targetId)) {
            return;
        }

        DB::table('role_has_permissions')->insertUsing(
            ['permission_id', 'role_id'],
            DB::table('role_has_permissions as source')
                ->selectRaw('cast(? as uuid), source.role_id', [$targetId])
                ->where('source.permission_id', $sourceId)
                ->whereNotExists(fn (Builder $query) => $query
                    ->from('role_has_permissions as target')
                    ->where('target.permission_id', $targetId)
                    ->whereColumn('target.role_id', 'source.role_id')),
        );

        DB::table('model_has_permissions')->insertUsing(
            ['permission_id', 'model_type', 'model_id'],
            DB::table('model_has_permissions as source')
                ->selectRaw('cast(? as uuid), source.model_type, source.model_id', [$targetId])
                ->where('source.permission_id', $sourceId)
                ->whereNotExists(fn (Builder $query) => $query
                    ->from('model_has_permissions as target')
                    ->where('target.permission_id', $targetId)
                    ->whereColumn('target.model_type', 'source.model_type')
                    ->whereColumn('target.model_id', 'source.model_id')),
        );
    }
};
