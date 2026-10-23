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

use AidingApp\Authorization\Models\Permission;
use AidingApp\Authorization\Models\PermissionGroup;
use AidingApp\Contact\Models\Contact;
use AidingApp\Engagement\Models\EmailTemplate;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use App\Features\EmailTemplateNameUniquenessFeature;
use App\Features\ServiceRequestFileUploadTypeFeature;
use App\Models\SystemUser;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

//describe('2025_01_01_165527_tmp_data_do_a_thing', function () {
//    it('properly changed the data', function () {
//        isolatedMigration(
//            '2025_01_01_165527_tmp_data_do_a_thing',
//            function () {
//                // Setup data before migration
//
//                // Run the migration
//                $migrate = Artisan::call('migrate', ['--path' => 'app/database/migrations/2025_01_01_165527_tmp_data_do_a_thing.php']);
//                // Confirm migration ran successfully
//                expect($migrate)->toBe(Command::SUCCESS);
//
//                // Add any assertions to verify the migration's effects
//            }
//        );
//    });
//});

// TODO: Cleanup Task (restore-reclassified-assignments): delete this describe and the test within
describe('2026_10_06_150704_tmp_restore_reclassified_service_request_assignments', function (): void {
    it('restores soft-deleted assignments as inactive and leaves other assignments untouched', function (): void {
        isolatedMigration('2026_10_06_150704_tmp_restore_reclassified_service_request_assignments', function (): void {
            // Setup data before migration
            $serviceRequest = ServiceRequest::factory()->create();
            $user = User::factory()->create();

            $insertAssignment = function (string $status, ?CarbonInterface $deletedAt) use ($serviceRequest, $user): string {
                $id = (string) Str::uuid();

                DB::table('service_request_assignments')->insert([
                    'id' => $id,
                    'service_request_id' => $serviceRequest->getKey(),
                    'user_id' => $user->getKey(),
                    'assigned_at' => now(),
                    'status' => $status,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'deleted_at' => $deletedAt,
                ]);

                return $id;
            };

            $deletedAssignmentId = $insertAssignment('active', now());
            $activeAssignmentId = $insertAssignment('active', null);

            // Run the migration
            $migrate = Artisan::call('migrate', ['--path' => 'app-modules/service-management/database/migrations/2026_10_06_150704_tmp_restore_reclassified_service_request_assignments.php']);
            // Confirm migration ran successfully
            expect($migrate)->toBe(Command::SUCCESS);

            // Add any assertions to verify the migration's effects
            assertDatabaseHas('service_request_assignments', [
                'id' => $deletedAssignmentId,
                'deleted_at' => null,
                'status' => 'inactive',
            ]);

            assertDatabaseHas('service_request_assignments', [
                'id' => $activeAssignmentId,
                'deleted_at' => null,
                'status' => 'active',
            ]);
        });
    });
});

