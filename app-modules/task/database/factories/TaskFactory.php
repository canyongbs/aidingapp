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

namespace AdvisingApp\Task\Database\Factories;

use App\Models\User;
use AdvisingApp\Task\Models\Task;
use AdvisingApp\Task\Enums\TaskStatus;
use AdvisingApp\Contact\Models\Contact;
use AdvisingApp\StudentDataModel\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => str(fake()->words(asText: 3))->title()->toString(),
            'description' => fake()->sentence(),
            'status' => fake()->randomElement(TaskStatus::cases())->value,
            'due' => null,
            'assigned_to' => null,
            'created_by' => User::factory(),
            'concern_id' => null,
            'concern_type' => null,
        ];
    }

    public function concerningStudent(Student $student = null): self
    {
        return $this->state([
            'concern_id' => $student?->id ?? fn () => Student::inRandomOrder()->first()->sisid ?? Student::factory(),
            'concern_type' => (new Student())->getMorphClass(),
        ]);
    }

    public function concerningContact(Contact $contact = null): self
    {
        return $this->state([
            'concern_id' => $contact?->id ?? Contact::factory(),
            'concern_type' => (new Contact())->getMorphClass(),
        ]);
    }

    public function assigned(User $user = null): self
    {
        return $this->state([
            'assigned_to' => $user?->id ?? User::factory(),
        ]);
    }

    public function pastDue(): self
    {
        return $this->state([
            'due' => fake()->dateTimeBetween('-2 weeks', '-1 week'),
        ]);
    }

    public function dueLater(): self
    {
        return $this->state([
            'due' => fake()->dateTimeBetween('+1 week', '+2 weeks'),
        ]);
    }
}
