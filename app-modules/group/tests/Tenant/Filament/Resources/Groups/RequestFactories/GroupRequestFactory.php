<?php

namespace AidingApp\Group\Tests\Tenant\Filament\Resources\Groups\RequestFactories;

use Worksome\RequestFactories\RequestFactory;

class GroupRequestFactory extends RequestFactory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->words(2, true),
            'description' => $this->faker->optional()->sentence(),
        ];
    }
}
