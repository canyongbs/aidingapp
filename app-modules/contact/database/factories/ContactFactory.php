<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\Contact\Database\Factories;

use App\Models\User;
use AdvisingApp\Contact\Models\Contact;
use AdvisingApp\Contact\Models\ContactSource;
use AdvisingApp\Contact\Models\ContactStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Contact>
 */
class ContactFactory extends Factory
{
    public function definition(): array
    {
        $firstName = fake()->firstName();
        $lastName = fake()->lastName();
        $address3 = fake()->optional()->words(asText: true);

        return [
            'status_id' => ContactStatus::inRandomOrder()->first() ?? ContactStatus::factory(),
            'source_id' => ContactSource::inRandomOrder()->first() ?? ContactSource::factory(),
            'first_name' => $firstName,
            'last_name' => $lastName,
            'full_name' => "{$firstName} {$lastName}",
            'preferred' => fake()->firstName(),
            'description' => fake()->paragraph(),
            'email' => fake()->unique()->email(),
            'email_2' => fake()->email(),
            'mobile' => fake()->phoneNumber(),
            'sms_opt_out' => fake()->boolean(),
            'email_bounce' => fake()->boolean(),
            'phone' => fake()->phoneNumber(),
            'address' => fake()->streetAddress(),
            'address_2' => fake()->secondaryAddress(),
            'address_3' => $address3 ? str($address3)->headline()->toString() : null,
            'city' => fake()->city(),
            'state' => fake()->stateAbbr(),
            'postal' => str(fake()->postcode())->before('-')->toString(),
            'birthdate' => fake()->date(),
            'hsgrad' => fake()->year(),
            'assigned_to_id' => User::factory(),
            'created_by_id' => User::factory(),
        ];
    }
}
