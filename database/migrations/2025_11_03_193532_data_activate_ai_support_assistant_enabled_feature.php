<?php

use App\Features\AiSupportAssistantEnabledFeature;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        AiSupportAssistantEnabledFeature::activate();
    }

    public function down(): void
    {
        AiSupportAssistantEnabledFeature::purge();
    }
};
