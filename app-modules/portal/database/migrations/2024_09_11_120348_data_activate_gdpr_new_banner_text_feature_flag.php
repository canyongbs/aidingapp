<?php

use App\Enums\FeatureFlag;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        FeatureFlag::GDPRNewBannerText->activate();
    }

    public function down(): void
    {
        FeatureFlag::GDPRNewBannerText->deactivate();
    }
};
