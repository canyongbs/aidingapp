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

use CanyonGBS\Common\Support\ConvertLiteralMergeTags;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    /**
     * @var array<string, string>
     */
    private array $mergeTags = [
        'recipient name' => "recipient's name",
        'contact name' => "contact's name",
        'assigned staff name' => 'assigned manager',
        'created date' => 'created date',
        'updated date' => 'updated date',
        'service request number' => 'service request number',
        'title' => 'title',
        'description' => 'description',
        'status' => 'status',
        'type' => 'type',
        'recent update' => 'recent update',
    ];

    /**
     * @var array<int, array{table: string, column: string}>
     */
    private array $targets = [
        ['table' => 'service_request_type_email_templates', 'column' => 'subject'],
        ['table' => 'service_request_type_email_templates', 'column' => 'body'],
        ['table' => 'service_request_notification_automation_email_templates', 'column' => 'subject'],
        ['table' => 'service_request_notification_automation_email_templates', 'column' => 'body'],
    ];

    public function up(): void
    {
        DB::transaction(function (): void {
            $convertLiteralMergeTags = new ConvertLiteralMergeTags();

            foreach ($this->targets as ['table' => $table, 'column' => $column]) {
                DB::table($table)
                    ->whereNotNull($column)
                    // Only rows that could possibly contain a literal merge tag. The `::text`
                    // cast is required because neither `json` nor `jsonb` supports `like`.
                    ->whereRaw("{$column}::text like ?", ['%{{%'])
                    ->select(['id', $column])
                    ->lazyById(100)
                    ->each(function (stdClass $row) use ($table, $column, $convertLiteralMergeTags): void {
                        $content = json_decode($row->{$column}, associative: true);

                        if (! is_array($content)) {
                            return;
                        }

                        $converted = $convertLiteralMergeTags($content, $this->mergeTags);

                        if ($converted === $content) {
                            return;
                        }

                        DB::table($table)
                            ->where('id', $row->id)
                            ->update([$column => json_encode($converted)]);
                    });
            }
        });
    }

    public function down(): void
    {
        // This is a data migration and cannot be reversed.
    }
};
