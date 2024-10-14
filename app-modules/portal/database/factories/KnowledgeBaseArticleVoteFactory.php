<?php

namespace AidingApp\Portal\Database\Factories;

use AidingApp\Contact\Models\Contact;
use AidingApp\KnowledgeBase\Models\KnowledgeBaseItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\AidingApp\Portal\Models\KnowledgeBaseArticleVote>
 */
class KnowledgeBaseArticleVoteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'is_helpful'=> fake()->boolean(),
            'voter_id'=> Contact::factory(),
            'voter_type'=> Contact::class,
            'article_id'=> KnowledgeBaseItem::factory()
        ];
    }
}
