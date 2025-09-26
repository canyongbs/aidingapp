<?php

use AidingApp\ServiceManagement\Enums\ServiceRequestAssignmentStatus;
use AidingApp\ServiceManagement\Enums\ServiceRequestUpdateDirection;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestPriority;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use AidingApp\ServiceManagement\Models\ServiceRequestUpdate;
use AidingApp\Team\Models\Team;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

describe('2025_09_26_183417_tmp_data_backfill_service_request_update_created_by', function () {
    it('can set createdBy for inbound direction', function () {
        isolatedMigration(
            '2025_09_26_183417_tmp_data_backfill_service_request_update_created_by',
            function () {
                $serviceRequestUpdate = ServiceRequestUpdate::factory()
                    ->create(['direction' => ServiceRequestUpdateDirection::Inbound]);

                expect($serviceRequestUpdate->createdBy)->toBeNull();

                $migrate = Artisan::call('migrate', ['--path' => 'app-modules/service-management/database/migrations/2025_09_26_183417_tmp_data_backfill_service_request_update_created_by.php']);

                expect($migrate)->toBe(Command::SUCCESS);

                expect($serviceRequestUpdate->refresh()->createdBy->is($serviceRequestUpdate->serviceRequest->respondent))->toBeTrue();
            }
        );
    });

    it('can set createdBy for a outbound direction when there is an assigned User', function () {
        isolatedMigration(
            '2025_09_26_183417_tmp_data_backfill_service_request_update_created_by',
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

                expect($serviceRequestUpdate->createdBy)->toBeNull();

                $migrate = Artisan::call('migrate', ['--path' => 'app-modules/service-management/database/migrations/2025_09_26_183417_tmp_data_backfill_service_request_update_created_by.php']);

                expect($migrate)->toBe(Command::SUCCESS);

                expect($serviceRequestUpdate->refresh()->createdBy->is($user))->toBeTrue();
            }
        );
    });

    it('can set createdBy for outbound direction when there is no assigned user', function () {})->todo();
    it('can set createdBy for outbound direction when there is no assigned user AND no manager', function () {})->todo();
});
