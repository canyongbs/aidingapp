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

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Collection;
use AdvisingApp\StudentDataModel\Models\Program;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\StudentDataModel\Models\Enrollment;
use AdvisingApp\StudentDataModel\Models\Performance;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        /** @var Collection $students */
        $students = Student::factory(20000)
            ->make();

        $students->chunk(200)
            ->each(function ($chunk) {
                Student::insert($chunk->toArray());

                $enrollments = [];

                $chunk->each(function (Student $student) use (&$enrollments) {
                    foreach (Enrollment::factory(5)->make(['sisid' => $student->sisid])->toArray() as $enrollment) {
                        $enrollments[] = $enrollment;
                    }
                });

                Enrollment::insert($enrollments);

                $programs = [];
                $performances = [];

                $chunk->each(function (Student $student) use (&$programs, &$performances) {
                    $programs[] = Program::factory()->make(
                        [
                            'sisid' => $student->sisid,
                            'otherid' => $student->otherid,
                        ]
                    )->toArray();

                    $performances[] = Performance::factory()->make(['sisid' => $student->sisid])->toArray();
                });

                Program::insert($programs);
                Performance::insert($performances);
            });

        /** @var Collection $students */
        $students = Student::factory(80000)
            ->make();

        $chunks = $students->chunk(2000);

        $chunks->each(function ($chunk) {
            Student::insert($chunk->toArray());

            $programs = [];
            $performances = [];

            $chunk->each(function (Student $student) use (&$programs, &$performances) {
                $programs[] = Program::factory()->make(
                    [
                        'sisid' => $student->sisid,
                        'otherid' => $student->otherid,
                    ]
                )->toArray();

                $performances[] = Performance::factory()->make(['sisid' => $student->sisid])->toArray();
            });

            Program::insert($programs);
            Performance::insert($performances);
        });
    }
}
