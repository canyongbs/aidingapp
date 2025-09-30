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

use AidingApp\Contact\Models\Contact;
use AidingApp\ServiceManagement\Enums\ServiceRequestUpdateDirection;
use AidingApp\ServiceManagement\Models\ServiceRequestUpdate;
use App\Features\ServiceRequestUpdateCreatedByFeature;
use App\Models\Authenticatable;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        DB::transaction(function () {
            Schema::table('service_request_updates', function (Blueprint $table) {
                $table->nullableUuidMorphs('created_by');
            });

            // Backfill existing records SHOULD BE REMOVED DURING CLEANUP OF ServiceRequestUpdateCreatedByFeature
            ServiceRequestUpdate::query()
                ->eachById(function (ServiceRequestUpdate $serviceRequestUpdate) {
                    match ($serviceRequestUpdate->direction) {
                        ServiceRequestUpdateDirection::Inbound => (function () use ($serviceRequestUpdate) {
                            $contact = $serviceRequestUpdate->serviceRequest?->respondent;
                            $serviceRequestUpdate->createdBy()->associate($contact);
                            $serviceRequestUpdate->save();
                        })(),
                        ServiceRequestUpdateDirection::Outbound => (function () use ($serviceRequestUpdate) {
                            $user = $serviceRequestUpdate->serviceRequest->assignedTo->user
                                // @phpstan-ignore method.notFound
                                ?? $serviceRequestUpdate->serviceRequest->priority->type->managers()->first()?->users()->first()
                                ?? User::role(Authenticatable::SUPER_ADMIN_ROLE)->first();
                            $serviceRequestUpdate->createdBy()->associate($user);
                            $serviceRequestUpdate->save();
                        })(),
                    };
                });

            Schema::table('service_request_updates', function (Blueprint $table) {
                $table->string('created_by_type')->nullable(false)->change();
                $table->uuid('created_by_id')->nullable(false)->change();
                $table->dropColumn('direction');
            });

            ServiceRequestUpdateCreatedByFeature::activate();
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            Schema::table('service_request_updates', function (Blueprint $table) {
                // Can be set back to non-nullable after cleanup of ServiceRequestUpdateCreatedByFeature
                $table->string('direction')->nullable();
            });

            // Revert backfill existing records SHOULD BE REMOVED DURING CLEANUP OF ServiceRequestUpdateCreatedByFeature
            ServiceRequestUpdate::query()
                ->eachById(function (ServiceRequestUpdate $serviceRequestUpdate) {
                    match ($serviceRequestUpdate->createdBy::class) {
                        Contact::class => (function () use ($serviceRequestUpdate) {
                            $serviceRequestUpdate->direction = ServiceRequestUpdateDirection::Inbound;
                            $serviceRequestUpdate->save();
                        })(),
                        User::class => (function () use ($serviceRequestUpdate) {
                            $serviceRequestUpdate->direction = ServiceRequestUpdateDirection::Outbound;
                            $serviceRequestUpdate->save();
                        })(),
                        default => throw new Exception('Unknown created_by type'),
                    };
                });

            ServiceRequestUpdateCreatedByFeature::deactivate();

            Schema::table('service_request_updates', function (Blueprint $table) {
                // direction change can be removed here after cleanup of ServiceRequestUpdateCreatedByFeature
                $table->string('direction')->nullable(false)->change();
                $table->dropMorphs('created_by');
            });
        });
    }
};
