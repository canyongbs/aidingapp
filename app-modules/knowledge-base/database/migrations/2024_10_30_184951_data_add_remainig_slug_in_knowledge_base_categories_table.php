<?php

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;
use AidingApp\KnowledgeBase\Models\KnowledgeBaseCategory;
use Illuminate\Database\UniqueConstraintViolationException;

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
                } catch (UniqueConstraintViolationException $e) {
                    DB::rollBack();
                    $attempts++;
                    $slug = $baseSlug . '-' . $attempts;
                }
            }

            if ($attempts == 15) {
                throw new Exception("Failed to generate a unique slug after 15 attempts for category ID {$category->name}\n");
            }
        }
    }
};
