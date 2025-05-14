<?php

use App\Features\MakeContactNotPolymorphicFeature;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        MakeContactNotPolymorphicFeature::activate();
    }

    public function down(): void
    {
        MakeContactNotPolymorphicFeature::deactivate();
    }
};
