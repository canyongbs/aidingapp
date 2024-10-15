<?php

use App\Features\FeaturedArticle;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        FeaturedArticle::activate();
    }

    public function down(): void
    {
        FeaturedArticle::deactivate();
    }
};
