<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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

use AidingApp\Authorization\Models\Role;
use AidingApp\ServiceManagement\Enums\ServiceRequestAssignmentStatus;
use AidingApp\ServiceManagement\Enums\ServiceRequestUpdateDirection;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestPriority;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use AidingApp\ServiceManagement\Models\ServiceRequestUpdate;
use AidingApp\Team\Models\Team;
use App\Models\User;
use Database\Seeders\NewTenantSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

use function Pest\Laravel\seed;

describe('2025_09_26_165527_add_created_by_columns_to_service_request_updates_table', function () {
    it('can set createdBy for inbound direction', function () {
        isolatedMigration(
            '2025_09_26_165527_add_created_by_columns_to_service_request_updates_table',
            function () {
                $serviceRequestUpdate = ServiceRequestUpdate::factory()
                    ->create(['direction' => ServiceRequestUpdateDirection::Inbound]);

                $migrate = Artisan::call('migrate', ['--path' => 'app-modules/service-management/database/migrations/2025_09_26_165527_add_created_by_columns_to_service_request_updates_table.php']);

                expect($migrate)->toBe(Command::SUCCESS);

                expect($serviceRequestUpdate->refresh()->createdBy->is($serviceRequestUpdate->serviceRequest->respondent))->toBeTrue();
            }
        );
    });

    it('can set createdBy for a outbound direction when there is an assigned User', function () {
        isolatedMigration(
            '2025_09_26_165527_add_created_by_columns_to_service_request_updates_table',
            function () {
                $user = User::factory()->create();

                $team = Team::factory()->create();

                $user->team()->associate($team);
                $user->save();

                $serviceRequest = ServiceRequest::factory()
                    ->for(
                        ServiceRequestPriority::factory()
                            ->for(
                                ServiceRequestType::factory()
                                    ->hasAttached(
                                        factory: $team,
                                        relationship: 'managers'
                                    ),
                                'type',
                            ),
                        'priority'
                    )
                    ->create();

                $serviceRequest->assignments()->create([
                    'user_id' => $user->id,
                    'assigned_by_id' => null,
                    'assigned_at' => now(),
                    'status' => ServiceRequestAssignmentStatus::Active,
                ]);

                $serviceRequestUpdate = ServiceRequestUpdate::factory()
                    ->for($serviceRequest, 'serviceRequest')
                    ->create(['direction' => ServiceRequestUpdateDirection::Outbound]);

                $migrate = Artisan::call('migrate', ['--path' => 'app-modules/service-management/database/migrations/2025_09_26_165527_add_created_by_columns_to_service_request_updates_table.php']);

                expect($migrate)->toBe(Command::SUCCESS);

                expect($serviceRequestUpdate->refresh()->createdBy->is($user))->toBeTrue();
            }
        );
    });

    it('can set createdBy for outbound direction when there is no assigned user', function () {
        isolatedMigration(
            '2025_09_26_165527_add_created_by_columns_to_service_request_updates_table',
            function () {
                $user = User::factory()->create();

                $team = Team::factory()->create();

                $user->team()->associate($team);
                $user->save();

                $serviceRequest = ServiceRequest::factory()
                    ->for(
                        ServiceRequestPriority::factory()
                            ->for(
                                ServiceRequestType::factory()
                                    ->hasAttached(
                                        factory: $team,
                                        relationship: 'managers'
                                    ),
                                'type',
                            ),
                        'priority'
                    )
                    ->create();

                $serviceRequestUpdate = ServiceRequestUpdate::factory()
                    ->for($serviceRequest, 'serviceRequest')
                    ->create(['direction' => ServiceRequestUpdateDirection::Outbound]);

                $migrate = Artisan::call('migrate', ['--path' => 'app-modules/service-management/database/migrations/2025_09_26_165527_add_created_by_columns_to_service_request_updates_table.php']);

                expect($migrate)->toBe(Command::SUCCESS);

                expect($serviceRequestUpdate->refresh()->createdBy->is($user))->toBeTrue();
            }
        );
    });

    it('can set createdBy for outbound direction when there is no assigned user AND no manager', function () {
        isolatedMigration(
            '2025_09_26_165527_add_created_by_columns_to_service_request_updates_table',
            function () {
                seed(NewTenantSeeder::class);

                $user = User::factory()->create();

                $superAdminRoles = Role::superAdmin()->get();

                $user->assignRole($superAdminRoles);

                $serviceRequest = ServiceRequest::factory()
                    ->create();

                $serviceRequestUpdate = ServiceRequestUpdate::factory()
                    ->for($serviceRequest, 'serviceRequest')
                    ->create(['direction' => ServiceRequestUpdateDirection::Outbound]);

                $migrate = Artisan::call('migrate', ['--path' => 'app-modules/service-management/database/migrations/2025_09_26_165527_add_created_by_columns_to_service_request_updates_table.php']);

                expect($migrate)->toBe(Command::SUCCESS);

                expect($serviceRequestUpdate->refresh()->createdBy->is($user))->toBeTrue();
            }
        );
    });
});
