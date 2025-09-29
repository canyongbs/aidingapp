<?php

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

            // Backfill existing records SHOULD BE REMOVED DURING CLEANUP
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

            ServiceRequestUpdateCreatedByFeature::activate();
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            ServiceRequestUpdateCreatedByFeature::deactivate();

            Schema::table('service_request_updates', function (Blueprint $table) {
                $table->dropMorphs('created_by');
            });
        });
    }
};
