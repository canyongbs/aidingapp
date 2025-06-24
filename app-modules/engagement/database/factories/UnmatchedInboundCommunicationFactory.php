<?php

namespace AidingApp\Engagement\Database\Factories;

use AidingApp\Engagement\Enums\EngagementResponseType;
use AidingApp\Engagement\Models\UnmatchedInboundCommunication;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UnmatchedInboundCommunication>
 */
class UnmatchedInboundCommunicationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type' => $this->faker->randomElement(EngagementResponseType::cases()),
            'sender' => function ($attributes) {
                /** @var EngagementResponseType $type */
                $type = $attributes['type'];

                return match ($type) {
                    EngagementResponseType::Email => $this->faker->unique()->email(),
                };
            },
            'occurred_at' => now(),
            'subject' => function ($attributes) {
                /** @var EngagementResponseType $type */
                $type = $attributes['type'];

                return match ($type) {
                    EngagementResponseType::Email => $this->faker->sentence(),
                };
            },
            'body' => $this->faker->sentence(),
        ];
    }

    public function email(): self
    {
        return $this->state(fn () => [
            'type' => EngagementResponseType::Email,
            'sender' => $this->faker->unique()->email(),
            'subject' => $this->faker->sentence(),
        ]);
    }
}
