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
use AidingApp\LicenseManagement\Models\Product;
use AidingApp\Project\Models\Pipeline;
use AidingApp\Project\Models\PipelineStage;
use AidingApp\Project\Models\Project;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use App\Features\EmailTemplateNameUniquenessFeature;
use App\Features\MaintenanceProviderNameUniquenessFeature;
use App\Features\PipelineNameUniquenessFeature;
use App\Features\ProductNameUniquenessFeature;
use App\Features\ServiceMonitoringTargetNameUniquenessFeature;
use App\Features\ServiceRequestFileUploadTypeFeature;
use App\Features\SystemUserNameUniquenessFeature;
use App\Models\SystemUser;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\UniqueConstraintViolationException;
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
describe('2026_10_23_083558_convert_email_templates_name_to_citext_and_enforce_unique', function (): void {
    it('rewrites case-insensitive duplicate names with a numeric suffix, keeps the oldest and activates the feature flag', function (): void {
        isolatedMigration('2026_10_23_083558_convert_email_templates_name_to_citext_and_enforce_unique', function (): void {
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

    it('leaves soft-deleted duplicates untouched', function (): void {
        isolatedMigration('2026_10_23_083558_convert_email_templates_name_to_citext_and_enforce_unique', function (): void {
            $kept = EmailTemplate::factory()->create(['name' => 'Welcome Email', 'created_at' => now()->subMinutes(2)]);
            $trashed = EmailTemplate::factory()->create(['name' => 'welcome email', 'created_at' => now()->subMinute()]);
            $trashed->delete();

            $migrate = Artisan::call('migrate', ['--path' => 'app-modules/engagement/database/migrations/2026_10_23_083558_convert_email_templates_name_to_citext_and_enforce_unique.php']);
            expect($migrate)->toBe(Command::SUCCESS);

            expect($kept->refresh()->name)->toBe('Welcome Email')
                ->and($trashed->refresh()->name)->toBe('welcome email');
        });
    });

    it('strips the numeric suffix on rollback once the original name is no longer taken', function (): void {
        isolatedMigration('2026_10_23_083558_convert_email_templates_name_to_citext_and_enforce_unique', function (): void {
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

// TODO: Cleanup Task (product-name-uniqueness): delete this describe and the tests within
describe('2026_10_23_134410_convert_products_name_to_citext_and_enforce_unique', function (): void {
    it('rewrites case-insensitive duplicate names with a numeric suffix, keeps the oldest and activates the feature flag', function (): void {
        isolatedMigration('2026_10_23_134410_convert_products_name_to_citext_and_enforce_unique', function (): void {
            $first = Product::factory()->create(['name' => 'Aiding App', 'created_at' => now()->subMinutes(3)]);
            $second = Product::factory()->create(['name' => 'aiding app', 'created_at' => now()->subMinutes(2)]);
            $third = Product::factory()->create(['name' => 'AIDING APP', 'created_at' => now()->subMinute()]);
            $unique = Product::factory()->create(['name' => 'Advising App', 'created_at' => now()->subMinutes(4)]);

            expect(ProductNameUniquenessFeature::active())->toBeFalse();

            $migrate = Artisan::call('migrate', ['--path' => 'app-modules/license-management/database/migrations/2026_10_23_134410_convert_products_name_to_citext_and_enforce_unique.php']);
            expect($migrate)->toBe(Command::SUCCESS);

            expect($first->refresh()->name)->toBe('Aiding App')
                ->and($second->refresh()->name)->toBe('aiding app-2')
                ->and($third->refresh()->name)->toBe('AIDING APP-3')
                ->and($unique->refresh()->name)->toBe('Advising App')
                ->and(ProductNameUniquenessFeature::active())->toBeTrue();
        });
    });

    it('leaves soft-deleted duplicates untouched', function (): void {
        isolatedMigration('2026_10_23_134410_convert_products_name_to_citext_and_enforce_unique', function (): void {
            $kept = Product::factory()->create(['name' => 'Aiding App', 'created_at' => now()->subMinutes(2)]);
            $trashed = Product::factory()->create(['name' => 'aiding app', 'created_at' => now()->subMinute()]);
            $trashed->delete();

            $migrate = Artisan::call('migrate', ['--path' => 'app-modules/license-management/database/migrations/2026_10_23_134410_convert_products_name_to_citext_and_enforce_unique.php']);
            expect($migrate)->toBe(Command::SUCCESS);

            expect($kept->refresh()->name)->toBe('Aiding App')
                ->and($trashed->refresh()->name)->toBe('aiding app');
        });
    });

    it('strips the numeric suffix on rollback once the original name is no longer taken', function (): void {
        isolatedMigration('2026_10_23_134410_convert_products_name_to_citext_and_enforce_unique', function (): void {
            $first = Product::factory()->create(['name' => 'Aiding App', 'created_at' => now()->subMinutes(2)]);
            $second = Product::factory()->create(['name' => 'aiding app', 'created_at' => now()->subMinute()]);

            $migrate = Artisan::call('migrate', ['--path' => 'app-modules/license-management/database/migrations/2026_10_23_134410_convert_products_name_to_citext_and_enforce_unique.php']);
            expect($migrate)->toBe(Command::SUCCESS);

            expect($second->refresh()->name)->toBe('aiding app-2');

            $first->delete();

            $rollback = Artisan::call('migrate:rollback', ['--path' => 'app-modules/license-management/database/migrations/2026_10_23_134410_convert_products_name_to_citext_and_enforce_unique.php']);
            expect($rollback)->toBe(Command::SUCCESS);

            expect($second->refresh()->name)->toBe('aiding app')
                ->and(ProductNameUniquenessFeature::active())->toBeFalse();
        });
    });
});

// TODO: Cleanup Task (system-user-name-uniqueness): delete this describe and the tests within
describe('2026_11_02_120000_convert_system_users_name_to_citext_and_enforce_unique', function (): void {
    $migrationPath = 'database/migrations/2026_11_02_120000_convert_system_users_name_to_citext_and_enforce_unique.php';

    it('rewrites case-insensitive duplicate names with a numeric suffix, keeps the oldest and activates the feature flag', function () use ($migrationPath): void {
        isolatedMigration('2026_11_02_120000_convert_system_users_name_to_citext_and_enforce_unique', function () use ($migrationPath): void {
            $first = SystemUser::factory()->create(['name' => 'Integration Service', 'created_at' => now()->subMinutes(3)]);
            $second = SystemUser::factory()->create(['name' => 'integration service', 'created_at' => now()->subMinutes(2)]);
            $third = SystemUser::factory()->create(['name' => 'INTEGRATION SERVICE', 'created_at' => now()->subMinute()]);
            $unique = SystemUser::factory()->create(['name' => 'Reporting Service', 'created_at' => now()->subMinutes(4)]);

            expect(SystemUserNameUniquenessFeature::active())->toBeFalse();

            $migrate = Artisan::call('migrate', ['--path' => $migrationPath]);
            expect($migrate)->toBe(Command::SUCCESS);

            expect($first->refresh()->name)->toBe('Integration Service')
                ->and($second->refresh()->name)->toBe('integration service-2')
                ->and($third->refresh()->name)->toBe('INTEGRATION SERVICE-3')
                ->and($unique->refresh()->name)->toBe('Reporting Service')
                ->and(SystemUserNameUniquenessFeature::active())->toBeTrue();
        });
    });

    it('leaves soft-deleted duplicates untouched', function () use ($migrationPath): void {
        isolatedMigration('2026_11_02_120000_convert_system_users_name_to_citext_and_enforce_unique', function () use ($migrationPath): void {
            $kept = SystemUser::factory()->create(['name' => 'Integration Service', 'created_at' => now()->subMinutes(2)]);
            $trashed = SystemUser::factory()->create(['name' => 'integration service', 'created_at' => now()->subMinute()]);
            $trashed->delete();

            $migrate = Artisan::call('migrate', ['--path' => $migrationPath]);
            expect($migrate)->toBe(Command::SUCCESS);

            expect($kept->refresh()->name)->toBe('Integration Service')
                ->and($trashed->refresh()->name)->toBe('integration service');
        });
    });

    it('enforces case-insensitive uniqueness among non-trashed system users at the database level', function () use ($migrationPath): void {
        isolatedMigration('2026_11_02_120000_convert_system_users_name_to_citext_and_enforce_unique', function () use ($migrationPath): void {
            $migrate = Artisan::call('migrate', ['--path' => $migrationPath]);
            expect($migrate)->toBe(Command::SUCCESS);

            SystemUser::factory()->create(['name' => 'Integration Service']);

            expect(fn () => SystemUser::factory()->create(['name' => 'INTEGRATION SERVICE']))
                ->toThrow(UniqueConstraintViolationException::class);
        });
    });

    it('allows a non-trashed system user to reuse a soft-deleted name once the migration has run', function () use ($migrationPath): void {
        isolatedMigration('2026_11_02_120000_convert_system_users_name_to_citext_and_enforce_unique', function () use ($migrationPath): void {
            $migrate = Artisan::call('migrate', ['--path' => $migrationPath]);
            expect($migrate)->toBe(Command::SUCCESS);

            SystemUser::factory()->create(['name' => 'Integration Service'])->delete();

            $live = SystemUser::factory()->create(['name' => 'Integration Service']);

            expect($live->refresh()->name)->toBe('Integration Service');
        });
    });

    it('strips the numeric suffix on rollback once the original name is no longer taken', function () use ($migrationPath): void {
        isolatedMigration('2026_11_02_120000_convert_system_users_name_to_citext_and_enforce_unique', function () use ($migrationPath): void {
            $first = SystemUser::factory()->create(['name' => 'Integration Service', 'created_at' => now()->subMinutes(2)]);
            $second = SystemUser::factory()->create(['name' => 'integration service', 'created_at' => now()->subMinute()]);

            $migrate = Artisan::call('migrate', ['--path' => $migrationPath]);
            expect($migrate)->toBe(Command::SUCCESS);

            expect($second->refresh()->name)->toBe('integration service-2');

            $first->delete();

            $rollback = Artisan::call('migrate:rollback', ['--path' => $migrationPath]);
            expect($rollback)->toBe(Command::SUCCESS);

            expect($second->refresh()->name)->toBe('integration service')
                ->and(SystemUserNameUniquenessFeature::active())->toBeFalse();
        });
    });

    it('restores the case-sensitive unique constraint on rollback', function () use ($migrationPath): void {
        isolatedMigration('2026_11_02_120000_convert_system_users_name_to_citext_and_enforce_unique', function () use ($migrationPath): void {
            $migrate = Artisan::call('migrate', ['--path' => $migrationPath]);
            expect($migrate)->toBe(Command::SUCCESS);

            $rollback = Artisan::call('migrate:rollback', ['--path' => $migrationPath]);
            expect($rollback)->toBe(Command::SUCCESS);

            SystemUser::factory()->create(['name' => 'Integration Service']);

            expect(fn () => SystemUser::factory()->create(['name' => 'Integration Service']))
                ->toThrow(UniqueConstraintViolationException::class);
        });
    });

    it('reconciles a live and soft-deleted name collision so rollback can restore the global unique constraint', function () use ($migrationPath): void {
        isolatedMigration('2026_11_02_120000_convert_system_users_name_to_citext_and_enforce_unique', function () use ($migrationPath): void {
            $migrate = Artisan::call('migrate', ['--path' => $migrationPath]);
            expect($migrate)->toBe(Command::SUCCESS);

            // Supported post-migration workflow: the partial unique index ignores soft-deleted rows,
            // so a live row may reuse the exact name of a soft-deleted one.
            $deleted = SystemUser::factory()->create(['name' => 'Integration Service']);
            DB::table('system_users')->where('id', $deleted->getKey())->update(['deleted_at' => now()]);

            $live = SystemUser::factory()->create(['name' => 'Integration Service']);

            $rollback = Artisan::call('migrate:rollback', ['--path' => $migrationPath]);
            expect($rollback)->toBe(Command::SUCCESS);

            // The live row keeps the exact name; the soft-deleted collision is suffixed so the
            // restored global unique constraint can build.
            expect(DB::table('system_users')->where('id', $live->getKey())->value('name'))->toBe('Integration Service')
                ->and(DB::table('system_users')->where('id', $deleted->getKey())->value('name'))->toBe('Integration Service-2');
        });
    });

    it('does not rename a case-variant duplicate system user onto an existing truncated name', function () use ($migrationPath): void {
        isolatedMigration('2026_11_02_120000_convert_system_users_name_to_citext_and_enforce_unique', function () use ($migrationPath): void {
            // Regression: a 255-char duplicate renamed with a "-2" suffix is truncated to 253
            // chars + "-2", which can land on an unrelated live row that already holds that exact
            // truncated value. The de-duplicator must truncate to stay within varchar(255), detect
            // the live collision, and pick the next free suffix instead of producing a name that
            // breaks the new unique index.
            $maxLengthUpper = str_repeat('A', 255);
            $maxLengthLower = str_repeat('a', 255);
            $truncatedCollision = str_repeat('a', 253) . '-2';

            $first = SystemUser::factory()->create(['name' => $maxLengthUpper, 'created_at' => now()->subMinutes(2)]);
            $second = SystemUser::factory()->create(['name' => $maxLengthLower, 'created_at' => now()->subMinute()]);
            $third = SystemUser::factory()->create(['name' => $truncatedCollision, 'created_at' => now()]);

            $migrate = Artisan::call('migrate', ['--path' => $migrationPath]);
            expect($migrate)->toBe(Command::SUCCESS);

            // The oldest live record keeps its name.
            expect($first->refresh()->name)->toBe($maxLengthUpper)
                // "-2" would collide with the pre-existing truncated row, so the next suffix is used.
                ->and($second->refresh()->name)->toBe(str_repeat('a', 253) . '-3')
                // The pre-existing truncated row is in its own group and is left untouched.
                ->and($third->refresh()->name)->toBe($truncatedCollision);
        });
    });
});

// TODO: Cleanup Task (service-monitoring-target-name-uniqueness): delete this describe and the tests within
describe('2026_10_26_122324_convert_service_monitoring_targets_name_to_citext_and_enforce_unique', function (): void {
    $migrationName = '2026_10_26_122324_convert_service_monitoring_targets_name_to_citext_and_enforce_unique';
    $migrationPath = "app-modules/service-management/database/migrations/{$migrationName}.php";

    $insertTarget = function (string $name, CarbonInterface $createdAt, ?CarbonInterface $deletedAt = null): string {
        $id = (string) Str::uuid();

        DB::table('service_monitoring_targets')->insert([
            'id' => $id,
            'name' => $name,
            'domain' => 'https://example.com',
            'frequency' => '5_minutes',
            'monitor_type' => 'availability',
            'follow_redirection' => true,
            'auth_type' => 'none',
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
            'deleted_at' => $deletedAt,
        ]);

        return $id;
    };

    it('rewrites case-insensitive duplicate names with a numeric suffix, keeps the oldest, and activates the feature flag', function () use ($migrationName, $migrationPath, $insertTarget): void {
        isolatedMigration($migrationName, function () use ($migrationPath, $insertTarget): void {
            $firstId = $insertTarget('Customer Website', now()->subMinutes(3));
            $secondId = $insertTarget('customer website', now()->subMinutes(2));
            $thirdId = $insertTarget('CUSTOMER WEBSITE', now()->subMinute());
            $existingSuffixedId = $insertTarget('Customer Website-2', now()->subMinutes(4));
            $uniqueId = $insertTarget('Payment API', now()->subMinutes(5));

            expect(ServiceMonitoringTargetNameUniquenessFeature::active())->toBeFalse();

            $migrate = Artisan::call('migrate', ['--path' => $migrationPath]);
            expect($migrate)->toBe(Command::SUCCESS);

            $name = fn (string $id): string => DB::table('service_monitoring_targets')->where('id', $id)->value('name');

            expect($name($firstId))->toBe('Customer Website')
                ->and($name($secondId))->toBe('customer website-3')
                ->and($name($thirdId))->toBe('CUSTOMER WEBSITE-4')
                ->and($name($existingSuffixedId))->toBe('Customer Website-2')
                ->and($name($uniqueId))->toBe('Payment API')
                ->and(ServiceMonitoringTargetNameUniquenessFeature::active())->toBeTrue();
        });
    });

    it('leaves soft-deleted duplicates untouched', function () use ($migrationName, $migrationPath, $insertTarget): void {
        isolatedMigration($migrationName, function () use ($migrationPath, $insertTarget): void {
            $keptId = $insertTarget('Customer Website', now()->subMinutes(2));
            $trashedId = $insertTarget('customer website', now()->subMinute(), now());

            $migrate = Artisan::call('migrate', ['--path' => $migrationPath]);
            expect($migrate)->toBe(Command::SUCCESS);

            expect(DB::table('service_monitoring_targets')->where('id', $keptId)->value('name'))->toBe('Customer Website')
                ->and(DB::table('service_monitoring_targets')->where('id', $trashedId)->value('name'))->toBe('customer website');
        });
    });

    it('enforces case-insensitive uniqueness among non-trashed service monitoring targets at the database level', function () use ($migrationName, $migrationPath, $insertTarget): void {
        isolatedMigration($migrationName, function () use ($migrationPath, $insertTarget): void {
            $migrate = Artisan::call('migrate', ['--path' => $migrationPath]);
            expect($migrate)->toBe(Command::SUCCESS);

            $insertTarget('Customer Website', now());

            expect(fn (): string => $insertTarget('CUSTOMER WEBSITE', now()))
                ->toThrow(UniqueConstraintViolationException::class);
        });
    });

    it('strips the numeric suffix on rollback once the original name is no longer taken', function () use ($migrationName, $migrationPath, $insertTarget): void {
        isolatedMigration($migrationName, function () use ($migrationPath, $insertTarget): void {
            $firstId = $insertTarget('Customer Website', now()->subMinutes(2));
            $secondId = $insertTarget('customer website', now()->subMinute());

            $migrate = Artisan::call('migrate', ['--path' => $migrationPath]);
            expect($migrate)->toBe(Command::SUCCESS);

            expect(DB::table('service_monitoring_targets')->where('id', $secondId)->value('name'))->toBe('customer website-2');

            DB::table('service_monitoring_targets')->where('id', $firstId)->update(['deleted_at' => now()]);

            $rollback = Artisan::call('migrate:rollback', ['--path' => $migrationPath]);
            expect($rollback)->toBe(Command::SUCCESS);

            expect(DB::table('service_monitoring_targets')->where('id', $secondId)->value('name'))->toBe('customer website')
                ->and(ServiceMonitoringTargetNameUniquenessFeature::active())->toBeFalse();
        });
    });
});

// TODO: Cleanup Task (maintenance-provider-name-uniqueness): delete this describe and the tests within
describe('2026_10_29_112040_convert_maintenance_providers_name_to_citext_and_enforce_unique', function (): void {
    $migrationName = '2026_10_29_112040_convert_maintenance_providers_name_to_citext_and_enforce_unique';
    $migrationPath = "app-modules/inventory-management/database/migrations/{$migrationName}.php";

    $insertProvider = function (string $name, CarbonInterface $createdAt, ?CarbonInterface $deletedAt = null): string {
        $id = (string) Str::uuid();

        DB::table('maintenance_providers')->insert([
            'id' => $id,
            'name' => $name,
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
            'deleted_at' => $deletedAt,
        ]);

        return $id;
    };

    it('rewrites case-insensitive duplicate names with a numeric suffix, keeps the oldest, and activates the feature flag', function () use ($migrationName, $migrationPath, $insertProvider): void {
        isolatedMigration($migrationName, function () use ($migrationPath, $insertProvider): void {
            $firstId = $insertProvider('Acme Repairs', now()->subMinutes(3));
            $secondId = $insertProvider('acme repairs', now()->subMinutes(2));
            $thirdId = $insertProvider('ACME REPAIRS', now()->subMinute());
            $existingSuffixedId = $insertProvider('Acme Repairs-2', now()->subMinutes(4));
            $uniqueId = $insertProvider('Globex Services', now()->subMinutes(5));

            expect(MaintenanceProviderNameUniquenessFeature::active())->toBeFalse();

            $migrate = Artisan::call('migrate', ['--path' => $migrationPath]);
            expect($migrate)->toBe(Command::SUCCESS);

            $name = fn (string $id): string => DB::table('maintenance_providers')->where('id', $id)->value('name');

            expect($name($firstId))->toBe('Acme Repairs')
                ->and($name($secondId))->toBe('acme repairs-3')
                ->and($name($thirdId))->toBe('ACME REPAIRS-4')
                ->and($name($existingSuffixedId))->toBe('Acme Repairs-2')
                ->and($name($uniqueId))->toBe('Globex Services')
                ->and(MaintenanceProviderNameUniquenessFeature::active())->toBeTrue();
        });
    });

    it('leaves soft-deleted duplicates untouched', function () use ($migrationName, $migrationPath, $insertProvider): void {
        isolatedMigration($migrationName, function () use ($migrationPath, $insertProvider): void {
            $keptId = $insertProvider('Acme Repairs', now()->subMinutes(2));
            $trashedId = $insertProvider('acme repairs', now()->subMinute(), now());

            $migrate = Artisan::call('migrate', ['--path' => $migrationPath]);
            expect($migrate)->toBe(Command::SUCCESS);

            expect(DB::table('maintenance_providers')->where('id', $keptId)->value('name'))->toBe('Acme Repairs')
                ->and(DB::table('maintenance_providers')->where('id', $trashedId)->value('name'))->toBe('acme repairs');
        });
    });

    it('enforces case-insensitive uniqueness among non-trashed maintenance providers at the database level', function () use ($migrationName, $migrationPath, $insertProvider): void {
        isolatedMigration($migrationName, function () use ($migrationPath, $insertProvider): void {
            $migrate = Artisan::call('migrate', ['--path' => $migrationPath]);
            expect($migrate)->toBe(Command::SUCCESS);

            $insertProvider('Acme Repairs', now());

            expect(fn (): string => $insertProvider('ACME REPAIRS', now()))
                ->toThrow(UniqueConstraintViolationException::class);
        });
    });

    it('strips the numeric suffix on rollback once the original name is no longer taken', function () use ($migrationName, $migrationPath, $insertProvider): void {
        isolatedMigration($migrationName, function () use ($migrationPath, $insertProvider): void {
            $firstId = $insertProvider('Acme Repairs', now()->subMinutes(2));
            $secondId = $insertProvider('acme repairs', now()->subMinute());

            $migrate = Artisan::call('migrate', ['--path' => $migrationPath]);
            expect($migrate)->toBe(Command::SUCCESS);

            expect(DB::table('maintenance_providers')->where('id', $secondId)->value('name'))->toBe('acme repairs-2');

            DB::table('maintenance_providers')->where('id', $firstId)->update(['deleted_at' => now()]);

            $rollback = Artisan::call('migrate:rollback', ['--path' => $migrationPath]);
            expect($rollback)->toBe(Command::SUCCESS);

            expect(DB::table('maintenance_providers')->where('id', $secondId)->value('name'))->toBe('acme repairs')
                ->and(MaintenanceProviderNameUniquenessFeature::active())->toBeFalse();
        });
    });
});

// TODO: Cleanup Task (pipeline-name-uniqueness): delete this describe and the tests within
describe('2026_11_04_100000_convert_pipelines_name_to_citext_and_enforce_unique', function (): void {
    $migrationName = '2026_11_04_100000_convert_pipelines_name_to_citext_and_enforce_unique';
    $migrationPath = "app-modules/project/database/migrations/{$migrationName}.php";

    it('rewrites case-insensitive duplicate names within a project with a numeric suffix, keeps the oldest and leaves other projects untouched', function () use ($migrationName, $migrationPath): void {
        isolatedMigration($migrationName, function () use ($migrationPath): void {
            $project = Project::factory()->create();
            $otherProject = Project::factory()->create();

            $first = Pipeline::factory()->for($project)->create(['name' => 'Admissions', 'created_at' => now()->subMinutes(3)]);
            $second = Pipeline::factory()->for($project)->create(['name' => 'admissions', 'created_at' => now()->subMinutes(2)]);
            $third = Pipeline::factory()->for($project)->create(['name' => 'ADMISSIONS', 'created_at' => now()->subMinute()]);
            $unique = Pipeline::factory()->for($project)->create(['name' => 'Retention', 'created_at' => now()->subMinutes(4)]);
            $other = Pipeline::factory()->for($otherProject)->create(['name' => 'admissions', 'created_at' => now()->subMinutes(5)]);

            expect(PipelineNameUniquenessFeature::active())->toBeFalse();

            $migrate = Artisan::call('migrate', ['--path' => $migrationPath]);
            expect($migrate)->toBe(Command::SUCCESS);

            expect($first->refresh()->name)->toBe('Admissions')
                ->and($second->refresh()->name)->toBe('admissions-2')
                ->and($third->refresh()->name)->toBe('ADMISSIONS-3')
                ->and($unique->refresh()->name)->toBe('Retention')
                ->and($other->refresh()->name)->toBe('admissions')
                ->and(PipelineNameUniquenessFeature::active())->toBeTrue();
        });
    });

    it('treats archived pipelines as regular rows when repairing duplicates', function () use ($migrationName, $migrationPath): void {
        isolatedMigration($migrationName, function () use ($migrationPath): void {
            $project = Project::factory()->create();
            $kept = Pipeline::factory()->for($project)->create(['name' => 'Admissions', 'created_at' => now()->subMinutes(2)]);
            $archived = Pipeline::factory()->for($project)->create(['name' => 'admissions', 'created_at' => now()->subMinute()]);
            $archived->archive();

            $migrate = Artisan::call('migrate', ['--path' => $migrationPath]);
            expect($migrate)->toBe(Command::SUCCESS);

            expect($kept->refresh()->name)->toBe('Admissions')
                ->and($archived->refresh()->name)->toBe('admissions-2');
        });
    });

    it('enforces case-insensitive uniqueness within a project at the database level but not across projects', function () use ($migrationName, $migrationPath): void {
        isolatedMigration($migrationName, function () use ($migrationPath): void {
            $migrate = Artisan::call('migrate', ['--path' => $migrationPath]);
            expect($migrate)->toBe(Command::SUCCESS);

            $project = Project::factory()->create();
            Pipeline::factory()->for($project)->create(['name' => 'Admissions']);

            expect(fn () => Pipeline::factory()->for($project)->create(['name' => 'ADMISSIONS']))
                ->toThrow(UniqueConstraintViolationException::class);

            $other = Pipeline::factory()->for(Project::factory()->create())->create(['name' => 'ADMISSIONS']);

            expect($other->refresh()->name)->toBe('ADMISSIONS');
        });
    });

    it('keeps names untouched on rollback, including legitimate names that end in a numeric suffix', function () use ($migrationName, $migrationPath): void {
        isolatedMigration($migrationName, function () use ($migrationPath): void {
            $project = Project::factory()->create();
            Pipeline::factory()->for($project)->create(['name' => 'Admissions', 'created_at' => now()->subMinutes(3)]);
            $renamed = Pipeline::factory()->for($project)->create(['name' => 'admissions', 'created_at' => now()->subMinutes(2)]);
            $legitimate = Pipeline::factory()->for($project)->create(['name' => 'Release-2026', 'created_at' => now()->subMinute()]);

            $migrate = Artisan::call('migrate', ['--path' => $migrationPath]);
            expect($migrate)->toBe(Command::SUCCESS);

            $rollback = Artisan::call('migrate:rollback', ['--path' => $migrationPath]);
            expect($rollback)->toBe(Command::SUCCESS);

            expect($renamed->refresh()->name)->toBe('admissions-2')
                ->and($legitimate->refresh()->name)->toBe('Release-2026')
                ->and(PipelineNameUniquenessFeature::active())->toBeFalse();

            Pipeline::factory()->for($project)->create(['name' => 'ADMISSIONS']);
        });
    });
});

// TODO: Cleanup Task (pipeline-name-uniqueness): delete this describe and the tests within
describe('2026_11_04_100100_convert_pipeline_stages_name_to_citext_and_enforce_unique', function (): void {
    $migrationName = '2026_11_04_100100_convert_pipeline_stages_name_to_citext_and_enforce_unique';
    $migrationPath = "app-modules/project/database/migrations/{$migrationName}.php";

    it('rewrites case-insensitive duplicate names within a pipeline, keeps the oldest and leaves other pipelines untouched', function () use ($migrationName, $migrationPath): void {
        isolatedMigration($migrationName, function () use ($migrationPath): void {
            $pipeline = Pipeline::factory()->create();
            $otherPipeline = Pipeline::factory()->create();

            $first = PipelineStage::factory()->for($pipeline)->create(['name' => 'Review', 'created_at' => now()->subMinutes(3)]);
            $second = PipelineStage::factory()->for($pipeline)->create(['name' => 'review', 'created_at' => now()->subMinutes(2)]);
            $third = PipelineStage::factory()->for($pipeline)->create(['name' => 'REVIEW', 'created_at' => now()->subMinute()]);
            $other = PipelineStage::factory()->for($otherPipeline)->create(['name' => 'review', 'created_at' => now()->subMinutes(4)]);

            $migrate = Artisan::call('migrate', ['--path' => $migrationPath]);
            expect($migrate)->toBe(Command::SUCCESS);

            expect($first->refresh()->name)->toBe('Review')
                ->and($second->refresh()->name)->toBe('review-2')
                ->and($third->refresh()->name)->toBe('REVIEW-3')
                ->and($other->refresh()->name)->toBe('review')
                ->and(PipelineNameUniquenessFeature::active())->toBeTrue();
        });
    });

    it('enforces case-insensitive uniqueness within a pipeline at the database level but not across pipelines', function () use ($migrationName, $migrationPath): void {
        isolatedMigration($migrationName, function () use ($migrationPath): void {
            $migrate = Artisan::call('migrate', ['--path' => $migrationPath]);
            expect($migrate)->toBe(Command::SUCCESS);

            $pipeline = Pipeline::factory()->create();
            PipelineStage::factory()->for($pipeline)->create(['name' => 'Review']);

            expect(fn () => PipelineStage::factory()->for($pipeline)->create(['name' => 'REVIEW']))
                ->toThrow(UniqueConstraintViolationException::class);

            $other = PipelineStage::factory()->for(Pipeline::factory()->create())->create(['name' => 'REVIEW']);

            expect($other->refresh()->name)->toBe('REVIEW');
        });
    });

    it('keeps names untouched on rollback and restores the original unique constraint', function () use ($migrationName, $migrationPath): void {
        isolatedMigration($migrationName, function () use ($migrationPath): void {
            $pipeline = Pipeline::factory()->create();
            PipelineStage::factory()->for($pipeline)->create(['name' => 'Review', 'created_at' => now()->subMinutes(3)]);
            $renamed = PipelineStage::factory()->for($pipeline)->create(['name' => 'review', 'created_at' => now()->subMinutes(2)]);
            $legitimate = PipelineStage::factory()->for($pipeline)->create(['name' => 'Phase-2', 'created_at' => now()->subMinute()]);

            $migrate = Artisan::call('migrate', ['--path' => $migrationPath]);
            expect($migrate)->toBe(Command::SUCCESS);

            $rollback = Artisan::call('migrate:rollback', ['--path' => $migrationPath]);
            expect($rollback)->toBe(Command::SUCCESS);

            expect($renamed->refresh()->name)->toBe('review-2')
                ->and($legitimate->refresh()->name)->toBe('Phase-2')
                ->and(PipelineNameUniquenessFeature::active())->toBeFalse();

            PipelineStage::factory()->for($pipeline)->create(['name' => 'REVIEW']);

            expect(fn () => PipelineStage::factory()->for($pipeline)->create(['name' => 'REVIEW']))
                ->toThrow(UniqueConstraintViolationException::class);
        });
    });
});
