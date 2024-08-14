<?php

use App\Enums\FeatureFlag;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        FeatureFlag::GdprBannerCustomization->activate();
    }

    public function down(): void
    {
        FeatureFlag::GdprBannerCustomization->deactivate();
    }
};
