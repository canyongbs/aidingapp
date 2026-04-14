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
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    public function up(): void
    {
        $tables = [
            ['table' => 'email_templates', 'column' => 'content'],
            ['table' => 'engagements', 'column' => 'body'],
            ['table' => 'engagement_batches', 'column' => 'body'],
        ];

        foreach ($tables as $entry) {
            $rows = DB::table($entry['table'])
                ->whereNotNull($entry['column'])
                ->select(['id', $entry['column']])
                ->get();

            foreach ($rows as $row) {
                $content = json_decode($row->{$entry['column']}, true);

                if (! is_array($content)) {
                    continue;
                }

                /** @var array<string, mixed> $content */
                $changed = false;

                $content = $this->processNode($content, $changed);

                if ($changed) {
                    DB::table($entry['table'])
                        ->where('id', $row->id)
                        ->update([$entry['column'] => json_encode($content)]);
                }
            }
        }
    }

    public function down(): void
    {
        // This is a data migration and cannot be reversed
    }

    /**
     * @param array<string, mixed> $node
     *
     * @return array<string, mixed>
     */
    private function processNode(array $node, bool &$changed): array
    {
        // Process text marks: convert textStyle to textColor
        if (isset($node['marks']) && is_array($node['marks'])) {
            foreach ($node['marks'] as $markIndex => $mark) {
                if (! is_array($mark)) {
                    continue;
                }

                if (($mark['type'] ?? null) === 'textStyle' && is_array($mark['attrs'] ?? null) && isset($mark['attrs']['color'])) {
                    $node['marks'][$markIndex] = [
                        'type' => 'textColor',
                        'attrs' => [
                            'data-color' => $mark['attrs']['color'],
                        ],
                    ];
                    $changed = true;
                }
            }
        }

        // Recursively process child content
        if (isset($node['content']) && is_array($node['content'])) {
            foreach ($node['content'] as $index => $child) {
                if (! is_array($child)) {
                    continue;
                }

                /** @var array<string, mixed> $child */
                $node['content'][$index] = $this->processNode($child, $changed);
            }
        }

        return $node;
    }
};
