<?php

namespace AidingApp\ServiceManagement\Tests\RequestFactories;

use Filament\Support\Colors\Color;
use Worksome\RequestFactories\RequestFactory;

class IncidentSeverityRequestFactory extends RequestFactory
{
    public function definition(): array
    {
        return [
            'name' => fake()->word(10),
            'color' => fake()->randomElement(
                collect(Color::all())->keys()
            ),
        ];
    }
}
