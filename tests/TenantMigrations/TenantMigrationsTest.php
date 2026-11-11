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
use AidingApp\Authorization\Models\Role;
use AidingApp\Contact\Models\Contact;
use AidingApp\Contact\Models\ContactType;
use AidingApp\ContractManagement\Models\ContractType;
use AidingApp\Engagement\Models\EmailTemplate;
use AidingApp\LicenseManagement\Models\Product;
use AidingApp\Notification\Enums\EmailMessageEventType;
use AidingApp\Notification\Models\EmailMessage;
use AidingApp\Notification\Models\EmailMessageEvent;
use AidingApp\Project\Models\Pipeline;
use AidingApp\Project\Models\PipelineStage;
use AidingApp\Project\Models\Project;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use App\Features\AdvisorySeverityNameUniquenessFeature;
use App\Features\AdvisoryStatusNameUniquenessFeature;
use App\Features\ChangeRequestStatusNameUniquenessFeature;
use App\Features\ChangeRequestTypeNameUniquenessFeature;
use App\Features\ContactPermissionConsolidationFeature;
use App\Features\ContactTypeNameUniquenessFeature;
use App\Features\ContractNameUniquenessFeature;
use App\Features\ContractTypeNameUniquenessFeature;
use App\Features\EmailTemplateNameUniquenessFeature;
use App\Features\KnowledgeBaseCategoryNameUniquenessFeature;
use App\Features\LicensePermissionConsolidationFeature;
use App\Features\MaintenanceProviderNameUniquenessFeature;
use App\Features\PipelineNameUniquenessFeature;
use App\Features\PipelineStageNameUniquenessFeature;
use App\Features\ProductNameUniquenessFeature;
use App\Features\ProjectPermissionConsolidationFeature;
use App\Features\ServiceMonitoringTargetNameUniquenessFeature;
use App\Features\ServiceRequestFileUploadTypeFeature;
use App\Features\ServiceRequestTypeCategoryNameUniquenessFeature;
use App\Features\SlaNameUniquenessFeature;
use App\Features\SystemUserNameUniquenessFeature;
use App\Models\SystemUser;
use App\Models\User;
use App\Support\AbstractFeatureFlag;
use Carbon\CarbonInterface;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

use Spatie\Permission\PermissionRegistrar;

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

