<?php

use App\Features\ServiceRequestTypeManagerAuditor;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        ServiceRequestTypeManagerAuditor::activate();
    }

    public function down(): void
    {
        ServiceRequestTypeManagerAuditor::deactivate();
    }
};
