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
      of the licensor in the software. Any use of the licensor's trademarks is subject
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
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    public function up(): void
    {
        DB::transaction(function () {
            DB::statement("
                UPDATE users
                SET name = COALESCE(SPLIT_PART(email, '@', 1), CAST(id AS TEXT))
                WHERE name IS NULL
            ");

            DB::statement("
                WITH duplicates AS (
                    SELECT
                        id,
                        email,
                        ROW_NUMBER() OVER (
                            PARTITION BY LOWER(email)
                            ORDER BY (CASE WHEN deleted_at IS NULL THEN 0 ELSE 1 END) ASC, created_at ASC
                        ) AS rn
                    FROM users
                    WHERE email IS NOT NULL
                ),
                to_update AS (
                    SELECT id, email, (rn - 1) AS suffix
                    FROM duplicates
                    WHERE rn > 1
                )
                UPDATE users
                SET email = CONCAT(
                    SPLIT_PART(to_update.email, '@', 1),
                    '+',
                    to_update.suffix,
                    '@',
                    SPLIT_PART(to_update.email, '@', 2)
                )
                FROM to_update
                WHERE users.id = to_update.id
            ");

            DB::statement("
                UPDATE users
                SET email = CONCAT(CAST(id AS TEXT), '@placeholder.invalid')
                WHERE email IS NULL
            ");

            DB::statement('DROP INDEX IF EXISTS users_email_unique');

            DB::statement('ALTER TABLE users ALTER COLUMN name SET NOT NULL');
            DB::statement('ALTER TABLE users ALTER COLUMN email SET NOT NULL');

            DB::statement('CREATE UNIQUE INDEX users_email_unique ON users (email)');
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            DB::statement('DROP INDEX IF EXISTS users_email_unique');

            DB::statement('ALTER TABLE users ALTER COLUMN name DROP NOT NULL');
            DB::statement('ALTER TABLE users ALTER COLUMN email DROP NOT NULL');

            DB::statement('CREATE UNIQUE INDEX users_email_unique ON users (email) WHERE deleted_at IS NULL');
        });
    }
};
