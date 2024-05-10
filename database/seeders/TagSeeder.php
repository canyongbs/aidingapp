<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;
use AidingApp\KnowledgeBase\Models\KnowledgeBaseItem;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        Tag::factory()
            ->count(20)
            ->forClass(KnowledgeBaseItem::class)
            ->create();
    }
}
