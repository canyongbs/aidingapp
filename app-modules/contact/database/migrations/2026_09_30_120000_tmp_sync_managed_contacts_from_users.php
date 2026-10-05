<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Aiding App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Aiding App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    <https://www.canyongbs.com> or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    public function up(): void
    {
        DB::transaction(function (): void {
            DB::table('contacts')
                ->whereNotNull('user_id')
                ->whereNull('deleted_at')
                ->chunkById(100, function (Collection $contacts): void {
                    $users = DB::table('users')
                        ->whereIn('id', $contacts->pluck('user_id'))
                        ->whereNull('deleted_at')
                        ->get()
                        ->keyBy('id');

                    foreach ($contacts as $contact) {
                        $user = $users->get($contact->user_id);

                        if (is_null($user)) {
                            continue;
                        }

                        $optionalAttributes = [
                            'job_title' => $user->job_title,
                            'phone' => $user->work_number,
                            'mobile' => $user->mobile,
                            'work_number' => $user->work_number,
                            'work_extension' => is_null($user->work_extension) ? null : (string) $user->work_extension,
                            'preferred' => $user->preferred_name,
                            'address' => $user->address,
                            'address_2' => $user->address_2,
                            'city' => $user->city,
                            'state' => $user->state,
                            'postal' => $user->postal_code,
                            'employee_id' => $user->employee_id,
                            'student_id' => $user->student_id,
                            'school' => $user->school,
                            'academic_department' => $user->academic_department,
                            'program' => $user->program,
                            'country' => $user->country,
                        ];

                        DB::table('contacts')
                            ->where('id', $contact->id)
                            ->update([
                                'full_name' => trim($user->name),
                                'first_name' => trim((string) $user->first_name),
                                'last_name' => trim((string) $user->last_name),
                                'email' => $user->email,
                                ...array_map(fn (mixed $value): mixed => filled($value) ? $value : null, $optionalAttributes),
                                'updated_at' => now(),
                            ]);
                    }
                });
        });
    }

    public function down(): void
    {
        // No-op: This migration is irreversible.
    }
};
