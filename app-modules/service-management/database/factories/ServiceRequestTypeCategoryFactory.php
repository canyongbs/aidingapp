<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

</COPYRIGHT>
*/

namespace AidingApp\ServiceManagement\Database\Factories;

use AidingApp\ServiceManagement\Models\ServiceRequestTypeCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ServiceRequestTypeCategory>
 */
class ServiceRequestTypeCategoryFactory extends Factory
{
    protected $model = ServiceRequestTypeCategory::class;

    public function definition(): array
    {
        return [
            'name' => str($this->faker->word())->ucfirst()->toString(),
            'sort' => $this->faker->randomNumber(1),
            'parent_id' => null,
        ];
    }

    public function withParent(?string $parentId): self
    {
        return $this->state(function (array $attributes) use ($parentId) {
            return ['parent_id' => $parentId];
        });
    }
}
