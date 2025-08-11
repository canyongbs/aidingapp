<?php

use App\Features\FeedbackReminderFeature;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        FeedbackReminderFeature::activate();
    }

    public function down(): void
    {
        FeedbackReminderFeature::deactivate();
    }
};
