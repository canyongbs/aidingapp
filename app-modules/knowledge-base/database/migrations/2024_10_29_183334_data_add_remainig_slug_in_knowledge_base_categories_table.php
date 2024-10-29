<?php

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;
use Illuminate\Database\Migrations\Migration;
use AidingApp\KnowledgeBase\Models\KnowledgeBaseCategory;

return new class () extends Migration {
    public function up(): void
    {
        $categories = KnowledgeBaseCategory::whereNull('slug')->get();

        foreach ($categories as $category) {
            $baseSlug = Str::slug($category->name);
            $slug = $baseSlug;
            $attempts = 0;

            while ($attempts < 15) {
                try {
                    DB::beginTransaction();

                    $category->slug = $slug;
                    $category->save();

                    DB::commit();

                    break;
                } catch (QueryException $e) {
                    DB::rollBack();

                    if ($e->errorInfo[1] === 7) {
                        $attempts++;
                        $slug = $baseSlug . '-' . $attempts;
                    } else {
                        throw $e;
                    }
                }
            }

            if ($attempts == 15) {
                Log::info("Failed to generate a unique slug after 15 attempts for category ID {$category->name}\n");
            }
        }
    }
};
