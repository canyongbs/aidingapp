<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

    Aiding App™ is licensed under the Elastic License 2.0. For more details,
    see <https://github.com/canyongbs/aidingapp/blob/main/LICENSE.>

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Aiding App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    <https://www.canyongbs.com> or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AidingApp\InventoryManagement\Database\Factories;

use AidingApp\Contact\Models\Contact;
use AidingApp\InventoryManagement\Models\Asset;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Relations\Relation;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\AidingApp\InventoryManagement\Models\AssetCheckIn>
 */
class AssetCheckInFactory extends Factory
{
    public function definition(): array
    {
        $checkedInBy = User::factory()->create();

        return [
            'asset_id' => Asset::factory(),
            'checked_in_by_type' => $checkedInBy->getMorphClass(),
            'checked_in_by_id' => $checkedInBy->getKey(),
            'checked_in_from_type' => fake()->randomElement([
                (new Contact())->getMorphClass(),
            ]),
            'checked_in_from_id' => function (array $attributes) {
                $checkedInFromClass = Relation::getMorphedModel($attributes['checked_in_from_type']);

                /** @var Contact $checkedInFromModel */
                $checkedInFromModel = new $checkedInFromClass();

                $checkedInFromModel = $checkedInFromModel::factory()->create();

                return $checkedInFromModel->getKey();
            },
            'checked_in_at' => fake()->dateTimeBetween('-1 year', 'now'),
            'notes' => fake()->paragraph(),
        ];
    }
}
