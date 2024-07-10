<?php

use AidingApp\ServiceManagement\Enums\SystemServiceRequestClassification;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Laravel\Pennant\Feature as PennantFeature;

return new class extends Migration
{
    public function up(): void
    {
        $serviceRequests = DB::table('service_requests')
            ->where('status_id',)
            ->get();

        if ($serviceRequests) {
            foreach ($serviceRequests as $serviceRequest) {
                if (
                    PennantFeature::active('time_to_resolution') &&
                    $serviceRequest->status->classification === SystemServiceRequestClassification::Closed &&
                    is_null($serviceRequest->time_to_resolution)
                ) {
                    $createdTime = $serviceRequest->created_at;
                    $updatedTime = $serviceRequest->updated_at;

                    // Calculate the difference in seconds
                    $secondsDifference = ($createdTime && $updatedTime) ? $createdTime->diffInSeconds($updatedTime) : null;
                    $serviceRequest->time_to_resolution = $secondsDifference;
                }
            }
        }
    }

    public function down(): void
    {
        $serviceRequests = DB::table('service_requests')
            ->where('status_id',)
            ->get();

        foreach ($serviceRequests as $serviceRequest) {
            if (
                PennantFeature::active('time_to_resolution') &&
                $serviceRequest->status->classification === SystemServiceRequestClassification::Closed &&
                is_null($serviceRequest->time_to_resolution)
            ) {
                $serviceRequest->time_to_resolution = null;
            }
        }
    }
};
