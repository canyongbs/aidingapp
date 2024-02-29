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

namespace AdvisingApp\Application\Database\Factories;

use AdvisingApp\Contact\Models\Contact;
use AdvisingApp\Application\Models\Application;
use AdvisingApp\StudentDataModel\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Relations\Relation;
use AdvisingApp\Application\Models\ApplicationSubmission;
use AdvisingApp\Application\Models\ApplicationSubmissionState;

/**
 * @extends Factory<ApplicationSubmission>
 */
class ApplicationSubmissionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'application_id' => Application::factory(),
            'author_type' => fake()->randomElement([(new Student())->getMorphClass(), (new Contact())->getMorphClass()]),
            'author_id' => function (array $attributes) {
                $authorClass = Relation::getMorphedModel($attributes['author_type']);

                /** @var Student|Contact $authorModel */
                $authorModel = new $authorClass();

                $author = $authorClass === Student::class
                    ? Student::inRandomOrder()->first() ?? Student::factory()->create()
                    : $authorModel::factory()->create();

                return $author->getKey();
            },
            'state_id' => ApplicationSubmissionState::factory(),
        ];
    }
}
