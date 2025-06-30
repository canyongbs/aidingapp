<?php

namespace AidingApp\Project\Tests\Tenant\Filament\Resources\ProjectResource\RequestFactory;

use Worksome\RequestFactories\RequestFactory;

class CreateProjectRequestFactory extends RequestFactory
{
    public function definition(): array
    {
        return [
            'name' => str($this->faker->unique()->words(asText: 3))->title()->toString(),
            'description' => $this->faker->paragraph(),
        ];
    }
}
