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

use App\Features\CheckboxesFormFieldFeature;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    private const OPTION_VALUE = '1';

    /**
     * Each single `checkbox` field becomes a `checkboxes` field with one option labelled with the old field label.
     * A checked response becomes that option's value; anything else becomes no selection.
     * Converted fields are marked `isLegacyCheckbox` so submissions from forms opened before this migration are normalized.
     */
    public function up(): void
    {
        DB::transaction(function (): void {
            $fieldIds = [];

            foreach (DB::table('service_request_form_fields')->where('type', 'checkbox')->lazyById() as $field) {
                $config = json_decode($field->config, associative: true);

                DB::table('service_request_form_fields')
                    ->where('id', $field->id)
                    ->update([
                        'type' => 'checkboxes',
                        'config' => json_encode($this->withOptions(is_array($config) ? $config : [], $field->label)),
                    ]);

                $fieldIds[] = $field->id;
            }

            foreach (array_chunk($fieldIds, 500) as $chunk) {
                $submissions = DB::table('service_request_form_field_submission')
                    ->whereIn('service_request_form_field_id', $chunk)
                    ->where('response', 'not like', '[%')
                    ->lazyById();

                foreach ($submissions as $submission) {
                    $isChecked = in_array(mb_strtolower(trim($submission->response)), ['1', 'true', 'on', 'yes'], true);

                    DB::table('service_request_form_field_submission')
                        ->where('id', $submission->id)
                        ->update(['response' => json_encode($isChecked ? [self::OPTION_VALUE] : [])]);
                }
            }

            foreach (['service_request_forms', 'service_request_form_steps'] as $table) {
                $rows = DB::table($table)
                    ->whereRaw("content::text ~ '\"id\"\\s*:\\s*\"checkbox\"'")
                    ->lazyById();

                foreach ($rows as $row) {
                    $content = json_decode($row->content, associative: true);

                    if (! is_array($content)) {
                        continue;
                    }

                    DB::table($table)
                        ->where('id', $row->id)
                        ->update(['content' => json_encode($this->convertNode($content))]);
                }
            }

            CheckboxesFormFieldFeature::activate();
        });
    }

    public function down(): void
    {
        DB::transaction(function (): void {
            CheckboxesFormFieldFeature::deactivate();

            // The converted fields are indistinguishable from `checkboxes` fields created later, so they are kept
        });
    }

    /**
     * @param array<mixed> $node
     *
     * @return array<mixed>
     */
    private function convertNode(array $node): array
    {
        if (($node['type'] ?? null) === 'customBlock' && ($node['attrs']['id'] ?? null) === 'checkbox') {
            $node['attrs']['id'] = 'checkboxes';

            $config = is_array($node['attrs']['config'] ?? null) ? $node['attrs']['config'] : [];
            $node['attrs']['config'] = $this->withOptions($config, (string) ($config['label'] ?? ''));
        }

        foreach ($node as $key => $value) {
            if (is_array($value)) {
                $node[$key] = $this->convertNode($value);
            }
        }

        return $node;
    }

    /**
     * @param array<mixed> $config
     *
     * @return array<mixed>
     */
    private function withOptions(array $config, string $label): array
    {
        return [
            ...$config,
            'hasOtherOption' => false,
            'options' => [['label' => $label, 'value' => self::OPTION_VALUE]],
            'isLegacyCheckbox' => true,
        ];
    }
};
