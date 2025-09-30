<?php

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
