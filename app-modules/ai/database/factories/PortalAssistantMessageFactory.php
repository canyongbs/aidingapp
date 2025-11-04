<?php

namespace AidingApp\Ai\Database\Factories;

use AidingApp\Ai\Models\PortalAssistantThread;
use AidingApp\Contact\Models\Contact;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\AidingApp\Ai\Models\PortalAssistantMessage>
 */
class PortalAssistantMessageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'content' => $this->faker->sentence(12),
            'thread_id' => PortalAssistantThread::factory(),
            'author_type' => 'contact',
            'author_id' => Contact::factory(),
            'is_advisor' => $this->faker->boolean(),
        ];
    }
}
