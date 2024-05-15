<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

    Aiding App™ is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Aiding App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    <https://www.canyongbs.com> or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AidingApp\Authorization\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use AidingApp\Authorization\Models\Role;
use AidingApp\Authorization\AuthorizationRoleRegistry;
use Spatie\Multitenancy\Commands\Concerns\TenantAware;

class SetupRoles extends Command
{
    use TenantAware;

    protected $signature = 'roles:setup {--tenant=*}';

    protected $description = 'This command will create all of the roles defined in the roles config directories.';

    public function handle(): int
    {
        $this->line('Creating roles...');

        $roleRegistry = resolve(AuthorizationRoleRegistry::class);

        $rolePermissions = [];

        foreach ($roleRegistry->getWebRoles() as $roleName => $permissionIds) {
            $roleId = Role::create([
                'name' => $roleName,
                'guard_name' => 'web',
            ])->id;

            $rolePermissions = [
                ...$rolePermissions,
                ...array_map(fn (string $permissionId) => [
                    'permission_id' => $permissionId,
                    'role_id' => $roleId,
                ], $permissionIds),
            ];
        }

        foreach ($roleRegistry->getApiRoles() as $roleName => $permissionIds) {
            $roleId = Role::create([
                'name' => $roleName,
                'guard_name' => 'api',
            ])->id;

            $rolePermissions = [
                ...$rolePermissions,
                ...array_map(fn (string $permissionId) => [
                    'permission_id' => $permissionId,
                    'role_id' => $roleId,
                ], $permissionIds),
            ];
        }

        DB::table('role_has_permissions')
            ->insert($rolePermissions);

        $this->info('Roles created successfully!');

        return static::SUCCESS;
    }
}
