<?php

namespace Database\Factories;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Tag>
 */
class TagFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(asText: true),
        ];
    }

    /**
     * @param class-string $type
     */
    public function forClass(string $type): TagFactory
    {
        return $this->state(fn (array $attributes) => [
            'type' => app($type)::getTagType(),
        ]);
    }
}
