<?php

use AidingApp\ServiceManagement\Filament\Resources\ServiceRequestResource\Pages\Feedback;
use App\Features\FeedbackReminderFeature;
use Google\Service\Classroom\Feed;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        FeedbackReminderFeature::activate();
    }

    public function down(): void {
        FeedbackReminderFeature::deactivate();
    }
};