// TODO: Cleanup Task (service-request-file-upload-type): delete this describe and the test within
describe('2026_10_15_081432_tmp_backfill_service_request_media_upload_type', function (): void {
    it('backfills the upload type of files attached directly to service requests and activates the feature flag', function (): void {
        isolatedMigration('2026_10_15_081432_tmp_backfill_service_request_media_upload_type', function (): void {
            $submittedAt = now()->subDay();
            $serviceRequest = ServiceRequest::factory()->create(['created_at' => $submittedAt]);
            $contact = Contact::factory()->create();
            $user = User::factory()->create();
            $systemUser = SystemUser::factory()->create();

            $insertMedia = function (string $modelType, string $modelId, ?Model $createdBy, CarbonInterface $createdAt, array $customProperties = []): int {
                return DB::table('media')->insertGetId([
                    'model_type' => $modelType,
                    'model_id' => $modelId,
                    'uuid' => (string) Str::uuid(),
                    'collection_name' => 'uploads',
                    'name' => 'file',
                    'file_name' => 'file.png',
                    'disk' => 's3',
                    'size' => 1,
                    'manipulations' => '[]',
                    'custom_properties' => json_encode($customProperties),
                    'generated_conversions' => '[]',
                    'responsive_images' => '[]',
                    'created_by_type' => $createdBy?->getMorphClass(),
                    'created_by_id' => $createdBy?->getKey(),
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);
            };

            $contactMediaId = $insertMedia('service_request', $serviceRequest->getKey(), $contact, $submittedAt->copy()->addDays(5), ['custom_headers' => ['ContentType' => 'image/png']]);
            $userMediaId = $insertMedia('service_request', $serviceRequest->getKey(), $user, $submittedAt->copy()->addMinute());
            $submittedWithinWindowMediaId = $insertMedia('service_request', $serviceRequest->getKey(), null, $submittedAt->copy()->addMinutes(10));
            $submittedAfterWindowMediaId = $insertMedia('service_request', $serviceRequest->getKey(), null, $submittedAt->copy()->addMinutes(11));
            $systemUserWithinWindowMediaId = $insertMedia('service_request', $serviceRequest->getKey(), $systemUser, $submittedAt->copy()->addMinute());
            $systemUserAfterWindowMediaId = $insertMedia('service_request', $serviceRequest->getKey(), $systemUser, $submittedAt->copy()->addHour());
            $alreadyRecordedMediaId = $insertMedia('service_request', $serviceRequest->getKey(), $contact, $submittedAt, ['upload_type' => 'staff_upload']);
            $serviceRequestUpdateMediaId = $insertMedia('service_request_update', (string) Str::uuid(), $contact, $submittedAt);

            expect(ServiceRequestFileUploadTypeFeature::active())->toBeFalse();

            $migrate = Artisan::call('migrate', ['--path' => 'app-modules/service-management/database/migrations/2026_10_15_081432_tmp_backfill_service_request_media_upload_type.php']);
            expect($migrate)->toBe(Command::SUCCESS);

            $customProperties = fn (int $mediaId): array => json_decode(DB::table('media')->where('id', $mediaId)->value('custom_properties'), associative: true);

            expect($customProperties($contactMediaId))->toEqual(['upload_type' => 'request_submission', 'custom_headers' => ['ContentType' => 'image/png']])
                ->and($customProperties($userMediaId))->toEqual(['upload_type' => 'staff_upload'])
                ->and($customProperties($submittedWithinWindowMediaId))->toEqual(['upload_type' => 'request_submission'])
                ->and($customProperties($submittedAfterWindowMediaId))->toEqual(['upload_type' => 'staff_upload'])
                ->and($customProperties($systemUserWithinWindowMediaId))->toEqual(['upload_type' => 'request_submission'])
                ->and($customProperties($systemUserAfterWindowMediaId))->toEqual(['upload_type' => 'staff_upload'])
                ->and($customProperties($alreadyRecordedMediaId))->toEqual(['upload_type' => 'staff_upload'])
                ->and($customProperties($serviceRequestUpdateMediaId))->toEqual([])
                ->and(ServiceRequestFileUploadTypeFeature::active())->toBeTrue();
        });
    });
});

describe('2026_10_15_101455_delete_role_group_and_interaction_import_permissions', function (): void {
    it('deletes the retired Role Group and interaction.import permissions and their emptied groups without touching unrelated permissions', function (): void {
        isolatedMigration('2026_10_15_101455_delete_role_group_and_interaction_import_permissions', function (): void {
            $roleGroupPermissionNames = [
                'role_group.view-any',
                'role_group.create',
                'role_group.*.view',
                'role_group.*.update',
                'role_group.*.delete',
                'role_group.*.restore',
                'role_group.*.force-delete',
            ];

            $retiredPermissionNames = [...$roleGroupPermissionNames, 'interaction.import'];

            // Simulate the orphaned data left behind by the removed Role Group feature and the Interaction module cleanup that missed `interaction.import`.
            $roleGroupGroup = PermissionGroup::create(['name' => 'Role Group']);
            $interactionGroup = PermissionGroup::create(['name' => 'Interaction']);

            foreach (['web', 'api'] as $guard) {
                foreach ($roleGroupPermissionNames as $name) {
                    Permission::factory()->create([
                        'name' => $name,
                        'guard_name' => $guard,
                        'group_id' => $roleGroupGroup->getKey(),
                    ]);
                }

                Permission::factory()->create([
                    'name' => 'interaction.import',
                    'guard_name' => $guard,
                    'group_id' => $interactionGroup->getKey(),
                ]);
            }

            // Run the migration
            $migrate = Artisan::call('migrate', ['--path' => 'database/migrations/2026_10_15_101455_delete_role_group_and_interaction_import_permissions.php']);
            // Confirm migration ran successfully
            expect($migrate)->toBe(Command::SUCCESS);

            foreach (['web', 'api'] as $guard) {
                foreach ($retiredPermissionNames as $name) {
                    assertDatabaseMissing('permissions', [
                        'name' => $name,
                        'guard_name' => $guard,
                    ]);
                }
            }

            assertDatabaseMissing('permission_groups', ['name' => 'Role Group']);
            assertDatabaseMissing('permission_groups', ['name' => 'Interaction']);

            // Unrelated permissions seeded by earlier migrations must survive the cleanup.
            assertDatabaseHas('permissions', ['name' => 'role.view-any', 'guard_name' => 'web']);
            assertDatabaseHas('permission_groups', ['name' => 'Role']);

            // Confirm the rollback restores the deleted permissions without duplicating any.
            $rollback = Artisan::call('migrate:rollback', ['--path' => 'database/migrations/2026_10_15_101455_delete_role_group_and_interaction_import_permissions.php']);
            expect($rollback)->toBe(Command::SUCCESS);

            foreach (['web', 'api'] as $guard) {
                foreach ($retiredPermissionNames as $name) {
                    expect(Permission::where('name', $name)->where('guard_name', $guard)->count())->toBe(1);
                }
            }
        });
    });
});

// TODO: Cleanup Task (email-template-name-uniqueness): delete this describe and the tests within
describe('2026_10_23_083558_convert_email_templates_name_to_citext_and_enforce_unique', function () {
    it('rewrites case-insensitive duplicate names with a numeric suffix, keeps the oldest and activates the feature flag', function () {
        isolatedMigration('2026_10_23_083558_convert_email_templates_name_to_citext_and_enforce_unique', function () {
            $first = EmailTemplate::factory()->create(['name' => 'Welcome Email', 'created_at' => now()->subMinutes(3)]);
            $second = EmailTemplate::factory()->create(['name' => 'welcome email', 'created_at' => now()->subMinutes(2)]);
            $third = EmailTemplate::factory()->create(['name' => 'WELCOME EMAIL', 'created_at' => now()->subMinute()]);
            $unique = EmailTemplate::factory()->create(['name' => 'Reminder Email', 'created_at' => now()->subMinutes(4)]);

            expect(EmailTemplateNameUniquenessFeature::active())->toBeFalse();

            $migrate = Artisan::call('migrate', ['--path' => 'app-modules/engagement/database/migrations/2026_10_23_083558_convert_email_templates_name_to_citext_and_enforce_unique.php']);
            expect($migrate)->toBe(Command::SUCCESS);

            expect($first->refresh()->name)->toBe('Welcome Email')
                ->and($second->refresh()->name)->toBe('welcome email-2')
                ->and($third->refresh()->name)->toBe('WELCOME EMAIL-3')
                ->and($unique->refresh()->name)->toBe('Reminder Email')
                ->and(EmailTemplateNameUniquenessFeature::active())->toBeTrue();
        });
    });

    it('leaves soft-deleted duplicates untouched', function () {
        isolatedMigration('2026_10_23_083558_convert_email_templates_name_to_citext_and_enforce_unique', function () {
            $kept = EmailTemplate::factory()->create(['name' => 'Welcome Email', 'created_at' => now()->subMinutes(2)]);
            $trashed = EmailTemplate::factory()->create(['name' => 'welcome email', 'created_at' => now()->subMinute()]);
            $trashed->delete();

            $migrate = Artisan::call('migrate', ['--path' => 'app-modules/engagement/database/migrations/2026_10_23_083558_convert_email_templates_name_to_citext_and_enforce_unique.php']);
            expect($migrate)->toBe(Command::SUCCESS);

            expect($kept->refresh()->name)->toBe('Welcome Email')
                ->and($trashed->refresh()->name)->toBe('welcome email');
        });
    });

    it('strips the numeric suffix on rollback once the original name is no longer taken', function () {
        isolatedMigration('2026_10_23_083558_convert_email_templates_name_to_citext_and_enforce_unique', function () {
            $first = EmailTemplate::factory()->create(['name' => 'Welcome Email', 'created_at' => now()->subMinutes(2)]);
            $second = EmailTemplate::factory()->create(['name' => 'welcome email', 'created_at' => now()->subMinute()]);

            $migrate = Artisan::call('migrate', ['--path' => 'app-modules/engagement/database/migrations/2026_10_23_083558_convert_email_templates_name_to_citext_and_enforce_unique.php']);
            expect($migrate)->toBe(Command::SUCCESS);

            expect($second->refresh()->name)->toBe('welcome email-2');

            $first->delete();

            $rollback = Artisan::call('migrate:rollback', ['--path' => 'app-modules/engagement/database/migrations/2026_10_23_083558_convert_email_templates_name_to_citext_and_enforce_unique.php']);
            expect($rollback)->toBe(Command::SUCCESS);

            expect($second->refresh()->name)->toBe('welcome email')
                ->and(EmailTemplateNameUniquenessFeature::active())->toBeFalse();
        });
    });
});
