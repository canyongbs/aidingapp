<?php

use AidingApp\ServiceManagement\Enums\ServiceRequestUpdateDirection;
use AidingApp\ServiceManagement\Models\ServiceRequestUpdate;
use App\Models\Authenticatable;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
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
                            ?? $serviceRequestUpdate->serviceRequest->priority->type->managers()->first()->users()->first()
                            ?? User::role(Authenticatable::SUPER_ADMIN_ROLE)->first();
                        $serviceRequestUpdate->createdBy()->associate($user);
                        $serviceRequestUpdate->save();
                    })(),
                };
            });
    }

    public function down(): void {}
};
