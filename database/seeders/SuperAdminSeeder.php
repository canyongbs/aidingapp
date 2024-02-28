<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/aidingapp/blob/main/LICENSE.

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
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use AdvisingApp\Contact\Models\Contact;
use AdvisingApp\Engagement\Models\Engagement;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\Engagement\Models\EngagementResponse;
use AdvisingApp\Engagement\Models\EngagementDeliverable;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        /** Super Admin */
        $superAdmin = User::where('email', config('local_development.super_admin.email'))->first();

        // Data for super admin
        $this->seedSubscribersFor($superAdmin);
        // $this->seedEngagementsFor($superAdmin);
    }

    protected function seedSubscribersFor(User $user): void
    {
        // Student subscriptions
        Student::query()
            ->orderBy('sisid')
            ->limit(25)
            ->get()
            ->each(function (Student $student) use ($user) {
                $user->subscriptions()->create([
                    'subscribable_id' => $student->sisid,
                    'subscribable_type' => resolve(Student::class)->getMorphClass(),
                ]);
            });

        // Contact subscriptions
        Contact::query()
            ->orderBy('id')
            ->limit(25)
            ->get()
            ->each(function (Contact $contact) use ($user) {
                $user->subscriptions()->create([
                    'subscribable_id' => $contact->id,
                    'subscribable_type' => resolve(Contact::class)->getMorphClass(),
                ]);
            });
    }

    protected function seedEngagementsFor(User $user): void
    {
        // Student Engagements
        Student::query()
            ->orderBy('sisid')
            ->limit(25)
            ->get()
            ->each(function (Student $student) use ($user) {
                $numberOfEngagements = rand(1, 10);

                for ($i = 0; $i < $numberOfEngagements; $i++) {
                    Engagement::factory()
                        ->has(EngagementDeliverable::factory()->count(1)->randomizeState(['deliveryAwaiting', 'deliverySuccessful', 'deliveryFailed']), 'engagementDeliverable')
                        ->for($student, 'recipient')
                        ->create([
                            'user_id' => $user->id,
                        ]);
                }

                EngagementResponse::factory()
                    ->count(rand(1, 10))
                    ->for($student, 'sender')
                    ->create();
            });

        // Contact Engagements
        Contact::query()
            ->orderBy('id')
            ->limit(25)
            ->get()
            ->each(function (Contact $contact) use ($user) {
                $numberOfEngagements = rand(1, 10);

                for ($i = 0; $i < $numberOfEngagements; $i++) {
                    Engagement::factory()
                        ->has(EngagementDeliverable::factory()->count(1)->randomizeState(['deliveryAwaiting', 'deliverySuccessful', 'deliveryFailed']), 'engagementDeliverable')
                        ->for($contact, 'recipient')
                        ->create([
                            'user_id' => $user->id,
                        ]);
                }

                EngagementResponse::factory()
                    ->count(rand(1, 10))
                    ->for($contact, 'sender')
                    ->create();
            });
    }
}
