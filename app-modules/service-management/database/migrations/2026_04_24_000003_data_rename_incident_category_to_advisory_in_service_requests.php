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
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('service_requests') && Schema::hasColumn('service_requests', 'category')) {
            DB::table('service_requests')
                ->where('category', 'incident')
                ->update(['category' => 'advisory']);
        }

        if (Schema::hasTable('service_request_types') && Schema::hasColumn('service_request_types', 'default_issue_category')) {
            DB::table('service_request_types')
                ->where('default_issue_category', 'incident')
                ->update(['default_issue_category' => 'advisory']);
        }

        if (Schema::hasTable('service_request_types') && Schema::hasColumn('service_request_types', 'default_category')) {
            DB::table('service_request_types')
                ->where('default_category', 'incident')
                ->update(['default_category' => 'advisory']);
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('service_requests') && Schema::hasColumn('service_requests', 'category')) {
            DB::table('service_requests')
                ->where('category', 'advisory')
                ->update(['category' => 'incident']);
        }

        if (Schema::hasTable('service_request_types') && Schema::hasColumn('service_request_types', 'default_issue_category')) {
            DB::table('service_request_types')
                ->where('default_issue_category', 'advisory')
                ->update(['default_issue_category' => 'incident']);
        }

        if (Schema::hasTable('service_request_types') && Schema::hasColumn('service_request_types', 'default_category')) {
            DB::table('service_request_types')
                ->where('default_category', 'advisory')
                ->update(['default_category' => 'incident']);
        }
    }
};