// TODO: Cleanup Task (sla-name-uniqueness): delete this describe and the tests within
describe('2026_10_26_083011_convert_slas_name_to_citext_and_enforce_unique', function (): void {
    $migrationName = '2026_10_26_083011_convert_slas_name_to_citext_and_enforce_unique';
    $migrationPath = "app-modules/service-management/database/migrations/{$migrationName}.php";

    $insertSla = function (string $name, CarbonInterface $createdAt, ?CarbonInterface $deletedAt = null): string {
        $id = (string) Str::uuid();

        DB::table('slas')->insert([
            'id' => $id,
            'name' => $name,
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
            'deleted_at' => $deletedAt,
        ]);

        return $id;
    };

    it('rewrites case-insensitive duplicate names with a numeric suffix, keeps the oldest, and activates the feature flag', function () use ($migrationName, $migrationPath, $insertSla): void {
        isolatedMigration($migrationName, function () use ($migrationPath, $insertSla): void {
            $firstId = $insertSla('Gold Support', now()->subMinutes(3));
            $secondId = $insertSla('gold support', now()->subMinutes(2));
            $thirdId = $insertSla('GOLD SUPPORT', now()->subMinute());
            $existingSuffixedId = $insertSla('Gold Support-2', now()->subMinutes(4));
            $uniqueId = $insertSla('Silver Support', now()->subMinutes(5));

            expect(SlaNameUniquenessFeature::active())->toBeFalse();

            $migrate = Artisan::call('migrate', ['--path' => $migrationPath]);
            expect($migrate)->toBe(Command::SUCCESS);

            $name = fn (string $id): string => DB::table('slas')->where('id', $id)->value('name');

            expect($name($firstId))->toBe('Gold Support')
                ->and($name($secondId))->toBe('gold support-3')
                ->and($name($thirdId))->toBe('GOLD SUPPORT-4')
                ->and($name($existingSuffixedId))->toBe('Gold Support-2')
                ->and($name($uniqueId))->toBe('Silver Support')
                ->and(SlaNameUniquenessFeature::active())->toBeTrue();
        });
    });

    it('leaves soft-deleted duplicates untouched', function () use ($migrationName, $migrationPath, $insertSla): void {
        isolatedMigration($migrationName, function () use ($migrationPath, $insertSla): void {
            $keptId = $insertSla('Gold Support', now()->subMinutes(2));
            $trashedId = $insertSla('gold support', now()->subMinute(), now());

            $migrate = Artisan::call('migrate', ['--path' => $migrationPath]);
            expect($migrate)->toBe(Command::SUCCESS);

            expect(DB::table('slas')->where('id', $keptId)->value('name'))->toBe('Gold Support')
                ->and(DB::table('slas')->where('id', $trashedId)->value('name'))->toBe('gold support');
        });
    });

    it('enforces case-insensitive uniqueness among non-trashed SLAs at the database level', function () use ($migrationName, $migrationPath, $insertSla): void {
        isolatedMigration($migrationName, function () use ($migrationPath, $insertSla): void {
            $migrate = Artisan::call('migrate', ['--path' => $migrationPath]);
            expect($migrate)->toBe(Command::SUCCESS);

            $insertSla('Gold Support', now());

            expect(fn (): string => $insertSla('GOLD SUPPORT', now()))
                ->toThrow(UniqueConstraintViolationException::class);
        });
    });

    it('allows a non-trashed SLA to reuse a soft-deleted name once the migration has run', function () use ($migrationName, $migrationPath, $insertSla): void {
        isolatedMigration($migrationName, function () use ($migrationPath, $insertSla): void {
            $migrate = Artisan::call('migrate', ['--path' => $migrationPath]);
            expect($migrate)->toBe(Command::SUCCESS);

            $insertSla('Gold Support', now(), now());

            $liveId = $insertSla('Gold Support', now());

            expect(DB::table('slas')->where('id', $liveId)->value('name'))->toBe('Gold Support');
        });
    });

    it('leaves deduplicated and pre-existing suffixed names unchanged on rollback', function () use ($migrationName, $migrationPath, $insertSla): void {
        isolatedMigration($migrationName, function () use ($migrationPath, $insertSla): void {
            $firstId = $insertSla('Gold Support', now()->subMinutes(2));
            $secondId = $insertSla('gold support', now()->subMinute());
            // A pre-existing, user-authored name ending in `-2` with no matching `Silver Support`. The migration must
            // not touch it on the way up, and rollback must not strip its suffix either.
            $preExistingSuffixedId = $insertSla('Silver Support-2', now()->subMinutes(3));

            $migrate = Artisan::call('migrate', ['--path' => $migrationPath]);
            expect($migrate)->toBe(Command::SUCCESS);

            expect(DB::table('slas')->where('id', $secondId)->value('name'))->toBe('gold support-2')
                ->and(DB::table('slas')->where('id', $preExistingSuffixedId)->value('name'))->toBe('Silver Support-2');

            $rollback = Artisan::call('migrate:rollback', ['--path' => $migrationPath]);
            expect($rollback)->toBe(Command::SUCCESS);

            expect(DB::table('slas')->where('id', $firstId)->value('name'))->toBe('Gold Support')
                ->and(DB::table('slas')->where('id', $secondId)->value('name'))->toBe('gold support-2')
                ->and(DB::table('slas')->where('id', $preExistingSuffixedId)->value('name'))->toBe('Silver Support-2')
                ->and(SlaNameUniquenessFeature::active())->toBeFalse();
        });
    });

    it('restores the case-insensitive varchar column on rollback', function () use ($migrationName, $migrationPath, $insertSla): void {
        isolatedMigration($migrationName, function () use ($migrationPath, $insertSla): void {
            $migrate = Artisan::call('migrate', ['--path' => $migrationPath]);
            expect($migrate)->toBe(Command::SUCCESS);

            $rollback = Artisan::call('migrate:rollback', ['--path' => $migrationPath]);
            expect($rollback)->toBe(Command::SUCCESS);

            $firstId = $insertSla('Gold Support', now());
            $secondId = $insertSla('gold support', now());

            expect(DB::table('slas')->where('id', $firstId)->value('name'))->toBe('Gold Support')
                ->and(DB::table('slas')->where('id', $secondId)->value('name'))->toBe('gold support');
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

// TODO: Cleanup Task (knowledge-base-category-name-uniqueness): delete this describe and the tests within
describe('2026_11_02_120000_convert_knowledge_base_categories_name_to_citext_and_enforce_unique', function (): void {
    $migrationName = '2026_11_02_120000_convert_knowledge_base_categories_name_to_citext_and_enforce_unique';
    $migrationPath = "app-modules/knowledge-base/database/migrations/{$migrationName}.php";

    $insertCategory = function (string $name, CarbonInterface $createdAt, ?CarbonInterface $deletedAt = null): string {
        $id = (string) Str::uuid();

        DB::table('knowledge_base_categories')->insert([
            'id' => $id,
            'name' => $name,
            'public_id' => Str::random(8),
            'slug' => Str::uuid()->toString(),
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
            'deleted_at' => $deletedAt,
        ]);

        return $id;
    };

    it('rewrites case-insensitive duplicate names with a numeric suffix, keeps the oldest, and activates the feature flag', function () use ($migrationName, $migrationPath, $insertCategory): void {
        isolatedMigration($migrationName, function () use ($migrationPath, $insertCategory): void {
            $firstId = $insertCategory('Getting Started', now()->subMinutes(3));
            $secondId = $insertCategory('getting started', now()->subMinutes(2));
            $thirdId = $insertCategory('GETTING STARTED', now()->subMinute());
            $existingSuffixedId = $insertCategory('Getting Started-2', now()->subMinutes(4));
            $uniqueId = $insertCategory('Billing', now()->subMinutes(5));

            expect(KnowledgeBaseCategoryNameUniquenessFeature::active())->toBeFalse();

            $migrate = Artisan::call('migrate', ['--path' => $migrationPath]);
            expect($migrate)->toBe(Command::SUCCESS);

            $name = fn (string $id): string => DB::table('knowledge_base_categories')->where('id', $id)->value('name');

            expect($name($firstId))->toBe('Getting Started')
                ->and($name($secondId))->toBe('getting started-3')
                ->and($name($thirdId))->toBe('GETTING STARTED-4')
                ->and($name($existingSuffixedId))->toBe('Getting Started-2')
                ->and($name($uniqueId))->toBe('Billing')
                ->and(KnowledgeBaseCategoryNameUniquenessFeature::active())->toBeTrue();
        });
    });

    it('leaves soft-deleted duplicates untouched', function () use ($migrationName, $migrationPath, $insertCategory): void {
        isolatedMigration($migrationName, function () use ($migrationPath, $insertCategory): void {
            $keptId = $insertCategory('Getting Started', now()->subMinutes(2));
            $trashedId = $insertCategory('getting started', now()->subMinute(), now());

            $migrate = Artisan::call('migrate', ['--path' => $migrationPath]);
            expect($migrate)->toBe(Command::SUCCESS);

            expect(DB::table('knowledge_base_categories')->where('id', $keptId)->value('name'))->toBe('Getting Started')
                ->and(DB::table('knowledge_base_categories')->where('id', $trashedId)->value('name'))->toBe('getting started');
        });
    });

    it('enforces case-insensitive uniqueness among non-trashed knowledge base categories at the database level', function () use ($migrationName, $migrationPath, $insertCategory): void {
        isolatedMigration($migrationName, function () use ($migrationPath, $insertCategory): void {
            $migrate = Artisan::call('migrate', ['--path' => $migrationPath]);
            expect($migrate)->toBe(Command::SUCCESS);

            $insertCategory('Getting Started', now());

            expect(fn (): string => $insertCategory('GETTING STARTED', now()))
                ->toThrow(UniqueConstraintViolationException::class);
        });
    });

    it('allows a non-trashed knowledge base category to reuse a soft-deleted name once the migration has run', function () use ($migrationName, $migrationPath, $insertCategory): void {
        isolatedMigration($migrationName, function () use ($migrationPath, $insertCategory): void {
            $migrate = Artisan::call('migrate', ['--path' => $migrationPath]);
            expect($migrate)->toBe(Command::SUCCESS);

            $insertCategory('Getting Started', now(), now());

            $liveId = $insertCategory('Getting Started', now());

            expect(DB::table('knowledge_base_categories')->where('id', $liveId)->value('name'))->toBe('Getting Started');
        });
    });

    it('leaves deduplicated names unchanged on rollback and only deactivates the feature flag', function () use ($migrationName, $migrationPath, $insertCategory): void {
        isolatedMigration($migrationName, function () use ($migrationPath, $insertCategory): void {
            $firstId = $insertCategory('Getting Started', now()->subMinutes(2));
            $secondId = $insertCategory('getting started', now()->subMinute());

            $migrate = Artisan::call('migrate', ['--path' => $migrationPath]);
            expect($migrate)->toBe(Command::SUCCESS);

            expect(DB::table('knowledge_base_categories')->where('id', $secondId)->value('name'))->toBe('getting started-2');

            DB::table('knowledge_base_categories')->where('id', $firstId)->update(['deleted_at' => now()]);

            $rollback = Artisan::call('migrate:rollback', ['--path' => $migrationPath]);
            expect($rollback)->toBe(Command::SUCCESS);

            expect(DB::table('knowledge_base_categories')->where('id', $firstId)->value('name'))->toBe('Getting Started')
                ->and(DB::table('knowledge_base_categories')->where('id', $secondId)->value('name'))->toBe('getting started-2')
                ->and(KnowledgeBaseCategoryNameUniquenessFeature::active())->toBeFalse();
        });
    });

    it('does not mutate an originally numbered name on rollback', function () use ($migrationName, $migrationPath, $insertCategory): void {
        isolatedMigration($migrationName, function () use ($migrationPath, $insertCategory): void {
            $numberedId = $insertCategory('ISO-27001', now());

            $migrate = Artisan::call('migrate', ['--path' => $migrationPath]);
            expect($migrate)->toBe(Command::SUCCESS);

            expect(DB::table('knowledge_base_categories')->where('id', $numberedId)->value('name'))->toBe('ISO-27001');

            $rollback = Artisan::call('migrate:rollback', ['--path' => $migrationPath]);
            expect($rollback)->toBe(Command::SUCCESS);

            expect(DB::table('knowledge_base_categories')->where('id', $numberedId)->value('name'))->toBe('ISO-27001')
                ->and(KnowledgeBaseCategoryNameUniquenessFeature::active())->toBeFalse();
        });
    });
});

// TODO: Cleanup Task (service-request-type-category-name-uniqueness): delete this describe and the tests within
describe('2026_11_02_120000_convert_service_request_type_categories_name_to_citext_and_enforce_unique', function (): void {
    $migrationName = '2026_11_02_120000_convert_service_request_type_categories_name_to_citext_and_enforce_unique';
    $migrationPath = "app-modules/service-management/database/migrations/{$migrationName}.php";

    $insertCategory = function (string $name, CarbonInterface $createdAt, ?CarbonInterface $deletedAt = null): string {
        $id = (string) Str::uuid();

        DB::table('service_request_type_categories')->insert([
            'id' => $id,
            'name' => $name,
            'parent_id' => null,
            'sort' => null,
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
            'deleted_at' => $deletedAt,
        ]);

        return $id;
    };

    it('rewrites case-insensitive duplicate names with a numeric suffix, keeps the oldest, and activates the feature flag', function () use ($migrationName, $migrationPath, $insertCategory): void {
        isolatedMigration($migrationName, function () use ($migrationPath, $insertCategory): void {
            $firstId = $insertCategory('Support', now()->subMinutes(3));
            $secondId = $insertCategory('support', now()->subMinutes(2));
            $thirdId = $insertCategory('SUPPORT', now()->subMinute());
            $existingSuffixedId = $insertCategory('Support-2', now()->subMinutes(4));
            $uniqueId = $insertCategory('Billing', now()->subMinutes(5));

            expect(ServiceRequestTypeCategoryNameUniquenessFeature::active())->toBeFalse();

            $migrate = Artisan::call('migrate', ['--path' => $migrationPath]);
            expect($migrate)->toBe(Command::SUCCESS);

            $name = fn (string $id): string => DB::table('service_request_type_categories')->where('id', $id)->value('name');

            expect($name($firstId))->toBe('Support')
                ->and($name($secondId))->toBe('support-3')
                ->and($name($thirdId))->toBe('SUPPORT-4')
                ->and($name($existingSuffixedId))->toBe('Support-2')
                ->and($name($uniqueId))->toBe('Billing')
                ->and(ServiceRequestTypeCategoryNameUniquenessFeature::active())->toBeTrue();
        });
    });

    it('leaves soft-deleted duplicates untouched', function () use ($migrationName, $migrationPath, $insertCategory): void {
        isolatedMigration($migrationName, function () use ($migrationPath, $insertCategory): void {
            $keptId = $insertCategory('Support', now()->subMinutes(2));
            $trashedId = $insertCategory('support', now()->subMinute(), now());

            $migrate = Artisan::call('migrate', ['--path' => $migrationPath]);
            expect($migrate)->toBe(Command::SUCCESS);

            expect(DB::table('service_request_type_categories')->where('id', $keptId)->value('name'))->toBe('Support')
                ->and(DB::table('service_request_type_categories')->where('id', $trashedId)->value('name'))->toBe('support');
        });
    });

    it('enforces case-insensitive uniqueness among non-trashed service request type categories at the database level', function () use ($migrationName, $migrationPath, $insertCategory): void {
        isolatedMigration($migrationName, function () use ($migrationPath, $insertCategory): void {
            $migrate = Artisan::call('migrate', ['--path' => $migrationPath]);
            expect($migrate)->toBe(Command::SUCCESS);

            $insertCategory('Support', now());

            expect(fn (): string => $insertCategory('SUPPORT', now()))
                ->toThrow(UniqueConstraintViolationException::class);
        });
    });

    it('allows a non-trashed service request type category to reuse a soft-deleted name once the migration has run', function () use ($migrationName, $migrationPath, $insertCategory): void {
        isolatedMigration($migrationName, function () use ($migrationPath, $insertCategory): void {
            $migrate = Artisan::call('migrate', ['--path' => $migrationPath]);
            expect($migrate)->toBe(Command::SUCCESS);

            $insertCategory('Support', now(), now());

            $liveId = $insertCategory('Support', now());

            expect(DB::table('service_request_type_categories')->where('id', $liveId)->value('name'))->toBe('Support');
        });
    });

    it('leaves deduplicated and pre-existing suffixed names unchanged on rollback', function () use ($migrationName, $migrationPath, $insertCategory): void {
        isolatedMigration($migrationName, function () use ($migrationPath, $insertCategory): void {
            $firstId = $insertCategory('Support', now()->subMinutes(2));
            $secondId = $insertCategory('support', now()->subMinute());
            // A pre-existing, user-authored name ending in `-2` with no matching `Level`. The migration must
            // not touch it on the way up, and rollback must not strip its suffix either.
            $preExistingSuffixedId = $insertCategory('Level-2', now()->subMinutes(3));

            $migrate = Artisan::call('migrate', ['--path' => $migrationPath]);
            expect($migrate)->toBe(Command::SUCCESS);

            expect(DB::table('service_request_type_categories')->where('id', $secondId)->value('name'))->toBe('support-2')
                ->and(DB::table('service_request_type_categories')->where('id', $preExistingSuffixedId)->value('name'))->toBe('Level-2');

            $rollback = Artisan::call('migrate:rollback', ['--path' => $migrationPath]);
            expect($rollback)->toBe(Command::SUCCESS);

            expect(DB::table('service_request_type_categories')->where('id', $firstId)->value('name'))->toBe('Support')
                ->and(DB::table('service_request_type_categories')->where('id', $secondId)->value('name'))->toBe('support-2')
                ->and(DB::table('service_request_type_categories')->where('id', $preExistingSuffixedId)->value('name'))->toBe('Level-2')
                ->and(ServiceRequestTypeCategoryNameUniquenessFeature::active())->toBeFalse();
        });
    });

    it('restores the case-insensitive varchar column on rollback', function () use ($migrationName, $migrationPath, $insertCategory): void {
        isolatedMigration($migrationName, function () use ($migrationPath, $insertCategory): void {
            $migrate = Artisan::call('migrate', ['--path' => $migrationPath]);
            expect($migrate)->toBe(Command::SUCCESS);

            $rollback = Artisan::call('migrate:rollback', ['--path' => $migrationPath]);
            expect($rollback)->toBe(Command::SUCCESS);

            $firstId = $insertCategory('Support', now());
            $secondId = $insertCategory('support', now());

            expect(DB::table('service_request_type_categories')->where('id', $firstId)->value('name'))->toBe('Support')
                ->and(DB::table('service_request_type_categories')->where('id', $secondId)->value('name'))->toBe('support');
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

// TODO: Cleanup Task (tag-name-uniqueness): delete this describe and the tests within
describe('2026_10_30_104700_convert_tags_name_to_citext_and_enforce_unique', function (): void {
    $migrationName = '2026_10_30_104700_convert_tags_name_to_citext_and_enforce_unique';
    $migrationPath = "database/migrations/{$migrationName}.php";

    $insertTag = function (string $name, CarbonInterface $createdAt, ?string $type = 'knowledge_base_item'): string {
        $id = (string) Str::uuid();

        DB::table('tags')->insert([
            'id' => $id,
            'name' => $name,
            'type' => $type,
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ]);

        return $id;
    };

    it('rewrites case-insensitive duplicate names with a numeric suffix and keeps the oldest', function () use ($migrationName, $migrationPath, $insertTag): void {
        isolatedMigration($migrationName, function () use ($migrationPath, $insertTag): void {
            $firstId = $insertTag('Billing', now()->subMinutes(3));
            $secondId = $insertTag('billing', now()->subMinutes(2));
            $thirdId = $insertTag('BILLING', now()->subMinute());
            $existingSuffixedId = $insertTag('Billing-2', now()->subMinutes(4));
            $uniqueId = $insertTag('Shipping', now()->subMinutes(5));

            $migrate = Artisan::call('migrate', ['--path' => $migrationPath]);
            expect($migrate)->toBe(Command::SUCCESS);

            $name = fn (string $id): string => DB::table('tags')->where('id', $id)->value('name');

            expect($name($firstId))->toBe('Billing')
                ->and($name($secondId))->toBe('billing-3')
                ->and($name($thirdId))->toBe('BILLING-4')
                ->and($name($existingSuffixedId))->toBe('Billing-2')
                ->and($name($uniqueId))->toBe('Shipping');
        });
    });

    it('only resolves duplicate names within the same type', function () use ($migrationName, $migrationPath, $insertTag): void {
        isolatedMigration($migrationName, function () use ($migrationPath, $insertTag): void {
            $knowledgeBaseItemId = $insertTag('Billing', now()->subMinutes(4));
            $otherTypeId = $insertTag('billing', now()->subMinutes(3), 'other_type');
            $firstUntypedId = $insertTag('BILLING', now()->subMinutes(2), null);
            $secondUntypedId = $insertTag('Billing', now()->subMinute(), null);

            $migrate = Artisan::call('migrate', ['--path' => $migrationPath]);
            expect($migrate)->toBe(Command::SUCCESS);

            $name = fn (string $id): string => DB::table('tags')->where('id', $id)->value('name');

            expect($name($knowledgeBaseItemId))->toBe('Billing')
                ->and($name($otherTypeId))->toBe('billing')
                ->and($name($firstUntypedId))->toBe('BILLING')
                ->and($name($secondUntypedId))->toBe('Billing-2');
        });
    });

    it('enforces case-insensitive uniqueness per type at the database level', function () use ($migrationName, $migrationPath, $insertTag): void {
        isolatedMigration($migrationName, function () use ($migrationPath, $insertTag): void {
            $migrate = Artisan::call('migrate', ['--path' => $migrationPath]);
            expect($migrate)->toBe(Command::SUCCESS);

            $insertTag('Billing', now());
            $insertTag('Billing', now(), 'other_type');
            $insertTag('Billing', now(), null);

            expect(fn (): string => $insertTag('BILLING', now()))->toThrow(UniqueConstraintViolationException::class)
                ->and(fn (): string => $insertTag('BILLING', now(), null))->toThrow(UniqueConstraintViolationException::class);
        });
    });

    it('strips the numeric suffix on rollback once the original name is no longer taken', function () use ($migrationName, $migrationPath, $insertTag): void {
        isolatedMigration($migrationName, function () use ($migrationPath, $insertTag): void {
            $firstId = $insertTag('Billing', now()->subMinutes(2));
            $secondId = $insertTag('billing', now()->subMinute());

            $migrate = Artisan::call('migrate', ['--path' => $migrationPath]);
            expect($migrate)->toBe(Command::SUCCESS);

            expect(DB::table('tags')->where('id', $secondId)->value('name'))->toBe('billing-2');

            DB::table('tags')->where('id', $firstId)->delete();

            $rollback = Artisan::call('migrate:rollback', ['--path' => $migrationPath]);
            expect($rollback)->toBe(Command::SUCCESS);

            expect(DB::table('tags')->where('id', $secondId)->value('name'))->toBe('billing');
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
                ->and(PipelineNameUniquenessFeature::active())->toBeTrue()
                ->and(PipelineStageNameUniquenessFeature::active())->toBeFalse();
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

            expect(PipelineStageNameUniquenessFeature::active())->toBeFalse();

            $migrate = Artisan::call('migrate', ['--path' => $migrationPath]);
            expect($migrate)->toBe(Command::SUCCESS);

            expect($first->refresh()->name)->toBe('Review')
                ->and($second->refresh()->name)->toBe('review-2')
                ->and($third->refresh()->name)->toBe('REVIEW-3')
                ->and($other->refresh()->name)->toBe('review')
                ->and(PipelineStageNameUniquenessFeature::active())->toBeTrue();
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
                ->and(PipelineStageNameUniquenessFeature::active())->toBeFalse()
                ->and(PipelineNameUniquenessFeature::active())->toBeTrue();

            PipelineStage::factory()->for($pipeline)->create(['name' => 'REVIEW']);

            expect(fn () => PipelineStage::factory()->for($pipeline)->create(['name' => 'REVIEW']))
                ->toThrow(UniqueConstraintViolationException::class);
        });
    });
});

describe('2026_10_30_002949_increase_occurred_at_precision_on_email_message_events_table', function (): void {
    it('keeps fractional seconds on occurred_at', function (): void {
        isolatedMigration('2026_10_30_002949_increase_occurred_at_precision_on_email_message_events_table', function (): void {
            $message = EmailMessage::factory()->create();

            // New code writes microseconds before the migration has run; Postgres rounds them away.
            $eventBefore = $message->events()->save(EmailMessageEvent::factory()->make(['occurred_at' => '2026-01-01 10:00:00.123456']));

            expect($eventBefore->refresh()->occurred_at->format('Y-m-d H:i:s.u'))->toBe('2026-01-01 10:00:00.000000');

            $migrate = Artisan::call('migrate', [
                '--path' => 'app-modules/notification/database/migrations/2026_10_30_002949_increase_occurred_at_precision_on_email_message_events_table.php',
            ]);

            $eventAfter = $message->events()->save(EmailMessageEvent::factory()->make(['occurred_at' => '2026-01-01 10:00:00.123456']));

            expect($migrate)->toBe(Command::SUCCESS)
                ->and($eventAfter->refresh()->occurred_at->format('Y-m-d H:i:s.u'))->toBe('2026-01-01 10:00:00.123456');
        });
    });
});

// TODO: Cleanup Task (email-message-events-occurred-at-backfill): delete this describe and the tests within
describe('2026_10_30_003135_tmp_backfill_email_message_events_occurred_at', function (): void {
    it('backfills occurred_at from the event\'s own payload timestamp', function (EmailMessageEventType $type, ?string $path, string $expected): void {
        isolatedMigration('2026_10_30_003135_tmp_backfill_email_message_events_occurred_at', function () use ($type, $path, $expected): void {
            $payload = ['mail' => ['timestamp' => '2016-10-19T23:20:52.240Z']];

            if ($path !== null) {
                $payload[$path] = ['timestamp' => '2016-10-19T23:21:04.133Z'];
            }

            $event = EmailMessage::factory()->create()->events()->save(EmailMessageEvent::factory()->make([
                'type' => $type,
                'payload' => $payload,
                'occurred_at' => '2026-05-06 07:08:09',
            ]));

            $migrate = Artisan::call('migrate', ['--path' => 'database/migrations/2026_10_30_003135_tmp_backfill_email_message_events_occurred_at.php']);

            expect($migrate)->toBe(Command::SUCCESS)
                ->and($event->refresh()->occurred_at->format('Y-m-d H:i:s.v'))->toBe($expected);
        });
    })->with([
        'send' => [EmailMessageEventType::Send, null, '2016-10-19 23:20:52.240'],
        'reject' => [EmailMessageEventType::Reject, null, '2016-10-19 23:20:52.240'],
        'rendering failure' => [EmailMessageEventType::RenderingFailure, null, '2016-10-19 23:20:52.240'],
        'bounce' => [EmailMessageEventType::Bounce, 'bounce', '2016-10-19 23:21:04.133'],
        'complaint' => [EmailMessageEventType::Complaint, 'complaint', '2016-10-19 23:21:04.133'],
        'delivery' => [EmailMessageEventType::Delivery, 'delivery', '2016-10-19 23:21:04.133'],
        'open' => [EmailMessageEventType::Open, 'open', '2016-10-19 23:21:04.133'],
        'click' => [EmailMessageEventType::Click, 'click', '2016-10-19 23:21:04.133'],
        'subscription' => [EmailMessageEventType::Subscription, 'subscription', '2016-10-19 23:21:04.133'],
        'delivery delay' => [EmailMessageEventType::DeliveryDelay, 'deliveryDelay', '2016-10-19 23:21:04.133'],
    ]);

    it('leaves occurred_at unchanged when the payload has no usable timestamp', function (EmailMessageEventType $type, array $payload): void {
        isolatedMigration('2026_10_30_003135_tmp_backfill_email_message_events_occurred_at', function () use ($type, $payload): void {
            $event = EmailMessage::factory()->create()->events()->save(EmailMessageEvent::factory()->make([
                'type' => $type,
                'payload' => $payload,
                'occurred_at' => '2026-05-06 07:08:09',
            ]));

            $migrate = Artisan::call('migrate', ['--path' => 'database/migrations/2026_10_30_003135_tmp_backfill_email_message_events_occurred_at.php']);

            expect($migrate)->toBe(Command::SUCCESS)
                ->and($event->refresh()->occurred_at->format('Y-m-d H:i:s.v'))->toBe('2026-05-06 07:08:09.000');
        });
    })->with([
        'type without a provider timestamp' => [EmailMessageEventType::Dispatched, ['mail' => ['timestamp' => '2016-10-19T23:20:52.240Z']]],
        'missing event object' => [EmailMessageEventType::DeliveryDelay, ['mail' => ['timestamp' => '2016-10-19T23:20:52.240Z']]],
        'unparseable timestamp' => [EmailMessageEventType::Delivery, ['delivery' => ['timestamp' => 'not-a-date']]],
    ]);
});

// TODO: Cleanup Task (change-request-type-name-uniqueness): delete the helper below, the two describes that follow, and the tests within
// TODO: Cleanup Task (change-request-status-name-uniqueness): delete the helper below, the two describes that follow, and the tests within
$defineChangeRequestNameUniquenessTests = function (string $migrationName, string $table, array $extraColumns, string $featureClass, string $otherFeatureClass): void {
    assert(is_subclass_of($featureClass, AbstractFeatureFlag::class));
    assert(is_subclass_of($otherFeatureClass, AbstractFeatureFlag::class));
    $migrationPath = "app-modules/service-management/database/migrations/{$migrationName}.php";

    $insertRecord = function (string $name, CarbonInterface $createdAt, ?CarbonInterface $deletedAt = null) use ($table, $extraColumns): string {
        $id = (string) Str::uuid();

        DB::table($table)->insert([
            'id' => $id,
            'name' => $name,
            ...$extraColumns,
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
            'deleted_at' => $deletedAt,
        ]);

        return $id;
    };

    it('rewrites case-insensitive duplicate names with a numeric suffix, keeps the oldest, and activates only its own feature flag', function () use ($migrationName, $migrationPath, $insertRecord, $table, $featureClass, $otherFeatureClass): void {
        isolatedMigration($migrationName, function () use ($migrationPath, $insertRecord, $table, $featureClass, $otherFeatureClass): void {
            $firstId = $insertRecord('Major Change', now()->subMinutes(3));
            $secondId = $insertRecord('major change', now()->subMinutes(2));
            $thirdId = $insertRecord('MAJOR CHANGE', now()->subMinute());
            $existingSuffixedId = $insertRecord('Major Change-2', now()->subMinutes(4));
            $uniqueId = $insertRecord('Minor Change', now()->subMinutes(5));

            $featureClass::deactivate();
            $otherFeatureClass::deactivate();

            expect($featureClass::active())->toBeFalse();

            $migrate = Artisan::call('migrate', ['--path' => $migrationPath]);
            expect($migrate)->toBe(Command::SUCCESS);

            $name = fn (string $id): string => DB::table($table)->where('id', $id)->value('name');

            expect($name($firstId))->toBe('Major Change')
                ->and($name($secondId))->toBe('major change-3')
                ->and($name($thirdId))->toBe('MAJOR CHANGE-4')
                ->and($name($existingSuffixedId))->toBe('Major Change-2')
                ->and($name($uniqueId))->toBe('Minor Change')
                ->and($featureClass::active())->toBeTrue()
                ->and($otherFeatureClass::active())->toBeFalse();
        });
    });

    it('treats backslashes in names literally when choosing a numeric suffix', function () use ($migrationName, $migrationPath, $insertRecord, $table): void {
        isolatedMigration($migrationName, function () use ($migrationPath, $insertRecord, $table): void {
            $firstId = $insertRecord('Ops\\Change', now()->subMinutes(3));
            $secondId = $insertRecord('ops\\change', now()->subMinutes(2));
            $existingSuffixedId = $insertRecord('Ops\\Change-2', now()->subMinutes(4));

            $migrate = Artisan::call('migrate', ['--path' => $migrationPath]);
            expect($migrate)->toBe(Command::SUCCESS);

            $name = fn (string $id): string => DB::table($table)->where('id', $id)->value('name');

            expect($name($firstId))->toBe('Ops\\Change')
                ->and($name($secondId))->toBe('ops\\change-3')
                ->and($name($existingSuffixedId))->toBe('Ops\\Change-2');
        });
    });

    it('keeps renamed duplicates within the 255 character column limit', function () use ($migrationName, $migrationPath, $insertRecord, $table): void {
        isolatedMigration($migrationName, function () use ($migrationPath, $insertRecord, $table): void {
            $firstId = $insertRecord(str_repeat('A', 255), now()->subMinutes(2));
            $secondId = $insertRecord(str_repeat('a', 255), now()->subMinute());

            $migrate = Artisan::call('migrate', ['--path' => $migrationPath]);
            expect($migrate)->toBe(Command::SUCCESS);

            $secondName = DB::table($table)->where('id', $secondId)->value('name');

            expect(DB::table($table)->where('id', $firstId)->value('name'))->toBe(str_repeat('A', 255))
                ->and($secondName)->toBe(str_repeat('a', 253) . '-2')
                ->and(mb_strlen($secondName))->toBe(255);
        });
    });

    it('leaves soft-deleted duplicates untouched', function () use ($migrationName, $migrationPath, $insertRecord, $table): void {
        isolatedMigration($migrationName, function () use ($migrationPath, $insertRecord, $table): void {
            $keptId = $insertRecord('Major Change', now()->subMinutes(2));
            $trashedId = $insertRecord('major change', now()->subMinute(), now());

            $migrate = Artisan::call('migrate', ['--path' => $migrationPath]);
            expect($migrate)->toBe(Command::SUCCESS);

            expect(DB::table($table)->where('id', $keptId)->value('name'))->toBe('Major Change')
                ->and(DB::table($table)->where('id', $trashedId)->value('name'))->toBe('major change');
        });
    });

    it('enforces case-insensitive uniqueness among non-trashed records at the database level', function () use ($migrationName, $migrationPath, $insertRecord): void {
        isolatedMigration($migrationName, function () use ($migrationPath, $insertRecord): void {
            $migrate = Artisan::call('migrate', ['--path' => $migrationPath]);
            expect($migrate)->toBe(Command::SUCCESS);

            $insertRecord('Major Change', now());

            expect(fn (): string => $insertRecord('MAJOR CHANGE', now()))
                ->toThrow(UniqueConstraintViolationException::class);
        });
    });

    it('allows a name freed by a soft-deleted record to be reused at the database level', function () use ($migrationName, $migrationPath, $insertRecord, $table): void {
        isolatedMigration($migrationName, function () use ($migrationPath, $insertRecord, $table): void {
            $migrate = Artisan::call('migrate', ['--path' => $migrationPath]);
            expect($migrate)->toBe(Command::SUCCESS);

            $insertRecord('Major Change', now()->subMinute(), now());
            $insertRecord('MAJOR CHANGE', now());

            expect(DB::table($table)->count())->toBe(2);
        });
    });

    it('drops the unique index and deactivates its feature flag on rollback without altering stored names', function () use ($migrationName, $migrationPath, $insertRecord, $table, $featureClass): void {
        isolatedMigration($migrationName, function () use ($migrationPath, $insertRecord, $table, $featureClass): void {
            $insertRecord('Major Change', now()->subMinutes(2));
            $secondId = $insertRecord('major change', now()->subMinute());
            $legitimateId = $insertRecord('Phase-2', now());

            $migrate = Artisan::call('migrate', ['--path' => $migrationPath]);
            expect($migrate)->toBe(Command::SUCCESS);

            $rollback = Artisan::call('migrate:rollback', ['--path' => $migrationPath]);
            expect($rollback)->toBe(Command::SUCCESS);

            expect(DB::table($table)->where('id', $secondId)->value('name'))->toBe('major change-2')
                ->and(DB::table($table)->where('id', $legitimateId)->value('name'))->toBe('Phase-2')
                ->and($featureClass::active())->toBeFalse();

            $insertRecord('Major Change', now());

            expect(DB::table($table)->where('name', 'Major Change')->count())->toBe(2);
        });
    });
};

describe('2026_11_03_100000_convert_change_request_types_name_to_citext_and_enforce_unique', function () use ($defineChangeRequestNameUniquenessTests): void {
    $defineChangeRequestNameUniquenessTests(
        '2026_11_03_100000_convert_change_request_types_name_to_citext_and_enforce_unique',
        'change_request_types',
        ['number_of_required_approvals' => 1],
        ChangeRequestTypeNameUniquenessFeature::class,
        ChangeRequestStatusNameUniquenessFeature::class,
    );
});

describe('2026_11_03_100001_convert_change_request_statuses_name_to_citext_and_enforce_unique', function () use ($defineChangeRequestNameUniquenessTests): void {
    $defineChangeRequestNameUniquenessTests(
        '2026_11_03_100001_convert_change_request_statuses_name_to_citext_and_enforce_unique',
        'change_request_statuses',
        ['classification' => 'new'],
        ChangeRequestStatusNameUniquenessFeature::class,
        ChangeRequestTypeNameUniquenessFeature::class,
    );
});

// TODO: Cleanup Task (contract-name-uniqueness): delete this describe and the tests within
describe('2026_11_04_120000_convert_contract_types_name_to_citext_and_enforce_unique', function (): void {
    $migrationName = '2026_11_04_120000_convert_contract_types_name_to_citext_and_enforce_unique';
    $migrationPath = "app-modules/contract-management/database/migrations/{$migrationName}.php";

    $insertContractType = function (string $name, CarbonInterface $createdAt, ?CarbonInterface $deletedAt = null): string {
        $id = (string) Str::uuid();

        DB::table('contract_types')->insert([
            'id' => $id,
            'name' => $name,
            'is_default' => false,
            'order' => DB::table('contract_types')->max('order') + 1,
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
            'deleted_at' => $deletedAt,
        ]);

        return $id;
    };

    it('rewrites case-insensitive duplicate names with a numeric suffix, keeps the oldest, and activates the feature flag', function () use ($migrationName, $migrationPath, $insertContractType): void {
        isolatedMigration($migrationName, function () use ($migrationPath, $insertContractType): void {
            $firstId = $insertContractType('Software License', now()->subMinutes(3));
            $secondId = $insertContractType('software license', now()->subMinutes(2));
            $thirdId = $insertContractType('SOFTWARE LICENSE', now()->subMinute());
            $existingSuffixedId = $insertContractType('Software License-2', now()->subMinutes(4));
            $uniqueId = $insertContractType('Hardware Lease', now()->subMinutes(5));

            expect(ContractTypeNameUniquenessFeature::active())->toBeFalse();

            $migrate = Artisan::call('migrate', ['--path' => $migrationPath]);
            expect($migrate)->toBe(Command::SUCCESS);

            $name = fn (string $id): string => DB::table('contract_types')->where('id', $id)->value('name');

            expect($name($firstId))->toBe('Software License')
                ->and($name($secondId))->toBe('software license-3')
                ->and($name($thirdId))->toBe('SOFTWARE LICENSE-4')
                ->and($name($existingSuffixedId))->toBe('Software License-2')
                ->and($name($uniqueId))->toBe('Hardware Lease')
                ->and(ContractTypeNameUniquenessFeature::active())->toBeTrue();
        });
    });

    it('leaves soft-deleted duplicates untouched', function () use ($migrationName, $migrationPath, $insertContractType): void {
        isolatedMigration($migrationName, function () use ($migrationPath, $insertContractType): void {
            $keptId = $insertContractType('Software License', now()->subMinutes(2));
            $trashedId = $insertContractType('software license', now()->subMinute(), now());

            $migrate = Artisan::call('migrate', ['--path' => $migrationPath]);
            expect($migrate)->toBe(Command::SUCCESS);

            expect(DB::table('contract_types')->where('id', $keptId)->value('name'))->toBe('Software License')
                ->and(DB::table('contract_types')->where('id', $trashedId)->value('name'))->toBe('software license');
        });
    });

    it('enforces case-insensitive uniqueness among non-trashed contract types at the database level', function () use ($migrationName, $migrationPath, $insertContractType): void {
        isolatedMigration($migrationName, function () use ($migrationPath, $insertContractType): void {
            $migrate = Artisan::call('migrate', ['--path' => $migrationPath]);
            expect($migrate)->toBe(Command::SUCCESS);

            $insertContractType('Software License', now());

            expect(fn (): string => $insertContractType('SOFTWARE LICENSE', now()))
                ->toThrow(UniqueConstraintViolationException::class);
        });
    });

    it('allows a name freed by a soft-deleted contract type to be reused at the database level', function () use ($migrationName, $migrationPath, $insertContractType): void {
        isolatedMigration($migrationName, function () use ($migrationPath, $insertContractType): void {
            $migrate = Artisan::call('migrate', ['--path' => $migrationPath]);
            expect($migrate)->toBe(Command::SUCCESS);

            $insertContractType('Software License', now()->subMinute(), now());

            $reusedId = $insertContractType('SOFTWARE LICENSE', now());

            expect(DB::table('contract_types')->where('id', $reusedId)->value('name'))->toBe('SOFTWARE LICENSE');
        });
    });

    it('does not rename a duplicate onto an existing name truncated for a longer suffix', function () use ($migrationName, $migrationPath, $insertContractType): void {
        isolatedMigration($migrationName, function () use ($migrationPath, $insertContractType): void {
            // The ninth rename uses "-10", which truncates the base to 252 characters and lands on the existing name.
            $ids = [];

            foreach (range(0, 9) as $index) {
                $ids[] = $insertContractType(str_repeat('A', 255 - $index) . str_repeat('a', $index), now()->subMinutes(20 - $index));
            }

            $existingId = $insertContractType(str_repeat('a', 252) . '-10', now()->subMinutes(30));

            $migrate = Artisan::call('migrate', ['--path' => $migrationPath]);
            expect($migrate)->toBe(Command::SUCCESS);

            $names = DB::table('contract_types')->pluck('name')->map(fn (string $name): string => Str::lower($name));

            expect($names->unique())->toHaveCount($names->count())
                ->and(DB::table('contract_types')->where('id', $existingId)->value('name'))->toBe(str_repeat('a', 252) . '-10')
                ->and(DB::table('contract_types')->where('id', $ids[9])->value('name'))->toBe(str_repeat('A', 246) . str_repeat('a', 6) . '-11');
        });
    });

    it('keeps repaired names and deactivates the feature flag on rollback', function () use ($migrationName, $migrationPath, $insertContractType): void {
        isolatedMigration($migrationName, function () use ($migrationPath, $insertContractType): void {
            $firstId = $insertContractType('Software License', now()->subMinutes(2));
            $secondId = $insertContractType('software license', now()->subMinute());

            $migrate = Artisan::call('migrate', ['--path' => $migrationPath]);
            expect($migrate)->toBe(Command::SUCCESS);

            expect(DB::table('contract_types')->where('id', $secondId)->value('name'))->toBe('software license-2');

            $rollback = Artisan::call('migrate:rollback', ['--path' => $migrationPath]);
            expect($rollback)->toBe(Command::SUCCESS);

            expect(DB::table('contract_types')->where('id', $firstId)->value('name'))->toBe('Software License')
                ->and(DB::table('contract_types')->where('id', $secondId)->value('name'))->toBe('software license-2')
                ->and(ContractTypeNameUniquenessFeature::active())->toBeFalse();
        });
    });
});

describe('2026_11_04_120100_convert_contracts_name_to_citext_and_enforce_unique', function (): void {
    $migrationName = '2026_11_04_120100_convert_contracts_name_to_citext_and_enforce_unique';
    $migrationPath = "app-modules/contract-management/database/migrations/{$migrationName}.php";

    $insertContract = function (string $name, CarbonInterface $createdAt, ?CarbonInterface $deletedAt = null): string {
        $id = (string) Str::uuid();

        DB::table('contracts')->insert([
            'id' => $id,
            'name' => $name,
            'contract_type_id' => ContractType::factory()->create()->getKey(),
            'vendor_name' => 'Acme',
            'start_date' => '2026-01-01',
            'end_date' => '2026-12-31',
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
            'deleted_at' => $deletedAt,
        ]);

        return $id;
    };

    it('rewrites case-insensitive duplicate names with a numeric suffix, keeps the oldest, and activates the feature flag', function () use ($migrationName, $migrationPath, $insertContract): void {
        isolatedMigration($migrationName, function () use ($migrationPath, $insertContract): void {
            $firstId = $insertContract('Master Agreement', now()->subMinutes(3));
            $secondId = $insertContract('master agreement', now()->subMinutes(2));
            $thirdId = $insertContract('MASTER AGREEMENT', now()->subMinute());
            $existingSuffixedId = $insertContract('Master Agreement-2', now()->subMinutes(4));
            $uniqueId = $insertContract('Service Agreement', now()->subMinutes(5));

            expect(ContractNameUniquenessFeature::active())->toBeFalse();

            $migrate = Artisan::call('migrate', ['--path' => $migrationPath]);
            expect($migrate)->toBe(Command::SUCCESS);

            $name = fn (string $id): string => DB::table('contracts')->where('id', $id)->value('name');

            expect($name($firstId))->toBe('Master Agreement')
                ->and($name($secondId))->toBe('master agreement-3')
                ->and($name($thirdId))->toBe('MASTER AGREEMENT-4')
                ->and($name($existingSuffixedId))->toBe('Master Agreement-2')
                ->and($name($uniqueId))->toBe('Service Agreement')
                ->and(ContractNameUniquenessFeature::active())->toBeTrue();
        });
    });

    it('leaves soft-deleted duplicates untouched', function () use ($migrationName, $migrationPath, $insertContract): void {
        isolatedMigration($migrationName, function () use ($migrationPath, $insertContract): void {
            $keptId = $insertContract('Master Agreement', now()->subMinutes(2));
            $trashedId = $insertContract('master agreement', now()->subMinute(), now());

            $migrate = Artisan::call('migrate', ['--path' => $migrationPath]);
            expect($migrate)->toBe(Command::SUCCESS);

            expect(DB::table('contracts')->where('id', $keptId)->value('name'))->toBe('Master Agreement')
                ->and(DB::table('contracts')->where('id', $trashedId)->value('name'))->toBe('master agreement');
        });
    });

    it('enforces case-insensitive uniqueness among non-trashed contracts at the database level', function () use ($migrationName, $migrationPath, $insertContract): void {
        isolatedMigration($migrationName, function () use ($migrationPath, $insertContract): void {
            $migrate = Artisan::call('migrate', ['--path' => $migrationPath]);
            expect($migrate)->toBe(Command::SUCCESS);

            $insertContract('Master Agreement', now());

            expect(fn (): string => $insertContract('MASTER AGREEMENT', now()))
                ->toThrow(UniqueConstraintViolationException::class);
        });
    });

    it('allows a name freed by a soft-deleted contract to be reused at the database level', function () use ($migrationName, $migrationPath, $insertContract): void {
        isolatedMigration($migrationName, function () use ($migrationPath, $insertContract): void {
            $migrate = Artisan::call('migrate', ['--path' => $migrationPath]);
            expect($migrate)->toBe(Command::SUCCESS);

            $insertContract('Master Agreement', now()->subMinute(), now());

            $reusedId = $insertContract('MASTER AGREEMENT', now());

            expect(DB::table('contracts')->where('id', $reusedId)->value('name'))->toBe('MASTER AGREEMENT');
        });
    });

    it('does not rename a duplicate onto an existing name truncated for a longer suffix', function () use ($migrationName, $migrationPath, $insertContract): void {
        isolatedMigration($migrationName, function () use ($migrationPath, $insertContract): void {
            // The ninth rename uses "-10", which truncates the base to 252 characters and lands on the existing name.
            $ids = [];

            foreach (range(0, 9) as $index) {
                $ids[] = $insertContract(str_repeat('A', 255 - $index) . str_repeat('a', $index), now()->subMinutes(20 - $index));
            }

            $existingId = $insertContract(str_repeat('a', 252) . '-10', now()->subMinutes(30));

            $migrate = Artisan::call('migrate', ['--path' => $migrationPath]);
            expect($migrate)->toBe(Command::SUCCESS);

            $names = DB::table('contracts')->pluck('name')->map(fn (string $name): string => Str::lower($name));

            expect($names->unique())->toHaveCount($names->count())
                ->and(DB::table('contracts')->where('id', $existingId)->value('name'))->toBe(str_repeat('a', 252) . '-10')
                ->and(DB::table('contracts')->where('id', $ids[9])->value('name'))->toBe(str_repeat('A', 246) . str_repeat('a', 6) . '-11');
        });
    });

    it('keeps repaired names and deactivates the feature flag on rollback', function () use ($migrationName, $migrationPath, $insertContract): void {
        isolatedMigration($migrationName, function () use ($migrationPath, $insertContract): void {
            $firstId = $insertContract('Master Agreement', now()->subMinutes(2));
            $secondId = $insertContract('master agreement', now()->subMinute());

            $migrate = Artisan::call('migrate', ['--path' => $migrationPath]);
            expect($migrate)->toBe(Command::SUCCESS);

            expect(DB::table('contracts')->where('id', $secondId)->value('name'))->toBe('master agreement-2');

            $rollback = Artisan::call('migrate:rollback', ['--path' => $migrationPath]);
            expect($rollback)->toBe(Command::SUCCESS);

            expect(DB::table('contracts')->where('id', $firstId)->value('name'))->toBe('Master Agreement')
                ->and(DB::table('contracts')->where('id', $secondId)->value('name'))->toBe('master agreement-2')
                ->and(ContractNameUniquenessFeature::active())->toBeFalse();
        });
    });
});

// TODO: Cleanup Task (advisory-status-and-severity-name-uniqueness): delete the helper below, the two describes that follow, and the tests within
$defineAdvisoryNameUniquenessTests = function (string $migrationName, string $table, array $extraColumns, string $featureClass, string $otherFeatureClass): void {
    assert(is_subclass_of($featureClass, AbstractFeatureFlag::class));
    assert(is_subclass_of($otherFeatureClass, AbstractFeatureFlag::class));
    $migrationPath = "app-modules/service-management/database/migrations/{$migrationName}.php";

    $insertRecord = function (string $name, CarbonInterface $createdAt, ?CarbonInterface $deletedAt = null) use ($table, $extraColumns): string {
        $id = (string) Str::uuid();

        DB::table($table)->insert([
            'id' => $id,
            'name' => $name,
            ...$extraColumns,
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
            'deleted_at' => $deletedAt,
        ]);

        return $id;
    };

    it('rewrites case-insensitive duplicate names with a numeric suffix, keeps the oldest, and activates only its own feature flag', function () use ($migrationName, $migrationPath, $insertRecord, $table, $featureClass, $otherFeatureClass): void {
        isolatedMigration($migrationName, function () use ($migrationPath, $insertRecord, $table, $featureClass, $otherFeatureClass): void {
            $firstId = $insertRecord('Investigating', now()->subMinutes(3));
            $secondId = $insertRecord('investigating', now()->subMinutes(2));
            $thirdId = $insertRecord('INVESTIGATING', now()->subMinute());
            $existingSuffixedId = $insertRecord('Investigating-2', now()->subMinutes(4));
            $uniqueId = $insertRecord('Resolved', now()->subMinutes(5));

            $featureClass::deactivate();
            $otherFeatureClass::deactivate();

            expect($featureClass::active())->toBeFalse();

            $migrate = Artisan::call('migrate', ['--path' => $migrationPath]);
            expect($migrate)->toBe(Command::SUCCESS);

            $name = fn (string $id): string => DB::table($table)->where('id', $id)->value('name');

            expect($name($firstId))->toBe('Investigating')
                ->and($name($secondId))->toBe('investigating-3')
                ->and($name($thirdId))->toBe('INVESTIGATING-4')
                ->and($name($existingSuffixedId))->toBe('Investigating-2')
                ->and($name($uniqueId))->toBe('Resolved')
                ->and($featureClass::active())->toBeTrue()
                ->and($otherFeatureClass::active())->toBeFalse();
        });
    });

    it('leaves soft-deleted duplicates untouched', function () use ($migrationName, $migrationPath, $insertRecord, $table): void {
        isolatedMigration($migrationName, function () use ($migrationPath, $insertRecord, $table): void {
            $keptId = $insertRecord('Investigating', now()->subMinutes(2));
            $trashedId = $insertRecord('investigating', now()->subMinute(), now());

            $migrate = Artisan::call('migrate', ['--path' => $migrationPath]);
            expect($migrate)->toBe(Command::SUCCESS);

            expect(DB::table($table)->where('id', $keptId)->value('name'))->toBe('Investigating')
                ->and(DB::table($table)->where('id', $trashedId)->value('name'))->toBe('investigating');
        });
    });

    it('enforces case-insensitive uniqueness among non-trashed records at the database level', function () use ($migrationName, $migrationPath, $insertRecord): void {
        isolatedMigration($migrationName, function () use ($migrationPath, $insertRecord): void {
            $migrate = Artisan::call('migrate', ['--path' => $migrationPath]);
            expect($migrate)->toBe(Command::SUCCESS);

            $insertRecord('Investigating', now());

            expect(fn (): string => $insertRecord('INVESTIGATING', now()))
                ->toThrow(UniqueConstraintViolationException::class);
        });
    });

    it('allows a name freed by a soft-deleted record to be reused at the database level', function () use ($migrationName, $migrationPath, $insertRecord, $table): void {
        isolatedMigration($migrationName, function () use ($migrationPath, $insertRecord, $table): void {
            $migrate = Artisan::call('migrate', ['--path' => $migrationPath]);
            expect($migrate)->toBe(Command::SUCCESS);

            $insertRecord('Investigating', now()->subMinute(), now());
            $insertRecord('INVESTIGATING', now());

            expect(DB::table($table)->count())->toBe(2);
        });
    });

    it('drops the unique index and deactivates its feature flag on rollback without altering stored names', function () use ($migrationName, $migrationPath, $insertRecord, $table, $featureClass): void {
        isolatedMigration($migrationName, function () use ($migrationPath, $insertRecord, $table, $featureClass): void {
            $insertRecord('Investigating', now()->subMinutes(2));
            $secondId = $insertRecord('investigating', now()->subMinute());
            $legitimateId = $insertRecord('Phase-2', now());

            $migrate = Artisan::call('migrate', ['--path' => $migrationPath]);
            expect($migrate)->toBe(Command::SUCCESS);

            $rollback = Artisan::call('migrate:rollback', ['--path' => $migrationPath]);
            expect($rollback)->toBe(Command::SUCCESS);

            expect(DB::table($table)->where('id', $secondId)->value('name'))->toBe('investigating-2')
                ->and(DB::table($table)->where('id', $legitimateId)->value('name'))->toBe('Phase-2')
                ->and($featureClass::active())->toBeFalse();

            $insertRecord('Investigating', now());

            expect(DB::table($table)->where('name', 'Investigating')->count())->toBe(2);
        });
    });
};

describe('2026_11_04_164714_convert_advisory_statuses_name_to_citext_and_enforce_unique', function () use ($defineAdvisoryNameUniquenessTests): void {
    $defineAdvisoryNameUniquenessTests(
        '2026_11_04_164714_convert_advisory_statuses_name_to_citext_and_enforce_unique',
        'advisory_statuses',
        ['classification' => 'open'],
        AdvisoryStatusNameUniquenessFeature::class,
        AdvisorySeverityNameUniquenessFeature::class,
    );
});

describe('2026_11_04_164715_convert_advisory_severities_name_to_citext_and_enforce_unique', function () use ($defineAdvisoryNameUniquenessTests): void {
    $defineAdvisoryNameUniquenessTests(
        '2026_11_04_164715_convert_advisory_severities_name_to_citext_and_enforce_unique',
        'advisory_severities',
        ['color' => 'red'],
        AdvisorySeverityNameUniquenessFeature::class,
        AdvisoryStatusNameUniquenessFeature::class,
    );
});

// TODO: Cleanup Task - Compress Change Request Permissions: delete this describe and the test within
describe('2026_11_06_233425_tmp_grant_change_request_permissions_to_users_and_roles_with_related_permissions', function (): void {
    it('grants the equivalent change request permission to users with a related permission', function (): void {
        isolatedMigration('2026_11_06_233425_tmp_grant_change_request_permissions_to_users_and_roles_with_related_permissions', function (): void {
            $permissionsFor = fn (string $prefix): array => array_map(
                fn (string $action): string => "{$prefix}.{$action}",
                ['view-any', 'create', '*.view', '*.update', '*.delete', '*.restore', '*.force-delete'],
            );

            $changeRequestPermissions = $permissionsFor('change_request');

            $statusUser = User::factory()->create();
            $statusUser->givePermissionTo($permissionsFor('change_request_status'));

            $typeUser = User::factory()->create();
            $typeUser->givePermissionTo($permissionsFor('change_request_type'));

            $systemUser = SystemUser::factory()->create();
            $systemUser->givePermissionTo($permissionsFor('change_request_type'));

            $roleUser = User::factory()->create();
            $role = Role::factory()->create();
            $relatedRolePermissions = [
                ...$permissionsFor('change_request_status'),
                ...$permissionsFor('change_request_type'),
            ];
            $role->givePermissionTo($relatedRolePermissions);
            $roleUser->assignRole($role);

            $unrelatedUser = User::factory()->create();
            $unrelatedUser->givePermissionTo('product.view-any');

            $migrate = Artisan::call('migrate', ['--path' => 'app-modules/service-management/database/migrations/2026_11_06_233425_tmp_grant_change_request_permissions_to_users_and_roles_with_related_permissions.php']);
            expect($migrate)->toBe(Command::SUCCESS);

            $directPermissionNames = fn (User|SystemUser $user): array => $user->refresh()->getDirectPermissions()->pluck('name')->all();
            $rolePermissionNames = fn (Role $role): array => $role->refresh()->permissions()->pluck('name')->all();

            expect($directPermissionNames($statusUser))->toContain(...$changeRequestPermissions)
                ->and($directPermissionNames($typeUser))->toContain(...$changeRequestPermissions)
                ->and($directPermissionNames($systemUser))->toContain(...$changeRequestPermissions)
                ->and($rolePermissionNames($role))->toContain(...$changeRequestPermissions)
                ->and($directPermissionNames($unrelatedUser))->not->toContain(...$changeRequestPermissions)
                ->and($directPermissionNames($unrelatedUser))->toBe(['product.view-any']);
        });
    });
});

// TODO: Cleanup Task (contacts-permission-consolidation): delete this describe and the test within
describe('2026_11_10_191246_tmp_grant_contacts_permissions_to_users_and_roles_with_related_permissions', function (): void {
    it('grants the equivalent contacts permissions to users and roles with a related permission', function (): void {
        isolatedMigration('2026_11_10_191246_tmp_grant_contacts_permissions_to_users_and_roles_with_related_permissions', function (): void {
            $actions = ['view-any', 'create', '*.view', '*.update', '*.delete', '*.restore', '*.force-delete'];
            $permissionsFor = fn (string $prefix): array => array_map(fn (string $action): string => "{$prefix}.{$action}", $actions);
            $contactsPermissions = $permissionsFor('contacts');

            $contactUser = User::factory()->create();
            $contactUser->givePermissionTo([...$permissionsFor('contact'), 'contact.import']);

            $fileUser = User::factory()->create();
            $fileUser->givePermissionTo($permissionsFor('engagement_file'));

            $responseUser = User::factory()->create();
            $responseUser->givePermissionTo($permissionsFor('engagement_response'));

            $timelineUser = User::factory()->create();
            $timelineUser->givePermissionTo('engagement.view-any', 'engagement.*.view');

            $sendUser = User::factory()->create();
            $sendUser->givePermissionTo('engagement.create');

            $systemUser = SystemUser::factory()->create();
            $systemUser->givePermissionTo('contact.view-any');

            $roleUser = User::factory()->create();
            $role = Role::factory()->create();
            $role->givePermissionTo('engagement.*.update');
            $roleUser->assignRole($role);

            $unrelatedUser = User::factory()->create();
            $unrelatedUser->givePermissionTo('product.view-any');

            expect(ContactPermissionConsolidationFeature::active())->toBeFalse();

            $migrate = Artisan::call('migrate', ['--path' => 'app-modules/contact/database/migrations/2026_11_10_191246_tmp_grant_contacts_permissions_to_users_and_roles_with_related_permissions.php']);
            expect($migrate)->toBe(Command::SUCCESS);

            $directPermissionNames = fn (User|SystemUser $user): array => $user->refresh()->getDirectPermissions()->pluck('name')->filter(fn (string $name): bool => str_starts_with($name, 'contacts.'))->sort()->values()->all();
            $sorted = fn (array $names): array => collect($names)->sort()->values()->all();

            expect($directPermissionNames($contactUser))->toBe($sorted([...$contactsPermissions, 'contacts.import']))
                ->and($directPermissionNames($fileUser))->toBe($sorted($contactsPermissions))
                ->and($directPermissionNames($responseUser))->toBe($sorted($contactsPermissions))
                ->and($directPermissionNames($timelineUser))->toBe($sorted(['contacts.view-any', 'contacts.*.view']))
                ->and($directPermissionNames($sendUser))->toBe($sorted(['contacts.view-any', 'contacts.*.view', 'contacts.*.update']))
                ->and($directPermissionNames($systemUser))->toBe($sorted(['contacts.view-any']))
                ->and($role->refresh()->permissions()->pluck('name')->all())->toContain('contacts.*.update')
                ->and($directPermissionNames($unrelatedUser))->toBe([])
                ->and($unrelatedUser->refresh()->getDirectPermissions()->pluck('name')->all())->toBe(['product.view-any'])
                ->and(ContactPermissionConsolidationFeature::active())->toBeTrue();
        });
    });
});

// TODO: Cleanup Task (contact-type-name-uniqueness): delete this describe and the tests within
describe('2026_10_29_153800_convert_contact_types_name_to_citext_and_enforce_unique', function (): void {
    it('rewrites case-insensitive duplicate names with a numeric suffix, keeps the oldest, and activates the feature flag', function (): void {
        isolatedMigration('2026_10_29_153800_convert_contact_types_name_to_citext_and_enforce_unique', function (): void {
            $first = ContactType::factory()->create(['name' => 'Student', 'created_at' => now()->subMinutes(3)]);
            $second = ContactType::factory()->create(['name' => 'student', 'created_at' => now()->subMinutes(2)]);
            $third = ContactType::factory()->create(['name' => 'STUDENT', 'created_at' => now()->subMinute()]);
            $existingSuffixed = ContactType::factory()->create(['name' => 'Student-2', 'created_at' => now()->subMinutes(4)]);
            $unique = ContactType::factory()->create(['name' => 'Employee', 'created_at' => now()->subMinutes(5)]);

            expect(ContactTypeNameUniquenessFeature::active())->toBeFalse();

            $migrate = Artisan::call('migrate', ['--path' => 'app-modules/contact/database/migrations/2026_10_29_153800_convert_contact_types_name_to_citext_and_enforce_unique.php']);
            expect($migrate)->toBe(Command::SUCCESS);

            expect($first->refresh()->name)->toBe('Student')
                ->and($second->refresh()->name)->toBe('student-3')
                ->and($third->refresh()->name)->toBe('STUDENT-4')
                ->and($existingSuffixed->refresh()->name)->toBe('Student-2')
                ->and($unique->refresh()->name)->toBe('Employee')
                ->and(ContactTypeNameUniquenessFeature::active())->toBeTrue();
        });
    });

    it('leaves soft-deleted duplicates untouched', function (): void {
        isolatedMigration('2026_10_29_153800_convert_contact_types_name_to_citext_and_enforce_unique', function (): void {
            $kept = ContactType::factory()->create(['name' => 'Student', 'created_at' => now()->subMinutes(2)]);
            $trashed = ContactType::factory()->create(['name' => 'student', 'created_at' => now()->subMinute()]);
            $trashed->delete();

            $migrate = Artisan::call('migrate', ['--path' => 'app-modules/contact/database/migrations/2026_10_29_153800_convert_contact_types_name_to_citext_and_enforce_unique.php']);
            expect($migrate)->toBe(Command::SUCCESS);

            expect($kept->refresh()->name)->toBe('Student')
                ->and($trashed->refresh()->name)->toBe('student');
        });
    });

    it('enforces case-insensitive uniqueness among non-trashed contact types at the database level', function (): void {
        isolatedMigration('2026_10_29_153800_convert_contact_types_name_to_citext_and_enforce_unique', function (): void {
            $migrate = Artisan::call('migrate', ['--path' => 'app-modules/contact/database/migrations/2026_10_29_153800_convert_contact_types_name_to_citext_and_enforce_unique.php']);
            expect($migrate)->toBe(Command::SUCCESS);

            ContactType::factory()->create(['name' => 'Student']);

            expect(fn () => ContactType::factory()->create(['name' => 'STUDENT']))
                ->toThrow(UniqueConstraintViolationException::class);
        });
    });

    it('keeps the repaired names on rollback and deactivates the feature flag', function (): void {
        isolatedMigration('2026_10_29_153800_convert_contact_types_name_to_citext_and_enforce_unique', function (): void {
            $first = ContactType::factory()->create(['name' => 'Student', 'created_at' => now()->subMinutes(2)]);
            $second = ContactType::factory()->create(['name' => 'student', 'created_at' => now()->subMinute()]);

            $migrate = Artisan::call('migrate', ['--path' => 'app-modules/contact/database/migrations/2026_10_29_153800_convert_contact_types_name_to_citext_and_enforce_unique.php']);
            expect($migrate)->toBe(Command::SUCCESS);

            expect($second->refresh()->name)->toBe('student-2');

            $first->delete();

            $rollback = Artisan::call('migrate:rollback', ['--path' => 'app-modules/contact/database/migrations/2026_10_29_153800_convert_contact_types_name_to_citext_and_enforce_unique.php']);
            expect($rollback)->toBe(Command::SUCCESS);

            expect($second->refresh()->name)->toBe('student-2')
                ->and(ContactTypeNameUniquenessFeature::active())->toBeFalse();
        });
    });

    it('keeps suffixed names within the column length for names at the maximum length', function (): void {
        isolatedMigration('2026_10_29_153800_convert_contact_types_name_to_citext_and_enforce_unique', function (): void {
            $first = ContactType::factory()->create(['name' => str_repeat('a', 255), 'created_at' => now()->subMinutes(2)]);
            $second = ContactType::factory()->create(['name' => str_repeat('A', 255), 'created_at' => now()->subMinute()]);

            $migrate = Artisan::call('migrate', ['--path' => 'app-modules/contact/database/migrations/2026_10_29_153800_convert_contact_types_name_to_citext_and_enforce_unique.php']);
            expect($migrate)->toBe(Command::SUCCESS);

            expect($first->refresh()->name)->toBe(str_repeat('a', 255))
                ->and($second->refresh()->name)->toBe(str_repeat('A', 253) . '-2');
        });
    });

    it('skips a truncated candidate name that is already taken', function (): void {
        isolatedMigration('2026_10_29_153800_convert_contact_types_name_to_citext_and_enforce_unique', function (): void {
            ContactType::factory()->create(['name' => str_repeat('a', 255), 'created_at' => now()->subMinutes(3)]);
            $second = ContactType::factory()->create(['name' => str_repeat('A', 255), 'created_at' => now()->subMinutes(2)]);
            ContactType::factory()->create(['name' => str_repeat('a', 253) . '-2', 'created_at' => now()->subMinute()]);

            $migrate = Artisan::call('migrate', ['--path' => 'app-modules/contact/database/migrations/2026_10_29_153800_convert_contact_types_name_to_citext_and_enforce_unique.php']);
            expect($migrate)->toBe(Command::SUCCESS);

            expect($second->refresh()->name)->toBe(str_repeat('A', 253) . '-3');
        });
    });
});

// TODO: Cleanup Task (projects-permission-consolidation): delete this describe and the test within
describe('2026_11_10_221002_tmp_grant_projects_permissions_to_users_and_roles_with_related_permissions', function (): void {
    it('grants the equivalent projects permissions to users and roles with a related permission', function (): void {
        isolatedMigration('2026_11_10_221002_tmp_grant_projects_permissions_to_users_and_roles_with_related_permissions', function (): void {
            $permissionsFor = fn (string $prefix): array => array_map(
                fn (string $action): string => "{$prefix}.{$action}",
                ['view-any', 'create', '*.view', '*.update', '*.delete', '*.restore', '*.force-delete'],
            );

            $projectsPermissions = $permissionsFor('projects');

            $projectUser = User::factory()->create();
            $projectUser->givePermissionTo($permissionsFor('project'));

            $pipelineUser = User::factory()->create();
            $pipelineUser->givePermissionTo($permissionsFor('pipeline'));

            $partialPipelineUser = User::factory()->create();
            $partialPipelineUser->givePermissionTo('pipeline.*.update');

            $systemUser = SystemUser::factory()->create();
            $systemUser->givePermissionTo('pipeline.view-any');

            $roleUser = User::factory()->create();
            $role = Role::factory()->create();
            $role->givePermissionTo('project.*.delete');
            $roleUser->assignRole($role);

            $unrelatedUser = User::factory()->create();
            $unrelatedUser->givePermissionTo('product.view-any');

            expect(ProjectPermissionConsolidationFeature::active())->toBeFalse();

            $migrate = Artisan::call('migrate', ['--path' => 'app-modules/project/database/migrations/2026_11_10_221002_tmp_grant_projects_permissions_to_users_and_roles_with_related_permissions.php']);
            expect($migrate)->toBe(Command::SUCCESS);

            $directProjectsPermissionNames = fn (User|SystemUser $user): array => $user->refresh()->getDirectPermissions()->pluck('name')->filter(fn (string $name): bool => str_starts_with($name, 'projects.'))->sort()->values()->all();
            $sorted = fn (array $names): array => collect($names)->sort()->values()->all();

            expect($directProjectsPermissionNames($projectUser))->toBe($sorted($projectsPermissions))
                ->and($directProjectsPermissionNames($pipelineUser))->toBe($sorted($projectsPermissions))
                ->and($directProjectsPermissionNames($partialPipelineUser))->toBe(['projects.*.update'])
                ->and($directProjectsPermissionNames($systemUser))->toBe(['projects.view-any'])
                ->and($role->refresh()->permissions()->pluck('name')->all())->toContain('projects.*.delete')
                ->and($roleUser->refresh()->hasPermissionTo('projects.*.delete'))->toBeTrue()
                ->and($directProjectsPermissionNames($unrelatedUser))->toBe([])
                ->and($unrelatedUser->refresh()->getDirectPermissions()->pluck('name')->all())->toBe(['product.view-any'])
                ->and(ProjectPermissionConsolidationFeature::active())->toBeTrue();
        });
    });
});

// TODO: Cleanup Task (licenses-permission-consolidation): delete this describe and the test within
describe('2026_11_11_150002_tmp_grant_licenses_permissions_to_users_and_roles_with_related_permissions', function (): void {
    it('grants the equivalent licenses permissions to users and roles with a related permission', function (): void {
        isolatedMigration('2026_11_11_150002_tmp_grant_licenses_permissions_to_users_and_roles_with_related_permissions', function (): void {
            $permissionsFor = fn (string $prefix): array => array_map(
                fn (string $action): string => "{$prefix}.{$action}",
                ['view-any', 'create', '*.view', '*.update', '*.delete', '*.restore', '*.force-delete'],
            );

            $licensesPermissions = $permissionsFor('licenses');

            $productUser = User::factory()->create();
            $productUser->givePermissionTo($permissionsFor('product'));

            $productLicenseUser = User::factory()->create();
            $productLicenseUser->givePermissionTo($permissionsFor('product_license'));

            $partialProductLicenseUser = User::factory()->create();
            $partialProductLicenseUser->givePermissionTo('product_license.*.update');

            $systemUser = SystemUser::factory()->create();
            $systemUser->givePermissionTo('product_license.view-any');

            $roleUser = User::factory()->create();
            $role = Role::factory()->create();
            $role->givePermissionTo('product.*.delete');
            $roleUser->assignRole($role);

            $apiRole = Role::factory()->create(['guard_name' => 'api']);
            $apiRole->givePermissionTo('product.view-any');

            $unrelatedUser = User::factory()->create();
            $unrelatedUser->givePermissionTo('knowledge_base_item.view-any');

            expect(LicensePermissionConsolidationFeature::active())->toBeFalse();

            $migrate = Artisan::call('migrate', ['--path' => 'app-modules/license-management/database/migrations/2026_11_11_150002_tmp_grant_licenses_permissions_to_users_and_roles_with_related_permissions.php']);
            expect($migrate)->toBe(Command::SUCCESS);

            $directLicensesPermissionNames = fn (User|SystemUser $user): array => $user->refresh()->getDirectPermissions()->pluck('name')->filter(fn (string $name): bool => str_starts_with($name, 'licenses.'))->sort()->values()->all();
            $sorted = fn (array $names): array => collect($names)->sort()->values()->all();

            expect($directLicensesPermissionNames($productUser))->toBe($sorted($licensesPermissions))
                ->and($directLicensesPermissionNames($productLicenseUser))->toBe($sorted($licensesPermissions))
                ->and($directLicensesPermissionNames($partialProductLicenseUser))->toBe(['licenses.*.update'])
                ->and($directLicensesPermissionNames($systemUser))->toBe(['licenses.view-any'])
                ->and($role->refresh()->permissions()->pluck('name')->all())->toContain('licenses.*.delete')
                ->and($roleUser->refresh()->hasPermissionTo('licenses.*.delete'))->toBeTrue()
                ->and($apiRole->refresh()->permissions()->pluck('name')->all())->toContain('licenses.view-any')
                ->and($directLicensesPermissionNames($unrelatedUser))->toBe([])
                ->and($unrelatedUser->refresh()->getDirectPermissions()->pluck('name')->all())->toBe(['knowledge_base_item.view-any'])
                ->and(LicensePermissionConsolidationFeature::active())->toBeTrue();
        });
    });
});

describe('2026_11_11_150003_purge_unneeded_license_permissions', function (): void {
    $migrationName = '2026_11_11_150003_purge_unneeded_license_permissions';
    $migrationPath = "app-modules/license-management/database/migrations/{$migrationName}.php";

    $retiredNames = fn (): array => collect(['product', 'product_license'])
        ->flatMap(fn (string $prefix): array => array_map(
            fn (string $action): string => "{$prefix}.{$action}",
            ['view-any', 'create', '*.view', '*.update', '*.delete', '*.restore', '*.force-delete'],
        ))
        ->all();

    it('deletes the Product and Product License permissions and groups without touching the Licenses group or unrelated permissions', function () use ($migrationName, $migrationPath, $retiredNames): void {
        isolatedMigration($migrationName, function () use ($migrationPath, $retiredNames): void {
            foreach (['web', 'api'] as $guard) {
                foreach ($retiredNames() as $name) {
                    assertDatabaseHas('permissions', ['name' => $name, 'guard_name' => $guard]);
                }
            }

            expect(Artisan::call('migrate', ['--path' => $migrationPath]))->toBe(Command::SUCCESS);

            foreach (['web', 'api'] as $guard) {
                foreach ($retiredNames() as $name) {
                    assertDatabaseMissing('permissions', ['name' => $name, 'guard_name' => $guard]);
                }

                assertDatabaseHas('permissions', ['name' => 'licenses.view-any', 'guard_name' => $guard]);
            }

            assertDatabaseMissing('permission_groups', ['name' => 'Product']);
            assertDatabaseMissing('permission_groups', ['name' => 'Product License']);
            assertDatabaseHas('permission_groups', ['name' => 'Licenses']);
            assertDatabaseHas('permissions', ['name' => 'knowledge_base_item.view-any', 'guard_name' => 'web']);

            expect(Artisan::call('migrate:rollback', ['--path' => $migrationPath]))->toBe(Command::SUCCESS);

            foreach (['web', 'api'] as $guard) {
                foreach ($retiredNames() as $name) {
                    expect(Permission::where('name', $name)->where('guard_name', $guard)->count())->toBe(1);
                }
            }

            assertDatabaseHas('permission_groups', ['name' => 'Product']);
            assertDatabaseHas('permission_groups', ['name' => 'Product License']);
        });
    });

    it('clears the permission registrar\'s cached permissions', function () use ($migrationName, $migrationPath): void {
        isolatedMigration($migrationName, function () use ($migrationPath): void {
            $cacheStore = app(PermissionRegistrar::class)->getCacheStore();

            $cacheStore->forever(config('permission.cache.key'), 'stale');

            expect(Artisan::call('migrate', ['--path' => $migrationPath]))->toBe(Command::SUCCESS);

            expect($cacheStore->get(config('permission.cache.key')))->toBeNull();
        });
    });
});

// TODO: Cleanup Task - Compress Service Request Permissions: delete this describe and the test within
describe('2026_11_11_042242_tmp_grant_service_request_permissions_to_users_and_roles_with_related_permissions', function (): void {
    it('grants the equivalent service request permission to users and roles with a related permission and activates the feature flag', function (): void {
        isolatedMigration('2026_11_11_042242_tmp_grant_service_request_permissions_to_users_and_roles_with_related_permissions', function (): void {
            $permissionsFor = fn (string $prefix): array => array_map(
                fn (string $action): string => "{$prefix}.{$action}",
                ['view-any', 'create', '*.view', '*.update', '*.delete', '*.restore', '*.force-delete'],
            );

            $serviceRequestPermissions = $permissionsFor('service_request');
            $updateMappedPermissions = array_values(array_diff($serviceRequestPermissions, ['service_request.create']));

            $assignmentUser = User::factory()->create();
            $assignmentUser->givePermissionTo($permissionsFor('service_request_assignment'));

            $historyUser = User::factory()->create();
            $historyUser->givePermissionTo($permissionsFor('service_request_history'));

            $priorityUser = User::factory()->create();
            $priorityUser->givePermissionTo($permissionsFor('service_request_priority'));

            $updateUser = User::factory()->create();
            $updateUser->givePermissionTo($permissionsFor('service_request_update'));

            $systemUser = SystemUser::factory()->create();
            $systemUser->givePermissionTo($permissionsFor('service_request_update'));

            $roleUser = User::factory()->create();
            $role = Role::factory()->create();
            $role->givePermissionTo([
                ...$permissionsFor('service_request_assignment'),
                ...$permissionsFor('service_request_history'),
                ...$permissionsFor('service_request_priority'),
                ...$permissionsFor('service_request_update'),
            ]);
            $roleUser->assignRole($role);

            $partialUser = User::factory()->create();
            $partialUser->givePermissionTo('service_request_priority.view-any');

            $updateCreatorUser = User::factory()->create();
            $updateCreatorUser->givePermissionTo('service_request_update.create');

            $unrelatedUser = User::factory()->create();
            $unrelatedUser->givePermissionTo('product.view-any');

            $migrate = Artisan::call('migrate', ['--path' => 'app-modules/service-management/database/migrations/2026_11_11_042242_tmp_grant_service_request_permissions_to_users_and_roles_with_related_permissions.php']);
            expect($migrate)->toBe(Command::SUCCESS);

            $directPermissionNames = fn (User|SystemUser $user): array => $user->refresh()->getDirectPermissions()->pluck('name')->all();
            $rolePermissionNames = fn (Role $role): array => $role->refresh()->permissions()->pluck('name')->all();

            expect($directPermissionNames($assignmentUser))->toContain(...$serviceRequestPermissions)
                ->and($directPermissionNames($historyUser))->toContain(...$serviceRequestPermissions)
                ->and($directPermissionNames($priorityUser))->toContain(...$serviceRequestPermissions)
                ->and($directPermissionNames($updateUser))->toContain(...$updateMappedPermissions)
                ->and($directPermissionNames($updateUser))->not->toContain('service_request.create')
                ->and($directPermissionNames($systemUser))->toContain(...$updateMappedPermissions)
                ->and($directPermissionNames($systemUser))->not->toContain('service_request.create')
                ->and($rolePermissionNames($role))->toContain(...$serviceRequestPermissions)
                ->and($directPermissionNames($partialUser))->toEqualCanonicalizing(['service_request_priority.view-any', 'service_request.view-any'])
                ->and($directPermissionNames($updateCreatorUser))->toEqualCanonicalizing(['service_request_update.create', 'service_request.*.update'])
                ->and($directPermissionNames($unrelatedUser))->toBe(['product.view-any']);
        });
    });
});
