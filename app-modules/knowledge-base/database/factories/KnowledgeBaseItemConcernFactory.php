<?php

namespace AidingApp\KnowledgeBase\Database\Factories;

use AidingApp\KnowledgeBase\Enums\ConcernStatus;
use AidingApp\KnowledgeBase\Models\KnowledgeBaseItem;
use AidingApp\KnowledgeBase\Models\KnowledgeBaseItemConcern;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<KnowledgeBaseItemConcern>
 */
class KnowledgeBaseItemConcernFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'description' => $this->faker->words(3, true),
            'created_by_id' => User::factory(),
            'status' => $this->faker->randomElement(ConcernStatus::cases()),
            'knowledge_base_item_id' => KnowledgeBaseItem::factory(),
        ];
    }
}
