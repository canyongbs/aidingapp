<?php

namespace AidingApp\Ai\Database\Factories;

use AidingApp\Contact\Models\Contact;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\AidingApp\Ai\Models\PortalAssistantThread>
 */
class PortalAssistantThreadFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'author_type' => 'contact',
            'author_id' => Contact::factory(),
        ];
    }
}
